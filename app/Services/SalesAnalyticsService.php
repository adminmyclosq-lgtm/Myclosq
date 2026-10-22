<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SalesAnalyticsService
{
    public function __construct(
        protected AddressAnalyticsService $addressAnalytics,
        protected OrderLocationFilterService $locationFilter
    ) {}

    /**
     * Get complete dashboard payload for given filters.
     * Caches query results based on filter criteria with 5-minute TTL.
     */
    public function getDashboardData(array $filters = [], bool $skipCache = false): array
    {
        $cacheKey = 'sales_bi_dash_' . md5(json_encode($filters));

        if ($skipCache) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($filters) {
            $dateRange = $this->resolveDateRange($filters);
            $startDate = $dateRange['start'];
            $endDate = $dateRange['end'];
            $prevStartDate = $dateRange['prev_start'];
            $prevEndDate = $dateRange['prev_end'];
            $interval = $filters['interval'] ?? $this->autoSelectInterval($startDate, $endDate);

            // 1. Calculate Core KPIs
            $kpis = $this->calculateKpiMetrics($filters, $startDate, $endDate, $prevStartDate, $prevEndDate);

            // 2. Revenue Trend Series
            $revenueTrend = $this->buildRevenueTrend($filters, $startDate, $endDate, $prevStartDate, $prevEndDate, $interval);

            // 3. Orders Trend Series
            $ordersTrend = $this->buildOrdersTrend($filters, $startDate, $endDate, $interval);

            // 4. Sales by Channel
            $channelSales = $this->buildChannelSales($filters, $startDate, $endDate);

            // 5. Payment Method Distribution
            $paymentDistribution = $this->buildPaymentDistribution($filters, $startDate, $endDate);

            // 6. Refund Analytics
            $refundAnalytics = $this->buildRefundAnalytics($filters, $startDate, $endDate, $interval);

            // 7. Top Selling Products
            $topProducts = $this->buildTopProducts($filters, $startDate, $endDate, $prevStartDate, $prevEndDate, 10);

            // 8. Product Category Performance
            $categoryPerformance = $this->buildCategoryPerformance($filters, $startDate, $endDate);

            // 9. Location Analytics (State, City, Geo Map Points)
            $locationAnalytics = $this->buildLocationAnalytics($filters, $startDate, $endDate);

            // 10. AOV Trend
            $aovTrend = $this->buildAovTrend($filters, $startDate, $endDate, $interval);

            // 11. Operational BI Insights
            $operationalInsights = $this->generateOperationalInsights(
                $kpis,
                $topProducts,
                $locationAnalytics,
                $paymentDistribution,
                $channelSales,
                $ordersTrend
            );

            // 12. Full Detailed Sales Table Data
            $salesTable = $this->buildSalesTableData($filters, $startDate, $endDate, $prevStartDate, $prevEndDate);

            return [
                'meta' => [
                    'date_range' => $dateRange['label'],
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                    'prev_start_date' => $prevStartDate->toDateString(),
                    'prev_end_date' => $prevEndDate->toDateString(),
                    'interval' => $interval,
                    'generated_at' => now()->toIso8601String(),
                ],
                'kpis' => $kpis,
                'revenue_trend' => $revenueTrend,
                'orders_trend' => $ordersTrend,
                'channel_sales' => $channelSales,
                'payment_distribution' => $paymentDistribution,
                'refund_analytics' => $refundAnalytics,
                'top_products' => $topProducts,
                'category_performance' => $categoryPerformance,
                'location_analytics' => $locationAnalytics,
                'aov_trend' => $aovTrend,
                'operational_insights' => $operationalInsights,
                'sales_table' => $salesTable,
            ];
        });
    }

    /**
     * Resolve date ranges for primary and comparison periods.
     */
    public function resolveDateRange(array $filters): array
    {
        $preset = $filters['date_preset'] ?? 'last_30_days';
        $now = now();

        switch ($preset) {
            case 'today':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                $prevStart = $start->copy()->subDay();
                $prevEnd = $end->copy()->subDay();
                $label = 'Today (' . $start->format('d M Y') . ')';
                break;

            case 'yesterday':
                $start = $now->copy()->subDay()->startOfDay();
                $end = $now->copy()->subDay()->endOfDay();
                $prevStart = $start->copy()->subDay();
                $prevEnd = $end->copy()->subDay();
                $label = 'Yesterday (' . $start->format('d M Y') . ')';
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
                $label = 'This Month (' . $start->format('M Y') . ')';
                break;

            case 'last_month':
                $start = $now->copy()->subMonth()->startOfMonth()->startOfDay();
                $end = $now->copy()->subMonth()->endOfMonth()->endOfDay();
                $prevStart = $start->copy()->subMonth();
                $prevEnd = $start->copy()->subSecond();
                $label = 'Last Month (' . $start->format('M Y') . ')';
                break;

            case 'custom':
                if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
                    $start = Carbon::parse($filters['start_date'])->startOfDay();
                    $end = Carbon::parse($filters['end_date'])->endOfDay();
                } else {
                    $start = $now->copy()->subDays(29)->startOfDay();
                    $end = $now->copy()->endOfDay();
                }
                $daysDiff = max(1, $start->diffInDays($end) + 1);
                $prevStart = $start->copy()->subDays($daysDiff);
                $prevEnd = $start->copy()->subSecond();
                $label = $start->format('d M Y') . ' – ' . $end->format('d M Y');
                break;

            case 'last_30_days':
            default:
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
        ];
    }

    /**
     * Choose appropriate default interval grouping based on range duration.
     */
    protected function autoSelectInterval(Carbon $start, Carbon $end): string
    {
        $days = $start->diffInDays($end);
        if ($days <= 1) {
            return 'hourly';
        }
        if ($days <= 60) {
            return 'daily';
        }
        if ($days <= 180) {
            return 'weekly';
        }
        return 'monthly';
    }

    /**
     * Base query with global filters applied.
     */
    public function getFilteredOrdersQuery(array $filters, ?Carbon $startDate = null, ?Carbon $endDate = null): Builder
    {
        $query = Order::query();

        if ($startDate) {
            $query->where('orders.created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('orders.created_at', '<=', $endDate);
        }

        // Location Filters (Country, State, City)
        $hasLocation = !empty($filters['country']) || !empty($filters['state']) || !empty($filters['city']);
        if ($hasLocation) {
            $this->locationFilter->applyOrderFilters($query, [
                'country' => $filters['country'] ?? null,
                'state' => $filters['state'] ?? null,
                'city' => $filters['city'] ?? null,
            ]);
        }

        // Payment Method Filter
        if (!empty($filters['payment_method'])) {
            $method = strtolower(trim($filters['payment_method']));
            $query->whereExists(function ($sub) use ($method) {
                $sub->select(DB::raw(1))
                    ->from('payments')
                    ->whereColumn('payments.order_id', 'orders.id')
                    ->where('payments.gateway', $method);
            });
        }

        // Product Filter
        if (!empty($filters['product_id'])) {
            $productId = (int) $filters['product_id'];
            $query->whereExists(function ($sub) use ($productId) {
                $sub->select(DB::raw(1))
                    ->from('order_items')
                    ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
                    ->whereColumn('order_items.order_id', 'orders.id')
                    ->where('product_variants.product_id', $productId);
            });
        }

        // Product Category Filter
        if (!empty($filters['category_id'])) {
            $categoryId = (int) $filters['category_id'];
            $query->whereExists(function ($sub) use ($categoryId) {
                $sub->select(DB::raw(1))
                    ->from('order_items')
                    ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
                    ->join('products', 'product_variants.product_id', '=', 'products.id')
                    ->whereColumn('order_items.order_id', 'orders.id')
                    ->where('products.category_id', $categoryId);
            });
        }

        // Sales Channel Filter
        if (!empty($filters['channel'])) {
            $query->where('orders.source', trim($filters['channel']));
        }

        // Order Status Filter
        if (!empty($filters['order_status'])) {
            $status = trim($filters['order_status']);
            $query->where(function ($sub) use ($status) {
                $sub->where('orders.payment_status', $status)
                    ->orWhere('orders.fulfilment_status', $status)
                    ->orWhere('orders.shipment_status', $status);
            });
        }

        return $query;
    }

    /**
     * Calculate 8 Core Business KPIs with comparison to previous period.
     */
    public function calculateKpiMetrics(array $filters, Carbon $start, Carbon $end, Carbon $prevStart, Carbon $prevEnd): array
    {
        $currentStats = $this->aggregatePeriodMetrics($filters, $start, $end);
        $prevStats = $this->aggregatePeriodMetrics($filters, $prevStart, $prevEnd);

        $keys = [
            'gross_sales',
            'net_sales',
            'total_orders',
            'average_order_value',
            'refund_amount',
            'refund_count',
            'total_customers',
            'returning_customers',
        ];

        $kpis = [];
        foreach ($keys as $key) {
            $curr = (float) ($currentStats[$key] ?? 0);
            $prev = (float) ($prevStats[$key] ?? 0);
            $deltaPct = $this->calculatePercentageDelta($curr, $prev);
            $trend = $this->resolveTrendDirection($key, $curr, $prev);

            $kpis[$key] = [
                'current' => $curr,
                'previous' => $prev,
                'delta_percent' => $deltaPct,
                'trend' => $trend,
                'formatted' => $this->formatMetricValue($key, $curr),
                'formatted_previous' => $this->formatMetricValue($key, $prev),
            ];
        }

        return $kpis;
    }

    /**
     * Run high performance SQL aggregation for single period.
     */
    protected function aggregatePeriodMetrics(array $filters, Carbon $start, Carbon $end): array
    {
        $query = $this->getFilteredOrdersQuery($filters, $start, $end);

        $row = (clone $query)->select(
            DB::raw('COUNT(orders.id) as total_orders'),
            DB::raw('SUM(orders.grand_total) as gross_sales'),
            DB::raw("SUM(CASE WHEN orders.payment_status = 'paid' THEN orders.grand_total ELSE 0 END) as paid_revenue"),
            DB::raw("COUNT(CASE WHEN orders.payment_status = 'paid' THEN orders.id ELSE NULL END) as paid_orders_count"),
            DB::raw('SUM(COALESCE(orders.refund_amount, 0)) as refund_amount'),
            DB::raw("COUNT(CASE WHEN orders.refund_amount > 0 OR orders.refund_status IN ('partially_refunded', 'refunded') THEN orders.id ELSE NULL END) as refund_count"),
            DB::raw('COUNT(DISTINCT orders.user_id) as total_customers')
        )->first();

        $grossSales = round((float) ($row->gross_sales ?? 0), 2);
        $paidRevenue = round((float) ($row->paid_revenue ?? 0), 2);
        $refundAmount = round((float) ($row->refund_amount ?? 0), 2);
        $netSales = max(0, round($paidRevenue - $refundAmount, 2));
        $totalOrders = (int) ($row->total_orders ?? 0);
        $paidOrders = (int) ($row->paid_orders_count ?? 0);
        $aov = $paidOrders > 0 ? round($paidRevenue / $paidOrders, 2) : 0.00;
        $refundCount = (int) ($row->refund_count ?? 0);
        $totalCustomers = (int) ($row->total_customers ?? 0);

        // Returning customers: Users who ordered in this period who had >=1 order prior
        $userIds = (clone $query)->whereNotNull('orders.user_id')->distinct()->pluck('orders.user_id')->all();
        $returningCustomers = 0;
        if (!empty($userIds)) {
            $returningCustomers = DB::table('orders')
                ->whereIn('user_id', $userIds)
                ->where('created_at', '<', $start)
                ->distinct('user_id')
                ->count('user_id');
        }

        return [
            'gross_sales' => $grossSales,
            'net_sales' => $netSales,
            'total_orders' => $totalOrders,
            'average_order_value' => $aov,
            'refund_amount' => $refundAmount,
            'refund_count' => $refundCount,
            'total_customers' => $totalCustomers,
            'returning_customers' => $returningCustomers,
        ];
    }

    /**
     * Build Revenue Trend Line Chart Data (Gross, Net, Comparison).
     */
    public function buildRevenueTrend(array $filters, Carbon $start, Carbon $end, Carbon $prevStart, Carbon $prevEnd, string $interval): array
    {
        $currentBuckets = $this->queryAggregatedTimeline($filters, $start, $end, $interval);
        $prevBuckets = $this->queryAggregatedTimeline($filters, $prevStart, $prevEnd, $interval);

        $categories = [];
        $grossSeries = [];
        $netSeries = [];
        $prevNetSeries = [];

        $prevValues = array_values($prevBuckets);

        $index = 0;
        foreach ($currentBuckets as $key => $data) {
            $categories[] = $data['label'];
            $grossSeries[] = (float) $data['gross_sales'];
            $netSeries[] = (float) $data['net_sales'];
            $prevNetSeries[] = isset($prevValues[$index]) ? (float) $prevValues[$index]['net_sales'] : 0.0;
            $index++;
        }

        return [
            'categories' => $categories,
            'series' => [
                ['name' => 'Gross Sales', 'data' => $grossSeries],
                ['name' => 'Net Sales', 'data' => $netSeries],
                ['name' => 'Previous Period Net', 'data' => $prevNetSeries],
            ],
            'totals' => [
                'gross' => array_sum($grossSeries),
                'net' => array_sum($netSeries),
                'previous_net' => array_sum($prevNetSeries),
            ],
        ];
    }

    /**
     * Build Orders Trend (Daily count, benchmark average, peak day identification).
     */
    public function buildOrdersTrend(array $filters, Carbon $start, Carbon $end, string $interval): array
    {
        $buckets = $this->queryAggregatedTimeline($filters, $start, $end, $interval);

        $categories = [];
        $orderCounts = [];
        $maxCount = 0;
        $maxDate = null;

        foreach ($buckets as $key => $data) {
            $count = (int) $data['order_count'];
            $categories[] = $data['label'];
            $orderCounts[] = $count;

            if ($count > $maxCount) {
                $maxCount = $count;
                $maxDate = $data['label'];
            }
        }

        $total = array_sum($orderCounts);
        $countBuckets = max(1, count($orderCounts));
        $averageOrders = round($total / $countBuckets, 1);

        return [
            'categories' => $categories,
            'series' => [
                ['name' => 'Orders', 'data' => $orderCounts],
            ],
            'average' => $averageOrders,
            'peak_day' => [
                'count' => $maxCount,
                'date' => $maxDate ?: '—',
            ],
            'total_orders' => $total,
        ];
    }

    /**
     * Build Sales by Channel (Horizontal Bar Chart).
     */
    public function buildChannelSales(array $filters, Carbon $start, Carbon $end): array
    {
        $standardChannels = [
            'website' => 'Website Direct',
            'google' => 'Google Search / Ads',
            'facebook' => 'Facebook Ads',
            'instagram' => 'Instagram',
            'whatsapp' => 'WhatsApp Commerce',
            'referral' => 'Referral Program',
            'direct' => 'Direct Traffic',
            'agent' => 'Wellness Agent',
            'organic' => 'Organic Search',
        ];

        $query = $this->getFilteredOrdersQuery($filters, $start, $end);

        $rows = (clone $query)->select(
            DB::raw("COALESCE(NULLIF(orders.source, ''), 'website') as channel"),
            DB::raw('COUNT(orders.id) as orders_count'),
            DB::raw("SUM(CASE WHEN orders.payment_status = 'paid' THEN orders.grand_total ELSE 0 END) as revenue")
        )
        ->groupBy('channel')
        ->orderByDesc('revenue')
        ->get();

        $dataByChannel = [];
        $totalRevenue = 0;

        foreach ($rows as $row) {
            $ch = strtolower(trim($row->channel));
            $rev = round((float) $row->revenue, 2);
            $totalRevenue += $rev;
            $dataByChannel[$ch] = [
                'channel_key' => $ch,
                'label' => $standardChannels[$ch] ?? ucfirst($ch),
                'revenue' => $rev,
                'orders_count' => (int) $row->orders_count,
            ];
        }

        // Fill standard channels
        $result = [];
        foreach ($standardChannels as $key => $label) {
            $entry = $dataByChannel[$key] ?? [
                'channel_key' => $key,
                'label' => $label,
                'revenue' => 0.0,
                'orders_count' => 0,
            ];
            $entry['share_pct'] = $totalRevenue > 0 ? round(($entry['revenue'] / $totalRevenue) * 100, 1) : 0.0;
            $result[] = $entry;
        }

        // Sort descending by revenue
        usort($result, fn($a, $b) => $b['revenue'] <=> $a['revenue']);

        $categories = array_column($result, 'label');
        $revenues = array_column($result, 'revenue');

        return [
            'categories' => $categories,
            'series' => [
                ['name' => 'Revenue (₹)', 'data' => $revenues],
            ],
            'items' => $result,
            'has_data' => $totalRevenue > 0,
        ];
    }

    /**
     * Build Payment Method Distribution (Donut Chart).
     */
    public function buildPaymentDistribution(array $filters, Carbon $start, Carbon $end): array
    {
        $gatewayNames = [
            'razorpay' => 'Razorpay (Cards / Netbanking)',
            'cash_on_delivery' => 'Cash on Delivery (COD)',
            'upi' => 'UPI Instant Pay',
            'cards' => 'Credit / Debit Cards',
            'bank_transfer' => 'Bank Transfer / NEFT',
            'wallet' => 'Digital Wallets',
            'manual' => 'Manual / Internal',
        ];

        $query = $this->getFilteredOrdersQuery($filters, $start, $end);

        $results = DB::table('payments')
            ->join('orders', 'payments.order_id', '=', 'orders.id')
            ->whereIn('orders.id', (clone $query)->select('orders.id'))
            ->select(
                DB::raw("COALESCE(NULLIF(payments.gateway, ''), 'cash_on_delivery') as gateway"),
                DB::raw('COUNT(payments.id) as count'),
                DB::raw('SUM(payments.amount) as total')
            )
            ->groupBy('gateway')
            ->orderByDesc('total')
            ->get();

        $labels = [];
        $series = [];
        $details = [];
        $totalAmount = (float) $results->sum('total');

        foreach ($results as $row) {
            $key = strtolower(trim($row->gateway));
            $label = $gatewayNames[$key] ?? ucwords(str_replace('_', ' ', $key));
            $amt = round((float) $row->total, 2);
            $count = (int) $row->count;

            $labels[] = $label;
            $series[] = $amt;
            $details[] = [
                'gateway' => $key,
                'label' => $label,
                'amount' => $amt,
                'count' => $count,
                'share_pct' => $totalAmount > 0 ? round(($amt / $totalAmount) * 100, 1) : 0,
            ];
        }

        // Graceful fallback if no payment records
        if (empty($labels)) {
            $labels = ['Cash on Delivery', 'Razorpay'];
            $series = [0, 0];
        }

        return [
            'labels' => $labels,
            'series' => $series,
            'details' => $details,
            'total_amount' => $totalAmount,
        ];
    }

    /**
     * Build Refund Analytics (Time series and spike detection).
     */
    public function buildRefundAnalytics(array $filters, Carbon $start, Carbon $end, string $interval): array
    {
        $buckets = $this->queryAggregatedTimeline($filters, $start, $end, $interval);

        $categories = [];
        $amounts = [];
        $counts = [];
        $spikes = [];

        foreach ($buckets as $key => $data) {
            $categories[] = $data['label'];
            $amt = (float) $data['refund_amount'];
            $cnt = (int) $data['refund_count'];
            $amounts[] = $amt;
            $counts[] = $cnt;
        }

        $avgRefund = count($amounts) > 0 ? array_sum($amounts) / count($amounts) : 0;
        $threshold = max(500, $avgRefund * 2);

        foreach ($amounts as $idx => $amt) {
            if ($amt > $threshold) {
                $spikes[] = [
                    'index' => $idx,
                    'date' => $categories[$idx] ?? '',
                    'amount' => $amt,
                    'reason' => 'Refund spike detected (>2x baseline)',
                ];
            }
        }

        return [
            'categories' => $categories,
            'series' => [
                ['name' => 'Refund Amount (₹)', 'type' => 'area', 'data' => $amounts],
                ['name' => 'Refund Count', 'type' => 'line', 'data' => $counts],
            ],
            'total_refund_amount' => array_sum($amounts),
            'total_refund_count' => array_sum($counts),
            'spikes' => $spikes,
        ];
    }

    /**
     * Build Top Selling Products with comparison growth and horizontal bars.
     */
    public function buildTopProducts(array $filters, Carbon $start, Carbon $end, Carbon $prevStart, Carbon $prevEnd, int $limit = 10): Collection
    {
        $orderIds = $this->getFilteredOrdersQuery($filters, $start, $end)->pluck('orders.id')->all();
        $prevOrderIds = $this->getFilteredOrdersQuery($filters, $prevStart, $prevEnd)->pluck('orders.id')->all();

        if (empty($orderIds)) {
            return collect();
        }

        $currentProducts = DB::table('order_items')
            ->whereIn('order_items.order_id', $orderIds)
            ->select(
                'order_items.product_name_snapshot as name',
                'order_items.sku_snapshot as sku',
                'order_items.product_variant_id',
                DB::raw('SUM(order_items.quantity) as units_sold'),
                DB::raw('SUM(order_items.line_total) as revenue'),
                DB::raw('COUNT(DISTINCT order_items.order_id) as orders_count'),
                DB::raw('AVG(order_items.unit_price) as avg_price')
            )
            ->groupBy('order_items.product_name_snapshot', 'order_items.sku_snapshot', 'order_items.product_variant_id')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get();

        $maxRevenue = (float) $currentProducts->max('revenue') ?: 1.0;

        // Previous period revenue per SKU for growth calculation
        $prevRevenueMap = [];
        if (!empty($prevOrderIds)) {
            $prevRevenueMap = DB::table('order_items')
                ->whereIn('order_id', $prevOrderIds)
                ->groupBy('sku_snapshot')
                ->pluck(DB::raw('SUM(line_total) as rev'), 'sku_snapshot')
                ->all();
        }

        return $currentProducts->map(function ($p) use ($maxRevenue, $prevRevenueMap) {
            $rev = round((float) $p->revenue, 2);
            $prevRev = (float) ($prevRevenueMap[$p->sku] ?? 0);
            $growth = $this->calculatePercentageDelta($rev, $prevRev);
            $units = (int) $p->units_sold;

            return [
                'name' => $p->name,
                'sku' => $p->sku ?: 'SKU-STD',
                'product_variant_id' => $p->product_variant_id,
                'units_sold' => $units,
                'revenue' => $rev,
                'orders_count' => (int) $p->orders_count,
                'avg_price' => $units > 0 ? round($rev / $units, 2) : round((float) $p->avg_price, 2),
                'growth' => $growth,
                'revenue_bar_pct' => round(($rev / $maxRevenue) * 100, 1),
            ];
        });
    }

    /**
     * Build Product Category Performance (Horizontal Bar Chart).
     */
    public function buildCategoryPerformance(array $filters, Carbon $start, Carbon $end): array
    {
        $orderIds = $this->getFilteredOrdersQuery($filters, $start, $end)->pluck('orders.id')->all();

        if (empty($orderIds)) {
            return [
                'categories' => ['Gut Reset Protocols'],
                'series' => [['name' => 'Revenue', 'data' => [0]]],
                'items' => [],
            ];
        }

        $rows = DB::table('order_items')
            ->whereIn('order_items.order_id', $orderIds)
            ->leftJoin('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
            ->leftJoin('products', 'product_variants.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                DB::raw("COALESCE(categories.name, 'Gut Protocols & Kits') as category_name"),
                DB::raw('COUNT(DISTINCT order_items.order_id) as orders_count'),
                DB::raw('SUM(order_items.quantity) as units_sold'),
                DB::raw('SUM(order_items.line_total) as revenue')
            )
            ->groupBy('category_name')
            ->orderByDesc('revenue')
            ->get();

        $categories = [];
        $revenues = [];
        $items = [];

        foreach ($rows as $row) {
            $catName = $row->category_name;
            $rev = round((float) $row->revenue, 2);
            $categories[] = $catName;
            $revenues[] = $rev;
            $items[] = [
                'category' => $catName,
                'revenue' => $rev,
                'orders_count' => (int) $row->orders_count,
                'units_sold' => (int) $row->units_sold,
            ];
        }

        return [
            'categories' => $categories,
            'series' => [
                ['name' => 'Revenue (₹)', 'data' => $revenues],
            ],
            'items' => $items,
        ];
    }

    /**
     * Location Analytics: Interactive State & City distributions and Leaflet map points.
     */
    public function buildLocationAnalytics(array $filters, Carbon $start, Carbon $end): array
    {
        $orderIds = $this->getFilteredOrdersQuery($filters, $start, $end)->pluck('orders.id')->all();

        // 1. By State
        $stateRows = DB::table('order_addresses')
            ->join('orders', 'order_addresses.order_id', '=', 'orders.id')
            ->where('order_addresses.type', 'shipping')
            ->whereIn('orders.id', $orderIds)
            ->select(
                DB::raw("COALESCE(NULLIF(order_addresses.state, ''), 'Unknown State') as state"),
                DB::raw('COUNT(DISTINCT orders.id) as orders_count'),
                DB::raw("SUM(CASE WHEN orders.payment_status = 'paid' THEN orders.grand_total ELSE 0 END) as revenue"),
                DB::raw("AVG(CASE WHEN orders.payment_status = 'paid' THEN orders.grand_total ELSE NULL END) as aov"),
                DB::raw("COUNT(CASE WHEN orders.refund_amount > 0 THEN orders.id ELSE NULL END) as refund_orders")
            )
            ->groupBy('state')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        $statesData = $stateRows->map(function ($s) {
            $orders = (int) $s->orders_count;
            $refunds = (int) $s->refund_orders;
            return [
                'name' => $s->state,
                'orders' => $orders,
                'revenue' => round((float) $s->revenue, 2),
                'aov' => round((float) ($s->aov ?? 0), 2),
                'refund_rate' => $orders > 0 ? round(($refunds / $orders) * 100, 1) : 0,
            ];
        });

        // 2. By City
        $cityRows = DB::table('order_addresses')
            ->join('orders', 'order_addresses.order_id', '=', 'orders.id')
            ->where('order_addresses.type', 'shipping')
            ->whereIn('orders.id', $orderIds)
            ->select(
                DB::raw("COALESCE(NULLIF(order_addresses.city, ''), 'Unknown City') as city"),
                DB::raw("COALESCE(NULLIF(order_addresses.state, ''), '') as state"),
                DB::raw('COUNT(DISTINCT orders.id) as orders_count'),
                DB::raw("SUM(CASE WHEN orders.payment_status = 'paid' THEN orders.grand_total ELSE 0 END) as revenue"),
                DB::raw("AVG(CASE WHEN orders.payment_status = 'paid' THEN orders.grand_total ELSE NULL END) as aov")
            )
            ->groupBy('city', 'state')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        $citiesData = $cityRows->map(function ($c) {
            return [
                'city' => $c->city,
                'state' => $c->state,
                'orders' => (int) $c->orders_count,
                'revenue' => round((float) $c->revenue, 2),
                'aov' => round((float) ($c->aov ?? 0), 2),
            ];
        });

        // 3. Map Points with Coordinates
        $cityCentroids = [
            'hyderabad' => [17.3850, 78.4867],
            'secunderabad' => [17.4399, 78.4983],
            'bengaluru' => [12.9716, 77.5946],
            'bangalore' => [12.9716, 77.5946],
            'mumbai' => [19.0760, 72.8777],
            'pune' => [18.5204, 73.8567],
            'delhi' => [28.6139, 77.2090],
            'new delhi' => [28.6139, 77.2090],
            'chennai' => [13.0827, 80.2707],
            'kolkata' => [22.5726, 88.3639],
            'ahmedabad' => [23.0225, 72.5714],
            'jaipur' => [26.9124, 75.7873],
            'lucknow' => [26.8467, 80.9462],
            'chandigarh' => [30.7333, 76.7794],
            'kochi' => [9.9312, 76.2673],
        ];

        $addressQuery = OrderAddress::where('type', 'shipping')
            ->whereIn('order_id', $orderIds)
            ->with(['order:id,order_number,grand_total,created_at,payment_status,refund_amount']);

        $mapPoints = [];
        foreach ($addressQuery->get() as $addr) {
            $cityKey = strtolower(trim($addr->city));
            $lat = $addr->latitude ?: ($cityCentroids[$cityKey][0] ?? null);
            $lng = $addr->longitude ?: ($cityCentroids[$cityKey][1] ?? null);

            if ($lat && $lng) {
                $mapPoints[] = [
                    'order_id' => $addr->order_id,
                    'order_number' => $addr->order?->order_number ?? '#' . $addr->order_id,
                    'city' => $addr->city,
                    'state' => $addr->state,
                    'lat' => (float) $lat,
                    'lng' => (float) $lng,
                    'revenue' => (float) ($addr->order?->grand_total ?? 0),
                    'payment_status' => $addr->order?->payment_status ?? 'pending',
                    'refund_amount' => (float) ($addr->order?->refund_amount ?? 0),
                ];
            }
        }

        return [
            'states' => [
                'categories' => $statesData->pluck('name')->all(),
                'series' => [
                    ['name' => 'Revenue (₹)', 'data' => $statesData->pluck('revenue')->all()],
                ],
                'items' => $statesData->all(),
            ],
            'cities' => [
                'categories' => $citiesData->pluck('city')->all(),
                'series' => [
                    ['name' => 'Revenue (₹)', 'data' => $citiesData->pluck('revenue')->all()],
                ],
                'items' => $citiesData->all(),
            ],
            'map_points' => $mapPoints,
        ];
    }

    /**
     * Build Average Order Value (AOV) Trend over time.
     */
    public function buildAovTrend(array $filters, Carbon $start, Carbon $end, string $interval): array
    {
        $buckets = $this->queryAggregatedTimeline($filters, $start, $end, $interval);

        $categories = [];
        $aovValues = [];

        foreach ($buckets as $key => $data) {
            $categories[] = $data['label'];
            $paidRev = (float) $data['net_sales'];
            $paidCnt = (int) $data['paid_orders'];
            $aov = $paidCnt > 0 ? round($paidRev / $paidCnt, 2) : 0.0;
            $aovValues[] = $aov;
        }

        return [
            'categories' => $categories,
            'series' => [
                ['name' => 'Average Order Value (₹)', 'data' => $aovValues],
            ],
            'current_aov' => count($aovValues) > 0 ? end($aovValues) : 0,
            'avg_aov' => count($aovValues) > 0 ? round(array_sum($aovValues) / count($aovValues), 2) : 0,
        ];
    }

    /**
     * Generate dynamic AI / Operational Insights based on real analytics data.
     */
    public function generateOperationalInsights(
        array $kpis,
        Collection $topProducts,
        array $locationAnalytics,
        array $paymentDistribution,
        array $channelSales,
        array $ordersTrend
    ): array {
        $insights = [];

        // 1. Highest Selling Product
        if ($topProducts->isNotEmpty()) {
            $bestProd = $topProducts->first();
            $totalNet = $kpis['net_sales']['current'] ?: 1;
            $share = round(($bestProd['revenue'] / $totalNet) * 100, 1);
            $insights[] = [
                'type' => 'product_leader',
                'title' => 'Top Revenue Driver',
                'metric' => '₹' . number_format($bestProd['revenue'], 2),
                'subtitle' => $bestProd['name'] . ' accounts for ' . $share . '% of net sales.',
                'action_label' => 'View Product',
                'filter_param' => ['search' => $bestProd['name']],
                'trend' => 'positive',
                'icon' => 'sparkles',
            ];
        }

        // 2. Highest Revenue State
        $topState = $locationAnalytics['states']['items'][0] ?? null;
        if ($topState && $topState['revenue'] > 0) {
            $insights[] = [
                'type' => 'top_state',
                'title' => 'Highest Revenue State',
                'metric' => $topState['name'],
                'subtitle' => 'Generated ₹' . number_format($topState['revenue'], 2) . ' across ' . $topState['orders'] . ' orders.',
                'action_label' => 'Filter by State',
                'filter_param' => ['state' => $topState['name']],
                'trend' => 'positive',
                'icon' => 'map-pin',
            ];
        }

        // 3. Best Performing Payment Method
        $topPayment = $paymentDistribution['details'][0] ?? null;
        if ($topPayment && $topPayment['amount'] > 0) {
            $insights[] = [
                'type' => 'payment_preference',
                'title' => 'Dominant Payment Method',
                'metric' => $topPayment['label'],
                'subtitle' => $topPayment['share_pct'] . '% of checkout volume (₹' . number_format($topPayment['amount'], 2) . ').',
                'action_label' => 'View Orders',
                'filter_param' => ['payment_method' => $topPayment['gateway']],
                'trend' => 'neutral',
                'icon' => 'credit-card',
            ];
        }

        // 4. Refund Health Check
        $refundAmt = $kpis['refund_amount']['current'] ?? 0;
        $netSales = $kpis['net_sales']['current'] ?? 1;
        $refundRate = $netSales > 0 ? round(($refundAmt / $netSales) * 100, 2) : 0;
        $insights[] = [
            'type' => 'refund_risk',
            'title' => 'Refund Rate Benchmark',
            'metric' => $refundRate . '%',
            'subtitle' => $refundRate < 3.0 ? 'Health is optimal (below 3% industry threshold).' : 'Attention required on product quality/returns.',
            'action_label' => 'Audit Refunds',
            'filter_param' => ['order_status' => 'refunded'],
            'trend' => $refundRate < 3.0 ? 'positive' : 'warning',
            'icon' => 'shield-check',
        ];

        // 5. Basket Size Momentum (AOV)
        $aovChange = $kpis['average_order_value']['delta_percent'] ?? 0;
        $currAov = $kpis['average_order_value']['formatted'] ?? '₹0';
        $insights[] = [
            'type' => 'aov_momentum',
            'title' => 'Basket Size Trajectory',
            'metric' => $currAov,
            'subtitle' => ($aovChange >= 0 ? '+' : '') . $aovChange . '% growth compared to previous timeframe.',
            'action_label' => 'Explore Bundles',
            'filter_param' => [],
            'trend' => $aovChange >= 0 ? 'positive' : 'warning',
            'icon' => 'trending-up',
        ];

        // 6. Peak Order Velocity
        if (!empty($ordersTrend['peak_day']['date']) && $ordersTrend['peak_day']['count'] > 0) {
            $insights[] = [
                'type' => 'peak_velocity',
                'title' => 'Peak Order Velocity',
                'metric' => $ordersTrend['peak_day']['count'] . ' Orders',
                'subtitle' => 'Record single-period volume achieved on ' . $ordersTrend['peak_day']['date'] . '.',
                'action_label' => 'Inspect Date',
                'filter_param' => ['search' => $ordersTrend['peak_day']['date']],
                'trend' => 'positive',
                'icon' => 'zap',
            ];
        }

        return $insights;
    }

    /**
     * Build Detailed Sales Table Data (Products, SKUs, Units Sold, Revenue, ASP, Refunds, Growth, Conversion).
     */
    public function buildSalesTableData(array $filters, Carbon $start, Carbon $end, Carbon $prevStart, Carbon $prevEnd): array
    {
        $orderIds = $this->getFilteredOrdersQuery($filters, $start, $end)->pluck('orders.id')->all();
        $prevOrderIds = $this->getFilteredOrdersQuery($filters, $prevStart, $prevEnd)->pluck('orders.id')->all();

        if (empty($orderIds)) {
            return [];
        }

        $items = DB::table('order_items')
            ->whereIn('order_items.order_id', $orderIds)
            ->select(
                'order_items.product_name_snapshot as name',
                'order_items.sku_snapshot as sku',
                'order_items.product_variant_id',
                DB::raw('SUM(order_items.quantity) as units_sold'),
                DB::raw('SUM(order_items.line_total) as revenue'),
                DB::raw('COUNT(DISTINCT order_items.order_id) as orders_count'),
                DB::raw('AVG(order_items.unit_price) as unit_price')
            )
            ->groupBy('order_items.product_name_snapshot', 'order_items.sku_snapshot', 'order_items.product_variant_id')
            ->orderByDesc('revenue')
            ->get();

        $prevMap = [];
        if (!empty($prevOrderIds)) {
            $prevMap = DB::table('order_items')
                ->whereIn('order_id', $prevOrderIds)
                ->groupBy('sku_snapshot')
                ->pluck(DB::raw('SUM(line_total)'), 'sku_snapshot')
                ->all();
        }

        $totalCatalogRevenue = (float) $items->sum('revenue') ?: 1.0;

        $table = [];
        foreach ($items as $item) {
            $rev = round((float) $item->revenue, 2);
            $prevRev = (float) ($prevMap[$item->sku] ?? 0);
            $units = (int) $item->units_sold;
            $orders = (int) $item->orders_count;
            $growth = $this->calculatePercentageDelta($rev, $prevRev);
            $share = round(($rev / $totalCatalogRevenue) * 100, 1);

            $table[] = [
                'product' => $item->name,
                'sku' => $item->sku ?: 'SKU-01',
                'variant_id' => $item->product_variant_id,
                'units_sold' => $units,
                'revenue' => $rev,
                'revenue_formatted' => '₹' . number_format($rev, 2),
                'average_price' => $units > 0 ? round($rev / $units, 2) : round((float) $item->unit_price, 2),
                'average_price_formatted' => '₹' . number_format($units > 0 ? $rev / $units : (float) $item->unit_price, 2),
                'orders' => $orders,
                'refunds' => 0.00, // per-item refund snapshot
                'refunds_formatted' => '₹0.00',
                'growth' => $growth,
                'growth_trend' => $growth >= 0 ? 'up' : 'down',
                'conversion_share' => $share,
            ];
        }

        return $table;
    }

    /**
     * Query timeline buckets for trend charts (Hourly, Daily, Weekly, Monthly).
     */
    protected function queryAggregatedTimeline(array $filters, Carbon $start, Carbon $end, string $interval): array
    {
        $query = $this->getFilteredOrdersQuery($filters, $start, $end);

        switch ($interval) {
            case 'hourly':
                $format = '%Y-%m-%d %H:00';
                $phpFormat = 'Y-m-d H:00';
                $displayFormat = 'H:i';
                break;
            case 'weekly':
                $format = '%Y-%u';
                $phpFormat = 'Y-W';
                $displayFormat = 'W [Wk] M';
                break;
            case 'monthly':
                $format = '%Y-%m';
                $phpFormat = 'Y-m';
                $displayFormat = 'M Y';
                break;
            case 'daily':
            default:
                $format = '%Y-%m-%d';
                $phpFormat = 'Y-m-d';
                $displayFormat = 'd M';
                break;
        }

        $rawStats = (clone $query)
            ->select(
                DB::raw("DATE_FORMAT(orders.created_at, '{$format}') as bucket"),
                DB::raw('COUNT(orders.id) as order_count'),
                DB::raw('SUM(orders.grand_total) as gross_sales'),
                DB::raw("SUM(CASE WHEN orders.payment_status = 'paid' THEN orders.grand_total ELSE 0 END) as paid_revenue"),
                DB::raw("COUNT(CASE WHEN orders.payment_status = 'paid' THEN orders.id ELSE NULL END) as paid_orders"),
                DB::raw('SUM(COALESCE(orders.refund_amount, 0)) as refund_amount'),
                DB::raw("COUNT(CASE WHEN orders.refund_amount > 0 THEN orders.id ELSE NULL END) as refund_count")
            )
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get()
            ->keyBy('bucket');

        // Generate complete sequence of buckets so there are no holes in charts
        $buckets = [];
        $cursor = $start->copy();

        while ($cursor <= $end) {
            $bKey = $cursor->format($phpFormat);
            $label = $cursor->format($displayFormat);

            if (!isset($buckets[$bKey])) {
                $stat = $rawStats[$bKey] ?? null;
                $gross = round((float) ($stat->gross_sales ?? 0), 2);
                $paid = round((float) ($stat->paid_revenue ?? 0), 2);
                $refund = round((float) ($stat->refund_amount ?? 0), 2);
                $net = max(0, round($paid - $refund, 2));

                $buckets[$bKey] = [
                    'key' => $bKey,
                    'label' => $label,
                    'gross_sales' => $gross,
                    'net_sales' => $net,
                    'order_count' => (int) ($stat->order_count ?? 0),
                    'paid_orders' => (int) ($stat->paid_orders ?? 0),
                    'refund_amount' => $refund,
                    'refund_count' => (int) ($stat->refund_count ?? 0),
                ];
            }

            switch ($interval) {
                case 'hourly':
                    $cursor->addHour();
                    break;
                case 'weekly':
                    $cursor->addWeek();
                    break;
                case 'monthly':
                    $cursor->addMonth();
                    break;
                case 'daily':
                default:
                    $cursor->addDay();
                    break;
            }
        }

        return $buckets;
    }

    /**
     * Helper to compute percentage change between current and previous values.
     */
    protected function calculatePercentageDelta(float $current, float $previous): float
    {
        if ($previous == 0.0) {
            return $current > 0 ? 100.0 : 0.0;
        }
        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * Resolve trend direction ('up', 'down', 'neutral').
     */
    protected function resolveTrendDirection(string $metricKey, float $current, float $previous): string
    {
        if ($current == $previous) {
            return 'neutral';
        }
        $isHigher = $current > $previous;

        // For refunds, lower is positive, higher is negative
        if (in_array($metricKey, ['refund_amount', 'refund_count'])) {
            return $isHigher ? 'down' : 'up';
        }

        return $isHigher ? 'up' : 'down';
    }

    /**
     * Format metric values for user presentation.
     */
    protected function formatMetricValue(string $key, float $value): string
    {
        if (in_array($key, ['gross_sales', 'net_sales', 'average_order_value', 'refund_amount'])) {
            return '₹' . number_format($value, 2);
        }
        return number_format((int) $value);
    }

    /**
     * Get cascading filter dropdown options from database.
     */
    public function getFilterDropdownOptions(): array
    {
        return Cache::remember('sales_filter_options', now()->addMinutes(10), function () {
            return [
                'countries' => OrderAddress::where('type', 'shipping')
                    ->whereNotNull('country')
                    ->where('country', '!=', '')
                    ->distinct()
                    ->orderBy('country')
                    ->pluck('country')
                    ->all(),
                'states' => OrderAddress::where('type', 'shipping')
                    ->whereNotNull('state')
                    ->where('state', '!=', '')
                    ->distinct()
                    ->orderBy('state')
                    ->pluck('state')
                    ->all(),
                'cities' => OrderAddress::where('type', 'shipping')
                    ->whereNotNull('city')
                    ->where('city', '!=', '')
                    ->distinct()
                    ->orderBy('city')
                    ->pluck('city')
                    ->all(),
                'payment_methods' => Payment::distinct()
                    ->whereNotNull('gateway')
                    ->pluck('gateway')
                    ->map(fn($g) => ['value' => $g, 'label' => ucwords(str_replace('_', ' ', $g))])
                    ->all(),
                'products' => Product::select('id', 'name')->orderBy('name')->get()->toArray(),
                'categories' => Category::select('id', 'name')->orderBy('name')->get()->toArray(),
                'channels' => Order::distinct()
                    ->whereNotNull('source')
                    ->pluck('source')
                    ->map(fn($s) => ['value' => $s, 'label' => ucfirst($s)])
                    ->all(),
                'order_statuses' => [
                    ['value' => 'paid', 'label' => 'Paid'],
                    ['value' => 'pending', 'label' => 'Payment Pending'],
                    ['value' => 'completed', 'label' => 'Fulfilled / Completed'],
                    ['value' => 'delivered', 'label' => 'Delivered'],
                    ['value' => 'refunded', 'label' => 'Refunded'],
                ],
            ];
        });
    }
}
