<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CouponAnalyticsService
{
    public function __construct(
        protected AdminExcelExportService $excelExportService
    ) {}

    /**
     * Get 8 core KPI cards for Coupons with previous period comparison.
     */
    public function getKpis(array $filters = []): array
    {
        $now = Carbon::now();
        $dateRange = $this->resolveDateRange($filters);
        $start = $dateRange['start'];
        $end = $dateRange['end'];
        $prevStart = $dateRange['prev_start'];
        $prevEnd = $dateRange['prev_end'];

        // 1. Active Coupons
        $activeCoupons = Coupon::where('is_active', true)
            ->where('status', 'active')
            ->where(function ($q) use ($now) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->count();

        $prevActiveCoupons = Coupon::where('created_at', '<=', $prevEnd)
            ->where('is_active', true)
            ->count();

        // 2. Scheduled Coupons
        $scheduledCoupons = Coupon::where('starts_at', '>', $now)->count();
        $prevScheduledCoupons = Coupon::where('starts_at', '>', $prevEnd)->count();

        // 3. Expired Coupons
        $expiredCoupons = Coupon::where(function ($q) use ($now) {
            $q->where('expires_at', '<', $now)
                ->orWhere('status', 'expired');
        })->count();
        $prevExpiredCoupons = Coupon::where('expires_at', '<', $prevEnd)->count();

        // 4. Total Redemptions
        $currentUsages = CouponUsage::whereBetween('used_at', [$start, $end])->count();
        $prevUsages = CouponUsage::whereBetween('used_at', [$prevStart, $prevEnd])->count();

        // 5. Revenue Generated from Coupon Orders
        $currentRevenue = (float) Order::whereNotNull('coupon_id')
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$start, $end])
            ->sum('grand_total');

        $prevRevenue = (float) Order::whereNotNull('coupon_id')
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$prevStart, $prevEnd])
            ->sum('grand_total');

        // 6. Total Discount Given
        $currentDiscount = (float) CouponUsage::whereBetween('used_at', [$start, $end])->sum('discount_amount');
        $prevDiscount = (float) CouponUsage::whereBetween('used_at', [$prevStart, $prevEnd])->sum('discount_amount');

        // 7. Average Discount
        $currentAvgDiscount = $currentUsages > 0 ? round($currentDiscount / $currentUsages, 2) : 0.0;
        $prevAvgDiscount = $prevUsages > 0 ? round($prevDiscount / $prevUsages, 2) : 0.0;

        // 8. Conversion Rate
        // Percentage of paid orders that used a promotional code
        $totalOrdersInPeriod = Order::whereBetween('created_at', [$start, $end])->count();
        $prevTotalOrdersInPeriod = Order::whereBetween('created_at', [$prevStart, $prevEnd])->count();

        $conversionRate = $totalOrdersInPeriod > 0 ? round(($currentUsages / $totalOrdersInPeriod) * 100, 1) : 0.0;
        $prevConversionRate = $prevTotalOrdersInPeriod > 0 ? round(($prevUsages / $prevTotalOrdersInPeriod) * 100, 1) : 0.0;

        return [
            'active_coupons' => [
                'label' => 'Active Coupons',
                'value' => number_format($activeCoupons),
                'prev_value' => number_format($prevActiveCoupons),
                'change_pct' => $this->calcChangePct($activeCoupons, $prevActiveCoupons),
                'trend' => $this->resolveTrend($activeCoupons, $prevActiveCoupons),
                'is_positive' => $activeCoupons >= $prevActiveCoupons,
                'subtext' => 'Live promotions ready for checkout',
            ],
            'scheduled_coupons' => [
                'label' => 'Scheduled Coupons',
                'value' => number_format($scheduledCoupons),
                'prev_value' => number_format($prevScheduledCoupons),
                'change_pct' => $this->calcChangePct($scheduledCoupons, $prevScheduledCoupons),
                'trend' => $this->resolveTrend($scheduledCoupons, $prevScheduledCoupons),
                'is_positive' => true,
                'subtext' => 'Upcoming scheduled launches',
            ],
            'expired_coupons' => [
                'label' => 'Expired Coupons',
                'value' => number_format($expiredCoupons),
                'prev_value' => number_format($prevExpiredCoupons),
                'change_pct' => $this->calcChangePct($expiredCoupons, $prevExpiredCoupons),
                'trend' => $this->resolveTrend($expiredCoupons, $prevExpiredCoupons),
                'is_positive' => $expiredCoupons <= $prevExpiredCoupons,
                'subtext' => 'Past end date or limit reached',
            ],
            'total_redemptions' => [
                'label' => 'Total Redemptions',
                'value' => number_format($currentUsages),
                'prev_value' => number_format($prevUsages),
                'change_pct' => $this->calcChangePct($currentUsages, $prevUsages),
                'trend' => $this->resolveTrend($currentUsages, $prevUsages),
                'is_positive' => $currentUsages >= $prevUsages,
                'subtext' => 'Times coupon codes were applied',
            ],
            'revenue_generated' => [
                'label' => 'Revenue Generated',
                'value' => '₹' . number_format($currentRevenue, 2),
                'prev_value' => '₹' . number_format($prevRevenue, 2),
                'change_pct' => $this->calcChangePct($currentRevenue, $prevRevenue),
                'trend' => $this->resolveTrend($currentRevenue, $prevRevenue),
                'is_positive' => $currentRevenue >= $prevRevenue,
                'subtext' => 'Sales unlocked via promo codes',
            ],
            'average_discount' => [
                'label' => 'Average Discount',
                'value' => '₹' . number_format($currentAvgDiscount, 2),
                'prev_value' => '₹' . number_format($prevAvgDiscount, 2),
                'change_pct' => $this->calcChangePct($currentAvgDiscount, $prevAvgDiscount),
                'trend' => $this->resolveTrend($currentAvgDiscount, $prevAvgDiscount),
                'is_positive' => true,
                'subtext' => 'Mean savings per redemption',
            ],
            'conversion_rate' => [
                'label' => 'Conversion Rate',
                'value' => number_format($conversionRate, 1) . '%',
                'prev_value' => number_format($prevConversionRate, 1) . '%',
                'change_pct' => $this->calcChangePct($conversionRate, $prevConversionRate),
                'trend' => $this->resolveTrend($conversionRate, $prevConversionRate),
                'is_positive' => $conversionRate >= $prevConversionRate,
                'subtext' => 'Share of total orders with coupons',
            ],
            'total_discount_given' => [
                'label' => 'Total Discount Given',
                'value' => '₹' . number_format($currentDiscount, 2),
                'prev_value' => '₹' . number_format($prevDiscount, 2),
                'change_pct' => $this->calcChangePct($currentDiscount, $prevDiscount),
                'trend' => $this->resolveTrend($currentDiscount, $prevDiscount),
                'is_positive' => true,
                'subtext' => 'Cumulative marketing subsidy',
            ],
        ];
    }

    /**
     * Build the 4 Analytics Charts for the Coupons module.
     */
    public function getAnalyticsCharts(array $filters = []): array
    {
        $dateRange = $this->resolveDateRange($filters);
        $start = $dateRange['start'];
        $end = $dateRange['end'];

        // Graph 1: Coupon Usage Trend (Line Chart - Daily, Weekly, Monthly)
        $graph1 = $this->buildUsageTrend($start, $end);

        // Graph 2: Coupon Type Distribution (Donut Chart)
        $graph2 = $this->buildTypeDistribution();

        // Graph 3: Revenue Generated by Coupon (Horizontal Bar Chart)
        $graph3 = $this->buildRevenueByCoupon($start, $end);

        // Graph 4: Discount Cost vs Revenue (Dual Line Chart: Revenue, Discount Cost, Net Revenue)
        $graph4 = $this->buildCostVsRevenue($start, $end);

        return [
            'usage_trend' => $graph1,
            'type_distribution' => $graph2,
            'revenue_by_coupon' => $graph3,
            'cost_vs_revenue' => $graph4,
        ];
    }

    /**
     * Graph 1: Coupon Usage Trend (Daily, Weekly, Monthly series).
     */
    protected function buildUsageTrend(Carbon $start, Carbon $end): array
    {
        $days = $start->diffInDays($end) + 1;
        $interval = $days <= 31 ? 'daily' : ($days <= 120 ? 'weekly' : 'monthly');

        $dateFormat = match ($interval) {
            'weekly' => '%Y-%u',
            'monthly' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        $usages = DB::table('coupon_usages')
            ->whereBetween('used_at', [$start, $end])
            ->select(
                DB::raw("DATE_FORMAT(used_at, '{$dateFormat}') as date_key"),
                DB::raw('COUNT(id) as count')
            )
            ->groupBy('date_key')
            ->pluck('count', 'date_key')
            ->toArray();

        $labels = [];
        $data = [];

        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $key = match ($interval) {
                'weekly' => $cursor->format('Y-W'),
                'monthly' => $cursor->format('Y-m'),
                default => $cursor->format('Y-m-d'),
            };

            $label = match ($interval) {
                'weekly' => 'Wk ' . $cursor->format('W, M'),
                'monthly' => $cursor->format('M Y'),
                default => $cursor->format('d M'),
            };

            $labels[] = $label;
            $data[] = (int) ($usages[$key] ?? 0);

            if ($interval === 'weekly') {
                $cursor->addWeek();
            } elseif ($interval === 'monthly') {
                $cursor->addMonth();
            } else {
                $cursor->addDay();
            }
        }

        return [
            'labels' => $labels,
            'interval' => $interval,
            'series' => [
                [
                    'name' => 'Redemptions',
                    'data' => $data,
                ],
            ],
            'total' => array_sum($data),
        ];
    }

    /**
     * Graph 2: Coupon Type Distribution (Donut Chart).
     */
    protected function buildTypeDistribution(): array
    {
        $distribution = Coupon::select('discount_type', DB::raw('COUNT(id) as count'))
            ->groupBy('discount_type')
            ->orderByDesc('count')
            ->get();

        $labels = [];
        $series = [];

        $typeNames = [
            'percentage' => 'Percentage Discount',
            'fixed' => 'Fixed Amount',
            'free_shipping' => 'Free Shipping',
            'bogo' => 'Buy One Get One',
            'bundle' => 'Bundle Offer',
            'first_order' => 'First Order Discount',
            'referral' => 'Referral Promotion',
        ];

        foreach ($distribution as $row) {
            $labels[] = $typeNames[$row->discount_type] ?? ucfirst(str_replace('_', ' ', $row->discount_type));
            $series[] = (int) $row->count;
        }

        if (empty($series)) {
            $labels = ['Percentage', 'Fixed Amount', 'Free Shipping'];
            $series = [0, 0, 0];
        }

        return [
            'labels' => $labels,
            'series' => $series,
            'total' => array_sum($series),
        ];
    }

    /**
     * Graph 3: Revenue Generated by Coupon (Horizontal Bar Chart).
     */
    protected function buildRevenueByCoupon(Carbon $start, Carbon $end, int $limit = 8): array
    {
        $coupons = Coupon::query()
            ->leftJoin('orders', function ($join) use ($start, $end) {
                $join->on('coupons.id', '=', 'orders.coupon_id')
                    ->where('orders.payment_status', '=', 'paid')
                    ->whereBetween('orders.created_at', [$start, $end]);
            })
            ->select(
                'coupons.id',
                'coupons.code',
                'coupons.name',
                DB::raw('COALESCE(SUM(orders.grand_total), 0) as total_revenue'),
                DB::raw('COUNT(orders.id) as orders_count')
            )
            ->groupBy('coupons.id', 'coupons.code', 'coupons.name')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get();

        $categories = [];
        $seriesData = [];

        foreach ($coupons as $c) {
            $categories[] = $c->code;
            $seriesData[] = round((float) $c->total_revenue, 2);
        }

        return [
            'categories' => $categories,
            'series' => [
                [
                    'name' => 'Revenue (₹)',
                    'data' => $seriesData,
                ],
            ],
            'items' => $coupons->map(fn ($c) => [
                'id' => $c->id,
                'code' => $c->code,
                'name' => $c->name,
                'revenue' => (float) $c->total_revenue,
                'orders_count' => (int) $c->orders_count,
            ])->toArray(),
        ];
    }

    /**
     * Graph 4: Discount Cost vs Revenue (Dual Line / Area Chart).
     */
    protected function buildCostVsRevenue(Carbon $start, Carbon $end): array
    {
        $days = $start->diffInDays($end) + 1;
        $interval = $days <= 31 ? 'daily' : ($days <= 120 ? 'weekly' : 'monthly');

        $dateFormat = match ($interval) {
            'weekly' => '%Y-%u',
            'monthly' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        // Revenue by date
        $revData = DB::table('orders')
            ->whereNotNull('coupon_id')
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$start, $end])
            ->select(
                DB::raw("DATE_FORMAT(created_at, '{$dateFormat}') as date_key"),
                DB::raw('SUM(grand_total) as rev')
            )
            ->groupBy('date_key')
            ->pluck('rev', 'date_key')
            ->toArray();

        // Discount cost by date
        $discData = DB::table('coupon_usages')
            ->whereBetween('used_at', [$start, $end])
            ->select(
                DB::raw("DATE_FORMAT(used_at, '{$dateFormat}') as date_key"),
                DB::raw('SUM(discount_amount) as disc')
            )
            ->groupBy('date_key')
            ->pluck('disc', 'date_key')
            ->toArray();

        $labels = [];
        $revSeries = [];
        $discSeries = [];
        $netSeries = [];

        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $key = match ($interval) {
                'weekly' => $cursor->format('Y-W'),
                'monthly' => $cursor->format('Y-m'),
                default => $cursor->format('Y-m-d'),
            };

            $label = match ($interval) {
                'weekly' => 'Wk ' . $cursor->format('W, M'),
                'monthly' => $cursor->format('M Y'),
                default => $cursor->format('d M'),
            };

            $labels[] = $label;
            $r = round((float) ($revData[$key] ?? 0), 2);
            $d = round((float) ($discData[$key] ?? 0), 2);
            $n = max(0, round($r - $d, 2));

            $revSeries[] = $r;
            $discSeries[] = $d;
            $netSeries[] = $n;

            if ($interval === 'weekly') {
                $cursor->addWeek();
            } elseif ($interval === 'monthly') {
                $cursor->addMonth();
            } else {
                $cursor->addDay();
            }
        }

        return [
            'labels' => $labels,
            'series' => [
                [
                    'name' => 'Revenue',
                    'data' => $revSeries,
                ],
                [
                    'name' => 'Discount Cost',
                    'data' => $discSeries,
                ],
                [
                    'name' => 'Net Revenue',
                    'data' => $netSeries,
                ],
            ],
            'totals' => [
                'revenue' => array_sum($revSeries),
                'discount' => array_sum($discSeries),
                'net_revenue' => array_sum($netSeries),
            ],
        ];
    }

    /**
     * Filter coupons query with search and advanced filters.
     */
    public function getFilteredCouponsQuery(array $filters): Builder
    {
        $query = Coupon::query()
            ->withCount('usages')
            ->withSum(['orders' => fn ($q) => $q->where('payment_status', 'paid')], 'grand_total')
            ->withSum('usages', 'discount_amount');

        // Search (Code or Name or Description)
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('campaign_name', 'like', "%{$search}%");
            });
        }

        // Coupon Code specific
        if (!empty($filters['code'])) {
            $query->where('code', 'like', '%' . trim($filters['code']) . '%');
        }

        // Coupon Name specific
        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . trim($filters['name']) . '%');
        }

        // Status Filter
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $status = $filters['status'];
            $now = Carbon::now();

            if ($status === 'active') {
                $query->where('is_active', true)
                    ->where(fn ($q) => $q->whereNull('status')->orWhere('status', 'active'))
                    ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', $now))
                    ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now));
            } elseif ($status === 'scheduled') {
                $query->where('starts_at', '>', $now);
            } elseif ($status === 'paused') {
                $query->where(fn ($q) => $q->where('is_active', false)->orWhere('status', 'paused'));
            } elseif ($status === 'expired') {
                $query->where(fn ($q) => $q->where('expires_at', '<', $now)->orWhere('status', 'expired'));
            } elseif ($status === 'draft') {
                $query->where('status', 'draft');
            }
        }

        // Type Filter
        if (!empty($filters['discount_type']) && $filters['discount_type'] !== 'all') {
            $query->where('discount_type', $filters['discount_type']);
        }

        // Product Filter
        if (!empty($filters['product_id'])) {
            $query->whereHas('products', fn ($q) => $q->where('products.id', $filters['product_id']));
        }

        // Customer Eligibility Filter
        if (!empty($filters['customer_eligibility']) && $filters['customer_eligibility'] !== 'all') {
            $query->where('customer_eligibility', $filters['customer_eligibility']);
        }

        // Sales Channel Filter (via orders)
        if (!empty($filters['channel'])) {
            $query->whereHas('orders', fn ($q) => $q->where('source', $filters['channel']));
        }

        // Date Range Filter (created_at or active range)
        if (!empty($filters['start_date'])) {
            $query->where('created_at', '>=', Carbon::parse($filters['start_date'])->startOfDay());
        }
        if (!empty($filters['end_date'])) {
            $query->where('created_at', '<=', Carbon::parse($filters['end_date'])->endOfDay());
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDir = strtolower($filters['sort_dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        if ($sortBy === 'code') {
            $query->orderBy('code', $sortDir);
        } elseif ($sortBy === 'revenue') {
            $query->orderBy('orders_sum_grand_total', $sortDir);
        } elseif ($sortBy === 'usage') {
            $query->orderBy('usages_count', $sortDir);
        } elseif ($sortBy === 'discount') {
            $query->orderBy('discount_value', $sortDir);
        } else {
            $query->orderBy('id', $sortDir);
        }

        return $query;
    }

    /**
     * Get single coupon 360-degree analytics for Details page.
     */
    public function getCouponDetailAnalytics(Coupon $coupon): array
    {
        $usages = $coupon->usages()->with(['order', 'user'])->get();
        $orders = $coupon->orders()->where('payment_status', 'paid')->get();

        $totalRevenue = (float) $orders->sum('grand_total');
        $totalDiscount = (float) $usages->sum('discount_amount');
        $ordersCount = $orders->count();
        $aov = $ordersCount > 0 ? round($totalRevenue / $ordersCount, 2) : 0.0;
        $netRevenue = max(0, $totalRevenue - $totalDiscount);

        // ROI: Net Revenue / Total Discount Given * 100
        $roi = $totalDiscount > 0 ? round(($netRevenue / $totalDiscount) * 100, 1) : 0.0;

        // Repeat customers using this coupon
        $customerIds = $usages->pluck('user_id')->filter()->unique();
        $totalCustomers = $customerIds->count();
        $repeatCustomers = 0;

        foreach ($customerIds as $userId) {
            $custOrdersCount = Order::where('user_id', $userId)->where('payment_status', 'paid')->count();
            if ($custOrdersCount > 1) {
                $repeatCustomers++;
            }
        }

        $repeatPct = $totalCustomers > 0 ? round(($repeatCustomers / $totalCustomers) * 100, 1) : 0.0;

        // Top Products Purchased with this coupon
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->where('orders.coupon_id', $coupon->id)
            ->where('orders.payment_status', 'paid')
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as units_sold'),
                DB::raw('SUM(order_items.line_total) as revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        // Top Locations (States/Cities)
        $topLocations = DB::table('order_addresses')
            ->join('orders', 'order_addresses.order_id', '=', 'orders.id')
            ->where('orders.coupon_id', $coupon->id)
            ->where('order_addresses.type', 'shipping')
            ->select(
                DB::raw("COALESCE(order_addresses.state, 'Unknown') as state_name"),
                DB::raw('COUNT(orders.id) as orders_count'),
                DB::raw('SUM(orders.grand_total) as revenue')
            )
            ->groupBy('state_name')
            ->orderByDesc('orders_count')
            ->limit(5)
            ->get();

        // Mini daily redemptions graph for last 14 days
        $recentUsageDaily = [];
        for ($i = 13; $i >= 0; $i--) {
            $d = Carbon::now()->subDays($i)->format('Y-m-d');
            $c = $usages->filter(fn ($u) => Carbon::parse($u->used_at)->format('Y-m-d') === $d)->count();
            $recentUsageDaily[] = [
                'date' => Carbon::parse($d)->format('d M'),
                'count' => $c,
            ];
        }

        return [
            'total_revenue' => $totalRevenue,
            'formatted_revenue' => '₹' . number_format($totalRevenue, 2),
            'total_discount' => $totalDiscount,
            'formatted_discount' => '₹' . number_format($totalDiscount, 2),
            'net_revenue' => $netRevenue,
            'formatted_net_revenue' => '₹' . number_format($netRevenue, 2),
            'orders_count' => $ordersCount,
            'aov' => $aov,
            'formatted_aov' => '₹' . number_format($aov, 2),
            'roi' => $roi,
            'total_customers' => $totalCustomers,
            'repeat_customers' => $repeatCustomers,
            'repeat_pct' => $repeatPct,
            'top_products' => $topProducts,
            'top_locations' => $topLocations,
            'recent_usage_daily' => $recentUsageDaily,
        ];
    }

    /**
     * Resolve date range presets.
     */
    public function resolveDateRange(array $filters): array
    {
        $preset = $filters['date_preset'] ?? 'last_30_days';
        $now = Carbon::now();

        switch ($preset) {
            case 'today':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                $prevStart = $start->copy()->subDay();
                $prevEnd = $end->copy()->subDay();
                $label = 'Today';
                break;

            case 'yesterday':
                $start = $now->copy()->subDay()->startOfDay();
                $end = $now->copy()->subDay()->endOfDay();
                $prevStart = $start->copy()->subDay();
                $prevEnd = $end->copy()->subDay();
                $label = 'Yesterday';
                break;

            case 'last_7_days':
                $start = $now->copy()->subDays(6)->startOfDay();
                $end = $now->copy()->endOfDay();
                $prevStart = $start->copy()->subDays(7);
                $prevEnd = $start->copy()->subSecond();
                $label = 'Last 7 Days';
                break;

            case 'this_month':
                $start = $now->copy()->startOfMonth()->startOfDay();
                $end = $now->copy()->endOfDay();
                $prevStart = $start->copy()->subMonth();
                $prevEnd = $start->copy()->subSecond();
                $label = 'This Month';
                break;

            case 'last_month':
                $start = $now->copy()->subMonth()->startOfMonth()->startOfDay();
                $end = $now->copy()->subMonth()->endOfMonth()->endOfDay();
                $prevStart = $start->copy()->subMonth();
                $prevEnd = $start->copy()->subSecond();
                $label = 'Last Month';
                break;

            case 'this_year':
                $start = $now->copy()->startOfYear()->startOfDay();
                $end = $now->copy()->endOfDay();
                $prevStart = $start->copy()->subYear();
                $prevEnd = $start->copy()->subSecond();
                $label = 'This Year';
                break;

            case 'custom':
                if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
                    $start = Carbon::parse($filters['start_date'])->startOfDay();
                    $end = Carbon::parse($filters['end_date'])->endOfDay();
                } else {
                    $start = $now->copy()->subDays(29)->startOfDay();
                    $end = $now->copy()->endOfDay();
                }
                $diff = $start->diffInDays($end) + 1;
                $prevStart = $start->copy()->subDays($diff);
                $prevEnd = $start->copy()->subSecond();
                $label = 'Custom Range';
                break;

            case 'last_30_days':
            default:
                $preset = 'last_30_days';
                $start = $now->copy()->subDays(29)->startOfDay();
                $end = $now->copy()->endOfDay();
                $prevStart = $start->copy()->subDays(30);
                $prevEnd = $start->copy()->subSecond();
                $label = 'Last 30 Days';
                break;
        }

        return [
            'start' => $start,
            'end' => $end,
            'prev_start' => $prevStart,
            'prev_end' => $prevEnd,
            'label' => $label,
            'preset' => $preset,
        ];
    }

    /**
     * Populate filter dropdown options.
     */
    public function getFilterOptions(): array
    {
        return [
            'types' => [
                'percentage' => 'Percentage Discount',
                'fixed' => 'Fixed Amount',
                'free_shipping' => 'Free Shipping',
                'bogo' => 'Buy One Get One',
                'bundle' => 'Bundle Savings',
                'first_order' => 'First Order Discount',
                'referral' => 'Referral Promotion',
            ],
            'statuses' => [
                'active' => 'Active',
                'scheduled' => 'Scheduled',
                'paused' => 'Paused',
                'expired' => 'Expired',
                'draft' => 'Draft',
            ],
            'products' => Product::orderBy('name')->get(['id', 'name', 'base_sku']),
            'categories' => Category::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'channels' => Order::whereNotNull('source')->distinct()->pluck('source')->toArray() ?: ['website', 'whatsapp', 'phone', 'direct'],
            'customer_eligibilities' => [
                'all' => 'All Customers',
                'new' => 'New Customers Only',
                'returning' => 'Returning Customers',
                'vip' => 'VIP Cohort Members',
            ],
        ];
    }

    protected function calcChangePct(float $current, float $previous): float
    {
        if ($previous == 0.0) {
            return $current > 0 ? 100.0 : 0.0;
        }
        return round((($current - $previous) / abs($previous)) * 100, 1);
    }

    protected function resolveTrend(float $current, float $previous): string
    {
        if ($current > $previous) return 'up';
        if ($current < $previous) return 'down';
        return 'flat';
    }
}
