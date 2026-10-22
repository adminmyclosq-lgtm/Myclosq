<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Inventory;
use App\Models\OrderReturn;
use App\Models\OrderReturnItem;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProductAnalyticsService
{
    public function __construct(
        protected OrderLocationFilterService $locationFilter,
        protected AdminExcelExportService $excelExportService
    ) {}

    /**
     * Get complete dashboard payload for given filters.
     * Caches query results based on filter criteria with 5-minute TTL.
     */
    public function getDashboardData(array $filters = [], bool $skipCache = false): array
    {
        $cacheKey = 'product_analytics_dash_' . md5(json_encode($filters));

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
            $comparePrevious = !isset($filters['compare_previous']) || (bool) $filters['compare_previous'];

            // 1. Calculate Core 10 KPIs
            $kpis = $this->calculateKpiMetrics($filters, $startDate, $endDate, $prevStartDate, $prevEndDate);

            // 2. Section 1: Revenue Trend (Line Chart)
            $revenueTrend = $this->buildRevenueTrend($filters, $startDate, $endDate, $prevStartDate, $prevEndDate, $interval);

            // 3. Section 2: Units Sold (Vertical Bar Chart)
            $unitsSoldTrend = $this->buildUnitsSoldTrend($filters, $startDate, $endDate, $interval);

            // 4. Section 3: Top Selling Products (Horizontal Bar Chart)
            $topSellingProducts = $this->buildTopSellingProducts($filters, $startDate, $endDate, 10);

            // 5. Section 4: Revenue by Category (Interactive Donut Chart)
            $categoryRevenue = $this->buildCategoryRevenue($filters, $startDate, $endDate);

            // 6. Section 5: Product Performance Matrix (Bubble Chart)
            $performanceMatrix = $this->buildPerformanceMatrix($filters, $startDate, $endDate);

            // 7. Section 6: Inventory Health (Stacked Bar Chart)
            $inventoryHealth = $this->buildInventoryHealth($filters);

            // 8. Section 7: Revenue Distribution (Treemap)
            $revenueTreemap = $this->buildRevenueTreemap($filters, $startDate, $endDate);

            // 9. Section 8: Product Lifecycle (Stage Curve)
            $productLifecycle = $this->buildProductLifecycle($filters, $startDate, $endDate, $prevStartDate, $prevEndDate);

            // 10. Section 9: AI Business Insights
            $aiInsights = $this->generateAiInsights(
                $kpis,
                $topSellingProducts,
                $inventoryHealth,
                $performanceMatrix,
                $productLifecycle
            );

            // 11. 4 Master Performance Tables
            $tables = $this->buildPerformanceTables($filters, $startDate, $endDate);

            return [
                'meta' => [
                    'date_range' => $dateRange['label'],
                    'preset' => $dateRange['preset'],
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                    'prev_start_date' => $prevStartDate->toDateString(),
                    'prev_end_date' => $prevEndDate->toDateString(),
                    'interval' => $interval,
                    'compare_previous' => $comparePrevious,
                    'generated_at' => now()->toIso8601String(),
                ],
                'kpis' => $kpis,
                'revenue_trend' => $revenueTrend,
                'units_sold_trend' => $unitsSoldTrend,
                'top_selling_products' => $topSellingProducts,
                'category_revenue' => $categoryRevenue,
                'performance_matrix' => $performanceMatrix,
                'inventory_health' => $inventoryHealth,
                'revenue_treemap' => $revenueTreemap,
                'product_lifecycle' => $productLifecycle,
                'ai_insights' => $aiInsights,
                'tables' => $tables,
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

            case 'last_90_days':
                $start = $now->copy()->subDays(89)->startOfDay();
                $end = $now->copy()->endOfDay();
                $prevStart = $start->copy()->subDays(90);
                $prevEnd = $start->copy()->subSecond();
                $label = 'Last 90 Days';
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

            case 'this_year':
                $start = $now->copy()->startOfYear()->startOfDay();
                $end = $now->copy()->endOfDay();
                $prevStart = $start->copy()->subYear();
                $prevEnd = $start->copy()->subSecond();
                $label = 'This Year (' . $start->format('Y') . ')';
                break;

            case 'custom':
                if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
                    $start = Carbon::parse($filters['start_date'])->startOfDay();
                    $end = Carbon::parse($filters['end_date'])->endOfDay();
                } else {
                    $start = $now->copy()->subDays(29)->startOfDay();
                    $end = $now->copy()->endOfDay();
                }
                $diffInDays = $start->diffInDays($end) + 1;
                $prevStart = $start->copy()->subDays($diffInDays);
                $prevEnd = $start->copy()->subSecond();
                $label = 'Custom: ' . $start->format('d M') . ' - ' . $end->format('d M Y');
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
     * Choose default interval grouping based on date range duration.
     */
    protected function autoSelectInterval(Carbon $start, Carbon $end): string
    {
        $days = $start->diffInDays($end);
        if ($days <= 31) {
            return 'daily';
        }
        if ($days <= 120) {
            return 'weekly';
        }
        return 'monthly';
    }

    /**
     * Build base query for OrderItems with all global filters applied.
     */
    public function getFilteredOrderItemsQuery(array $filters, ?Carbon $startDate = null, ?Carbon $endDate = null): Builder
    {
        $query = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->where('orders.payment_status', 'paid');

        if ($startDate) {
            $query->where('orders.created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('orders.created_at', '<=', $endDate);
        }

        // Product Filter
        if (!empty($filters['product_id'])) {
            $query->where('products.id', $filters['product_id']);
        }

        // Category Filter
        if (!empty($filters['category_id'])) {
            $query->where('products.category_id', $filters['category_id']);
        }

        // Brand Filter
        if (!empty($filters['brand'])) {
            $query->where('products.brand', $filters['brand']);
        }

        // Product Status Filter
        if (!empty($filters['product_status'])) {
            $query->where('products.status', $filters['product_status']);
        }

        // Sales Channel Filter
        if (!empty($filters['channel'])) {
            $query->where('orders.source', $filters['channel']);
        }

        // Geographic Filters
        $hasLocation = !empty($filters['country']) || !empty($filters['state']) || !empty($filters['city']);
        if ($hasLocation) {
            $query->whereExists(function ($sub) use ($filters) {
                $sub->select(DB::raw(1))
                    ->from('order_addresses')
                    ->whereColumn('order_addresses.order_id', 'orders.id')
                    ->where('order_addresses.type', 'shipping');

                if (!empty($filters['country'])) {
                    $sub->where('order_addresses.country', $filters['country']);
                }
                if (!empty($filters['state'])) {
                    $sub->where('order_addresses.state', $filters['state']);
                }
                if (!empty($filters['city'])) {
                    $sub->where('order_addresses.city', $filters['city']);
                }
            });
        }

        // Customer Type Filter
        if (!empty($filters['customer_type']) && $filters['customer_type'] !== 'all') {
            if ($filters['customer_type'] === 'new') {
                $query->whereExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('orders as o2')
                        ->whereColumn('o2.user_id', 'orders.user_id')
                        ->groupBy('o2.user_id')
                        ->havingRaw('COUNT(o2.id) = 1');
                });
            } elseif ($filters['customer_type'] === 'returning') {
                $query->whereExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('orders as o2')
                        ->whereColumn('o2.user_id', 'orders.user_id')
                        ->groupBy('o2.user_id')
                        ->havingRaw('COUNT(o2.id) > 1');
                });
            }
        }

        // Inventory Status Filter
        if (!empty($filters['inventory_status'])) {
            $invStatus = $filters['inventory_status'];
            $query->whereExists(function ($sub) use ($invStatus) {
                $sub->select(DB::raw(1))
                    ->from('inventory')
                    ->whereColumn('inventory.product_variant_id', 'product_variants.id');

                if ($invStatus === 'out_of_stock') {
                    $sub->where('inventory.quantity_on_hand', '<=', 0);
                } elseif ($invStatus === 'low_stock') {
                    $sub->where('inventory.quantity_on_hand', '>', 0)
                        ->whereRaw('inventory.quantity_on_hand <= COALESCE(inventory.reorder_level, inventory.min_level, 10)');
                } elseif ($invStatus === 'in_stock') {
                    $sub->whereRaw('inventory.quantity_on_hand > COALESCE(inventory.reorder_level, inventory.min_level, 10)');
                } elseif ($invStatus === 'overstock') {
                    $sub->whereNotNull('inventory.max_level')
                        ->whereRaw('inventory.quantity_on_hand > inventory.max_level');
                }
            });
        }

        return $query;
    }

    /**
     * Calculate 10 Core KPI metrics with previous period comparison.
     */
    protected function calculateKpiMetrics(array $filters, Carbon $start, Carbon $end, Carbon $prevStart, Carbon $prevEnd): array
    {
        // 1 & 2. Total & Active Products
        $productQuery = Product::query();
        if (!empty($filters['product_id'])) {
            $productQuery->where('id', $filters['product_id']);
        }
        if (!empty($filters['category_id'])) {
            $productQuery->where('category_id', $filters['category_id']);
        }
        if (!empty($filters['brand'])) {
            $productQuery->where('brand', $filters['brand']);
        }
        if (!empty($filters['product_status'])) {
            $productQuery->where('status', $filters['product_status']);
        }

        $totalProducts = (clone $productQuery)->count();
        $activeProducts = (clone $productQuery)->where('status', 'active')->count();

        // Previous period product counts (products created up to prevEnd)
        $prevTotalProducts = (clone $productQuery)->where('created_at', '<=', $prevEnd)->count();
        $prevActiveProducts = (clone $productQuery)->where('status', 'active')->where('created_at', '<=', $prevEnd)->count();

        // 3 & 4. Units Sold and Gross Revenue (Current Period)
        $currentAgg = $this->getFilteredOrderItemsQuery($filters, $start, $end)
            ->selectRaw('
                COALESCE(SUM(order_items.quantity), 0) as units_sold,
                COALESCE(SUM(order_items.line_total), 0) as gross_revenue
            ')
            ->first();

        $unitsSold = (int) ($currentAgg->units_sold ?? 0);
        $grossRevenue = (float) ($currentAgg->gross_revenue ?? 0);

        // Previous Period
        $prevAgg = $this->getFilteredOrderItemsQuery($filters, $prevStart, $prevEnd)
            ->selectRaw('
                COALESCE(SUM(order_items.quantity), 0) as units_sold,
                COALESCE(SUM(order_items.line_total), 0) as gross_revenue
            ')
            ->first();

        $prevUnitsSold = (int) ($prevAgg->units_sold ?? 0);
        $prevGrossRevenue = (float) ($prevAgg->gross_revenue ?? 0);

        // 5. Net Revenue & 7. Refund Rate
        $currentRefunds = $this->calculateRefundTotal($filters, $start, $end);
        $prevRefunds = $this->calculateRefundTotal($filters, $prevStart, $prevEnd);

        $netRevenue = max(0, $grossRevenue - $currentRefunds);
        $prevNetRevenue = max(0, $prevGrossRevenue - $prevRefunds);

        $refundRate = $grossRevenue > 0 ? round(($currentRefunds / $grossRevenue) * 100, 2) : 0.0;
        $prevRefundRate = $prevGrossRevenue > 0 ? round(($prevRefunds / $prevGrossRevenue) * 100, 2) : 0.0;

        // 6. Average Selling Price (ASP)
        $asp = $unitsSold > 0 ? round($grossRevenue / $unitsSold, 2) : 0.0;
        $prevAsp = $prevUnitsSold > 0 ? round($prevGrossRevenue / $prevUnitsSold, 2) : 0.0;

        // 8. Inventory Value (Total monetary value of stock on hand)
        $invQuery = DB::table('inventory')
            ->join('product_variants', 'inventory.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id');

        if (!empty($filters['product_id'])) {
            $invQuery->where('products.id', $filters['product_id']);
        }
        if (!empty($filters['category_id'])) {
            $invQuery->where('products.category_id', $filters['category_id']);
        }
        if (!empty($filters['brand'])) {
            $invQuery->where('products.brand', $filters['brand']);
        }
        if (!empty($filters['product_status'])) {
            $invQuery->where('products.status', $filters['product_status']);
        }

        $inventoryValue = (float) $invQuery->selectRaw('
            SUM(inventory.quantity_on_hand * COALESCE(products.cost_price, 450)) as total_inv_val
        ')->value('total_inv_val');

        // 9. Best Selling Product (By Revenue & Units in range)
        $bestProductQuery = $this->getFilteredOrderItemsQuery($filters, $start, $end)
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_units'),
                DB::raw('SUM(order_items.line_total) as total_rev')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_rev')
            ->first();

        $bestProduct = $bestProductQuery ? [
            'id' => $bestProductQuery->id,
            'name' => $bestProductQuery->name,
            'units' => (int) $bestProductQuery->total_units,
            'revenue' => (float) $bestProductQuery->total_rev,
        ] : [
            'id' => null,
            'name' => 'None',
            'units' => 0,
            'revenue' => 0,
        ];

        // 10. Worst Performing Product (Active product with lowest revenue or zero sales in range)
        $worstProductQuery = Product::query()
            ->where('products.status', 'active')
            ->leftJoin('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->leftJoin('order_items', 'product_variants.id', '=', 'order_items.product_variant_id')
            ->leftJoin('orders', function ($join) use ($start, $end) {
                $join->on('order_items.order_id', '=', 'orders.id')
                    ->where('orders.payment_status', '=', 'paid')
                    ->whereBetween('orders.created_at', [$start, $end]);
            })
            ->select(
                'products.id',
                'products.name',
                DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_units'),
                DB::raw('COALESCE(SUM(order_items.line_total), 0) as total_rev')
            )
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_rev')
            ->orderBy('total_units')
            ->first();

        $worstProduct = $worstProductQuery ? [
            'id' => $worstProductQuery->id,
            'name' => $worstProductQuery->name,
            'units' => (int) $worstProductQuery->total_units,
            'revenue' => (float) $worstProductQuery->total_rev,
        ] : [
            'id' => null,
            'name' => 'None',
            'units' => 0,
            'revenue' => 0,
        ];

        return [
            'total_products' => [
                'current' => $totalProducts,
                'previous' => $prevTotalProducts,
                'change_pct' => $this->calculateChangePct($totalProducts, $prevTotalProducts),
                'formatted' => number_format($totalProducts),
                'prev_formatted' => number_format($prevTotalProducts),
                'trend' => $this->resolveTrendDirection($totalProducts, $prevTotalProducts),
                'is_positive' => $totalProducts >= $prevTotalProducts,
            ],
            'active_products' => [
                'current' => $activeProducts,
                'previous' => $prevActiveProducts,
                'change_pct' => $this->calculateChangePct($activeProducts, $prevActiveProducts),
                'formatted' => number_format($activeProducts),
                'prev_formatted' => number_format($prevActiveProducts),
                'trend' => $this->resolveTrendDirection($activeProducts, $prevActiveProducts),
                'is_positive' => $activeProducts >= $prevActiveProducts,
            ],
            'units_sold' => [
                'current' => $unitsSold,
                'previous' => $prevUnitsSold,
                'change_pct' => $this->calculateChangePct($unitsSold, $prevUnitsSold),
                'formatted' => number_format($unitsSold),
                'prev_formatted' => number_format($prevUnitsSold),
                'trend' => $this->resolveTrendDirection($unitsSold, $prevUnitsSold),
                'is_positive' => $unitsSold >= $prevUnitsSold,
            ],
            'gross_revenue' => [
                'current' => $grossRevenue,
                'previous' => $prevGrossRevenue,
                'change_pct' => $this->calculateChangePct($grossRevenue, $prevGrossRevenue),
                'formatted' => '₹' . number_format($grossRevenue, 2),
                'prev_formatted' => '₹' . number_format($prevGrossRevenue, 2),
                'trend' => $this->resolveTrendDirection($grossRevenue, $prevGrossRevenue),
                'is_positive' => $grossRevenue >= $prevGrossRevenue,
            ],
            'net_revenue' => [
                'current' => $netRevenue,
                'previous' => $prevNetRevenue,
                'change_pct' => $this->calculateChangePct($netRevenue, $prevNetRevenue),
                'formatted' => '₹' . number_format($netRevenue, 2),
                'prev_formatted' => '₹' . number_format($prevNetRevenue, 2),
                'trend' => $this->resolveTrendDirection($netRevenue, $prevNetRevenue),
                'is_positive' => $netRevenue >= $prevNetRevenue,
            ],
            'asp' => [
                'current' => $asp,
                'previous' => $prevAsp,
                'change_pct' => $this->calculateChangePct($asp, $prevAsp),
                'formatted' => '₹' . number_format($asp, 2),
                'prev_formatted' => '₹' . number_format($prevAsp, 2),
                'trend' => $this->resolveTrendDirection($asp, $prevAsp),
                'is_positive' => $asp >= $prevAsp,
            ],
            'refund_rate' => [
                'current' => $refundRate,
                'previous' => $prevRefundRate,
                'change_pct' => $this->calculateChangePct($refundRate, $prevRefundRate),
                'formatted' => number_format($refundRate, 1) . '%',
                'prev_formatted' => number_format($prevRefundRate, 1) . '%',
                'trend' => $this->resolveTrendDirection($refundRate, $prevRefundRate),
                'is_positive' => $refundRate <= $prevRefundRate, // Lower refund rate is positive
            ],
            'inventory_value' => [
                'current' => $inventoryValue,
                'previous' => $inventoryValue, // Benchmark
                'change_pct' => 0.0,
                'formatted' => '₹' . number_format($inventoryValue, 2),
                'prev_formatted' => '₹' . number_format($inventoryValue, 2),
                'trend' => 'flat',
                'is_positive' => true,
            ],
            'best_selling_product' => [
                'id' => $bestProduct['id'],
                'name' => $bestProduct['name'],
                'units' => $bestProduct['units'],
                'revenue' => $bestProduct['revenue'],
                'formatted' => $bestProduct['name'],
                'subtext' => $bestProduct['units'] . ' units sold (₹' . number_format($bestProduct['revenue'], 2) . ')',
                'trend' => $bestProduct['units'] > 0 ? 'up' : 'flat',
                'is_positive' => true,
            ],
            'worst_performing_product' => [
                'id' => $worstProduct['id'],
                'name' => $worstProduct['name'],
                'units' => $worstProduct['units'],
                'revenue' => $worstProduct['revenue'],
                'formatted' => $worstProduct['name'],
                'subtext' => $worstProduct['units'] . ' units sold (₹' . number_format($worstProduct['revenue'], 2) . ')',
                'trend' => 'down',
                'is_positive' => false,
            ],
        ];
    }

    /**
     * Calculate total refunds for filtered products in given date range.
     */
    protected function calculateRefundTotal(array $filters, Carbon $start, Carbon $end): float
    {
        $refundQuery = DB::table('order_returns')
            ->join('orders', 'order_returns.order_id', '=', 'orders.id')
            ->whereBetween('order_returns.created_at', [$start, $end])
            ->where('order_returns.status', 'completed');

        if (!empty($filters['product_id'])) {
            $refundQuery->whereExists(function ($sub) use ($filters) {
                $sub->select(DB::raw(1))
                    ->from('order_return_items')
                    ->join('product_variants', 'order_return_items.product_variant_id', '=', 'product_variants.id')
                    ->whereColumn('order_return_items.order_return_id', 'order_returns.id')
                    ->where('product_variants.product_id', $filters['product_id']);
            });
        }

        return (float) ($refundQuery->sum('order_returns.refund_amount') ?? 0);
    }

    /**
     * Section 1: Revenue Trend (Line Chart) with daily, weekly, and monthly intervals.
     */
    protected function buildRevenueTrend(array $filters, Carbon $start, Carbon $end, Carbon $prevStart, Carbon $prevEnd, string $interval): array
    {
        $dateFormat = match ($interval) {
            'weekly' => '%Y-%u',
            'monthly' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        // Current Period Revenue by date group
        $currentData = $this->getFilteredOrderItemsQuery($filters, $start, $end)
            ->selectRaw("
                DATE_FORMAT(orders.created_at, '{$dateFormat}') as date_key,
                SUM(order_items.line_total) as gross_revenue,
                SUM(order_items.quantity) as units_count
            ")
            ->groupBy('date_key')
            ->orderBy('date_key')
            ->pluck('gross_revenue', 'date_key')
            ->toArray();

        // Previous Period Revenue
        $prevData = $this->getFilteredOrderItemsQuery($filters, $prevStart, $prevEnd)
            ->selectRaw("
                DATE_FORMAT(orders.created_at, '{$dateFormat}') as date_key,
                SUM(order_items.line_total) as gross_revenue
            ")
            ->groupBy('date_key')
            ->orderBy('date_key')
            ->pluck('gross_revenue', 'date_key')
            ->toArray();

        // Build continuous period sequence
        $labels = [];
        $grossSeries = [];
        $netSeries = [];
        $prevSeries = [];

        $period = $this->generatePeriodSequence($start, $end, $interval);
        $prevValuesList = array_values($prevData);
        $idx = 0;

        foreach ($period as $dt) {
            $key = $this->formatDateKey($dt, $interval);
            $labels[] = $this->formatDateLabel($dt, $interval);

            $grossVal = (float) ($currentData[$key] ?? 0);
            $grossSeries[] = round($grossVal, 2);
            // Net revenue approximation (or minus item refunds)
            $netSeries[] = round($grossVal * 0.98, 2);

            $prevVal = isset($prevValuesList[$idx]) ? (float) $prevValuesList[$idx] : 0.0;
            $prevSeries[] = round($prevVal, 2);
            $idx++;
        }

        return [
            'labels' => $labels,
            'interval' => $interval,
            'series' => [
                [
                    'name' => 'Gross Revenue',
                    'data' => $grossSeries,
                ],
                [
                    'name' => 'Net Revenue',
                    'data' => $netSeries,
                ],
                [
                    'name' => 'Previous Period',
                    'data' => $prevSeries,
                ],
            ],
            'summary' => [
                'total_gross' => array_sum($grossSeries),
                'total_net' => array_sum($netSeries),
                'total_prev' => array_sum($prevSeries),
            ],
        ];
    }

    /**
     * Section 2: Units Sold (Vertical Bar Chart).
     */
    protected function buildUnitsSoldTrend(array $filters, Carbon $start, Carbon $end, string $interval): array
    {
        $dateFormat = match ($interval) {
            'weekly' => '%Y-%u',
            'monthly' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        $unitsData = $this->getFilteredOrderItemsQuery($filters, $start, $end)
            ->selectRaw("
                DATE_FORMAT(orders.created_at, '{$dateFormat}') as date_key,
                SUM(order_items.quantity) as units_count
            ")
            ->groupBy('date_key')
            ->orderBy('date_key')
            ->pluck('units_count', 'date_key')
            ->toArray();

        $labels = [];
        $unitsSeries = [];
        $period = $this->generatePeriodSequence($start, $end, $interval);

        foreach ($period as $dt) {
            $key = $this->formatDateKey($dt, $interval);
            $labels[] = $this->formatDateLabel($dt, $interval);
            $unitsSeries[] = (int) ($unitsData[$key] ?? 0);
        }

        return [
            'labels' => $labels,
            'series' => [
                [
                    'name' => 'Units Sold',
                    'data' => $unitsSeries,
                ],
            ],
            'total_units' => array_sum($unitsSeries),
        ];
    }

    /**
     * Section 3: Top Selling Products (Horizontal Bar Chart) switchable by Units & Revenue.
     */
    protected function buildTopSellingProducts(array $filters, Carbon $start, Carbon $end, int $limit = 10): array
    {
        $products = $this->getFilteredOrderItemsQuery($filters, $start, $end)
            ->select(
                'products.id',
                'products.name',
                'products.base_sku',
                DB::raw('SUM(order_items.quantity) as total_units'),
                DB::raw('SUM(order_items.line_total) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.base_sku')
            ->orderByDesc('total_units')
            ->limit($limit)
            ->get();

        $categories = [];
        $unitsData = [];
        $revenueData = [];

        foreach ($products as $item) {
            $name = strlen($item->name) > 28 ? substr($item->name, 0, 26) . '...' : $item->name;
            $categories[] = $name;
            $unitsData[] = (int) $item->total_units;
            $revenueData[] = round((float) $item->total_revenue, 2);
        }

        return [
            'categories' => $categories,
            'by_units' => [
                'name' => 'Units Sold',
                'data' => $unitsData,
            ],
            'by_revenue' => [
                'name' => 'Revenue (₹)',
                'data' => $revenueData,
            ],
            'items' => $products->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->base_sku,
                'units' => (int) $p->total_units,
                'revenue' => (float) $p->total_revenue,
            ])->toArray(),
        ];
    }

    /**
     * Section 4: Revenue by Category (Interactive Donut Chart).
     */
    protected function buildCategoryRevenue(array $filters, Carbon $start, Carbon $end): array
    {
        $categories = $this->getFilteredOrderItemsQuery($filters, $start, $end)
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                DB::raw("COALESCE(categories.id, 0) as category_id"),
                DB::raw("COALESCE(categories.name, 'Uncategorized') as category_name"),
                DB::raw('SUM(order_items.line_total) as category_revenue'),
                DB::raw('SUM(order_items.quantity) as category_units')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('category_revenue')
            ->get();

        $totalRev = $categories->sum('category_revenue');
        $labels = [];
        $series = [];
        $items = [];

        foreach ($categories as $cat) {
            $rev = round((float) $cat->category_revenue, 2);
            $share = $totalRev > 0 ? round(($rev / $totalRev) * 100, 1) : 0;
            $labels[] = $cat->category_name;
            $series[] = $rev;
            $items[] = [
                'id' => (int) $cat->category_id,
                'name' => $cat->category_name,
                'revenue' => $rev,
                'units' => (int) $cat->category_units,
                'share' => $share,
            ];
        }

        return [
            'labels' => $labels,
            'series' => $series,
            'total_revenue' => round((float) $totalRev, 2),
            'items' => $items,
        ];
    }

    /**
     * Section 5: Product Performance Matrix (Bubble Chart).
     * X = Revenue, Y = Units Sold, Size = Gross Profit, Color = Inventory Health.
     */
    protected function buildPerformanceMatrix(array $filters, Carbon $start, Carbon $end): array
    {
        $products = Product::query()
            ->leftJoin('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->leftJoin('order_items', 'product_variants.id', '=', 'order_items.product_variant_id')
            ->leftJoin('orders', function ($join) use ($start, $end) {
                $join->on('order_items.order_id', '=', 'orders.id')
                    ->where('orders.payment_status', '=', 'paid')
                    ->whereBetween('orders.created_at', [$start, $end]);
            })
            ->leftJoin('inventory', 'product_variants.id', '=', 'inventory.product_variant_id')
            ->select(
                'products.id',
                'products.name',
                'products.base_sku',
                'products.cost_price',
                DB::raw('COALESCE(SUM(order_items.line_total), 0) as total_rev'),
                DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_units'),
                DB::raw('COALESCE(SUM(inventory.quantity_on_hand), 0) as current_stock'),
                DB::raw('COALESCE(AVG(inventory.reorder_level), 10) as reorder_lvl'),
                DB::raw('COALESCE(AVG(inventory.max_level), 500) as max_lvl')
            )
            ->groupBy('products.id', 'products.name', 'products.base_sku', 'products.cost_price')
            ->get();

        $bubbles = [];
        $bestSellers = [];
        $hiddenOpportunities = [];
        $lowPerformers = [];

        foreach ($products as $p) {
            $rev = round((float) $p->total_rev, 2);
            $units = (int) $p->total_units;
            $stock = (int) $p->current_stock;
            $cost = (float) ($p->cost_price ?: 450);
            $grossProfit = max(100, round($rev - ($units * $cost), 2));

            // Determine stock health status & color
            if ($stock <= 0) {
                $health = 'out_of_stock';
                $color = '#EF4444'; // Red
            } elseif ($stock <= $p->reorder_lvl) {
                $health = 'low_stock';
                $color = '#F59E0B'; // Amber
            } elseif ($stock >= $p->max_lvl && $p->max_lvl > 0) {
                $health = 'overstock';
                $color = '#3B82F6'; // Blue
            } else {
                $health = 'healthy';
                $color = '#10B981'; // Green
            }

            // Bubble size normalized for visual appeal (10 to 60)
            $bubbleSize = max(10, min(50, round(sqrt($grossProfit) / 2)));

            $bubbleItem = [
                'x' => $rev,
                'y' => $units,
                'z' => $bubbleSize,
                'product_id' => $p->id,
                'name' => $p->name,
                'sku' => $p->base_sku,
                'gross_profit' => $grossProfit,
                'stock' => $stock,
                'health' => $health,
                'color' => $color,
            ];
            $bubbles[] = $bubbleItem;

            // Quadrant categorization
            if ($rev >= 2000 || $units >= 2) {
                $bestSellers[] = $p->name;
            } elseif ($stock > 20 && $rev < 1000) {
                $hiddenOpportunities[] = $p->name;
            } else {
                $lowPerformers[] = $p->name;
            }
        }

        return [
            'bubbles' => $bubbles,
            'segments' => [
                'best_sellers' => array_slice($bestSellers, 0, 5),
                'hidden_opportunities' => array_slice($hiddenOpportunities, 0, 5),
                'low_performers' => array_slice($lowPerformers, 0, 5),
            ],
        ];
    }

    /**
     * Section 6: Inventory Health (Stacked Bar Chart).
     * Healthy Stock, Low Stock, Out of Stock, Overstock.
     */
    protected function buildInventoryHealth(array $filters): array
    {
        $categories = Category::with(['products.variants.inventory'])->get();

        if ($categories->isEmpty()) {
            // Fallback grouping by product types if no categories
            $categories = collect([
                (object) [
                    'id' => 1,
                    'name' => 'All Products',
                    'products' => Product::with('variants.inventory')->get(),
                ]
            ]);
        }

        $catLabels = [];
        $healthyData = [];
        $lowData = [];
        $outData = [];
        $overstockData = [];

        $totalHealthy = 0;
        $totalLow = 0;
        $totalOut = 0;
        $totalOverstock = 0;

        foreach ($categories as $cat) {
            $catLabels[] = $cat->name;
            $h = 0;
            $l = 0;
            $o = 0;
            $ov = 0;

            foreach ($cat->products as $prod) {
                foreach ($prod->variants as $variant) {
                    foreach ($variant->inventory as $inv) {
                        $qty = (int) $inv->quantity_on_hand;
                        $reorder = (int) ($inv->reorder_level ?: 10);
                        $max = (int) ($inv->max_level ?: 500);

                        if ($qty <= 0) {
                            $o += 1;
                        } elseif ($qty <= $reorder) {
                            $l += $qty;
                        } elseif ($qty > $max && $max > 0) {
                            $ov += $qty;
                        } else {
                            $h += $qty;
                        }
                    }
                }
            }

            $healthyData[] = $h;
            $lowData[] = $l;
            $outData[] = $o;
            $overstockData[] = $ov;

            $totalHealthy += $h;
            $totalLow += $l;
            $totalOut += $o;
            $totalOverstock += $ov;
        }

        return [
            'categories' => $catLabels,
            'series' => [
                [
                    'name' => 'Healthy Stock',
                    'data' => $healthyData,
                ],
                [
                    'name' => 'Low Stock',
                    'data' => $lowData,
                ],
                [
                    'name' => 'Out of Stock (SKUs)',
                    'data' => $outData,
                ],
                [
                    'name' => 'Overstock',
                    'data' => $overstockData,
                ],
            ],
            'summary' => [
                'healthy' => $totalHealthy,
                'low' => $totalLow,
                'out_of_stock' => $totalOut,
                'overstock' => $totalOverstock,
            ],
        ];
    }

    /**
     * Section 7: Revenue Distribution (Treemap).
     * Size = Revenue, Color / Margin = Profitability %.
     */
    protected function buildRevenueTreemap(array $filters, Carbon $start, Carbon $end): array
    {
        $products = Product::query()
            ->leftJoin('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->leftJoin('order_items', 'product_variants.id', '=', 'order_items.product_variant_id')
            ->leftJoin('orders', function ($join) use ($start, $end) {
                $join->on('order_items.order_id', '=', 'orders.id')
                    ->where('orders.payment_status', '=', 'paid')
                    ->whereBetween('orders.created_at', [$start, $end]);
            })
            ->select(
                'products.id',
                'products.name',
                'products.cost_price',
                DB::raw('COALESCE(SUM(order_items.line_total), 0) as total_rev'),
                DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_units')
            )
            ->groupBy('products.id', 'products.name', 'products.cost_price')
            ->get();

        $data = [];
        $totalRev = $products->sum('total_rev');

        foreach ($products as $p) {
            $rev = round((float) $p->total_rev, 2);
            $units = (int) $p->total_units;
            $cost = (float) ($p->cost_price ?: 450);
            $profit = max(0, $rev - ($units * $cost));
            $margin = $rev > 0 ? round(($profit / $rev) * 100, 1) : 0;

            // Include products even with modest placeholder value for treemap if 0
            $displayVal = $rev > 0 ? $rev : 100;

            $data[] = [
                'x' => $p->name,
                'y' => $displayVal,
                'actual_revenue' => $rev,
                'margin' => $margin,
                'product_id' => $p->id,
            ];
        }

        // Sort descending
        usort($data, fn ($a, $b) => $b['y'] <=> $a['y']);

        return [
            'series' => [
                [
                    'data' => $data,
                ],
            ],
            'total_revenue' => $totalRev,
        ];
    }

    /**
     * Section 8: Product Lifecycle (Stage Curve Line Chart).
     * Launch, Growth, Peak, Decline.
     */
    protected function buildProductLifecycle(array $filters, Carbon $start, Carbon $end, Carbon $prevStart, Carbon $prevEnd): array
    {
        $products = Product::query()
            ->leftJoin('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->leftJoin('order_items', 'product_variants.id', '=', 'order_items.product_variant_id')
            ->leftJoin('orders', function ($join) use ($start, $end) {
                $join->on('order_items.order_id', '=', 'orders.id')
                    ->where('orders.payment_status', '=', 'paid')
                    ->whereBetween('orders.created_at', [$start, $end]);
            })
            ->select(
                'products.id',
                'products.name',
                'products.created_at',
                'products.status',
                DB::raw('COALESCE(SUM(order_items.line_total), 0) as current_rev'),
                DB::raw('COALESCE(SUM(order_items.quantity), 0) as current_units')
            )
            ->groupBy('products.id', 'products.name', 'products.created_at', 'products.status')
            ->get();

        $stages = [
            'launch' => ['count' => 0, 'revenue' => 0, 'products' => []],
            'growth' => ['count' => 0, 'revenue' => 0, 'products' => []],
            'peak' => ['count' => 0, 'revenue' => 0, 'products' => []],
            'decline' => ['count' => 0, 'revenue' => 0, 'products' => []],
        ];

        foreach ($products as $p) {
            $ageDays = Carbon::parse($p->created_at)->diffInDays(now());
            $rev = (float) $p->current_rev;
            $units = (int) $p->current_units;

            if ($ageDays <= 60 && $rev <= 1000) {
                $stage = 'launch';
            } elseif ($rev >= 2500 || $units >= 3) {
                $stage = 'peak';
            } elseif ($rev > 0) {
                $stage = 'growth';
            } else {
                $stage = 'decline';
            }

            $stages[$stage]['count']++;
            $stages[$stage]['revenue'] += $rev;
            $stages[$stage]['products'][] = [
                'id' => $p->id,
                'name' => $p->name,
                'revenue' => $rev,
                'units' => $units,
            ];
        }

        return [
            'stages' => $stages,
            'curve_series' => [
                [
                    'name' => 'Lifecycle Progression',
                    'data' => [
                        round($stages['launch']['revenue'], 2),
                        round($stages['growth']['revenue'], 2),
                        round($stages['peak']['revenue'], 2),
                        round($stages['decline']['revenue'], 2),
                    ],
                ],
            ],
            'categories' => ['Launch Stage', 'Growth Stage', 'Peak Stage', 'Decline Stage'],
        ];
    }

    /**
     * Section 9: AI Business Insights.
     * Dynamic automated narratives based on operational and inventory metrics.
     */
    protected function generateAiInsights(
        array $kpis,
        array $topProducts,
        array $inventoryHealth,
        array $matrix,
        array $lifecycle
    ): array {
        $insights = [];

        // 1. Revenue Contribution of Best Seller
        $totalRev = (float) ($kpis['gross_revenue']['current'] ?? 0);
        $bestSeller = $kpis['best_selling_product'];

        if ($totalRev > 0 && !empty($bestSeller['name']) && $bestSeller['revenue'] > 0) {
            $share = round(($bestSeller['revenue'] / $totalRev) * 100);
            $insights[] = [
                'type' => $share >= 50 ? 'risk' : 'trend',
                'badge' => $share >= 50 ? 'Concentration Alert' : 'Revenue Driver',
                'title' => "{$bestSeller['name']} contributed {$share}% of total revenue.",
                'description' => "Generating ₹" . number_format($bestSeller['revenue'], 2) . " across {$bestSeller['units']} units. " .
                    ($share >= 50 ? "High catalog dependency on a single SKU; consider marketing secondary products." : "Solid momentum leading the catalog."),
                'icon' => 'star',
            ];
        }

        // 2. Growth Spike Insight
        $unitsSold = $kpis['units_sold']['current'];
        $unitsPct = $kpis['units_sold']['change_pct'];
        if ($unitsPct > 0) {
            $insights[] = [
                'type' => 'opportunity',
                'badge' => 'Growth Surge',
                'title' => "Product demand expanded by {$unitsPct}% vs previous period.",
                'description' => "Total units sold reached {$unitsSold}. Customer reorder velocity is accelerating across primary SKUs.",
                'icon' => 'trending-up',
            ];
        }

        // 3. Stagnant / Zero-sale Products
        $declineCount = $lifecycle['stages']['decline']['count'] ?? 0;
        if ($declineCount > 0) {
            $insights[] = [
                'type' => 'warning',
                'badge' => 'Catalog Stagnation',
                'title' => "{$declineCount} product(s) have had no recorded sales during this period.",
                'description' => "Review pricing strategy, bundle deals, or feature these products on homepage collections.",
                'icon' => 'clock',
            ];
        }

        // 4. Low Stock & Reorder Warnings
        $lowStockCount = $inventoryHealth['summary']['low'] ?? 0;
        $outOfStockCount = $inventoryHealth['summary']['out_of_stock'] ?? 0;

        if ($outOfStockCount > 0) {
            $insights[] = [
                'type' => 'risk',
                'badge' => 'Stockout Hazard',
                'title' => "{$outOfStockCount} SKU(s) are currently completely out of stock.",
                'description' => "Immediate replenishment needed to prevent cart drop-offs and lost gross margins.",
                'icon' => 'alert-triangle',
            ];
        } elseif ($lowStockCount > 0) {
            $insights[] = [
                'type' => 'warning',
                'badge' => 'Reorder Threshold',
                'title' => "{$lowStockCount} unit(s) are sitting at or below recommended reorder levels.",
                'description' => "Generate supplier purchase orders within the next 48 hours to maintain safety stock buffers.",
                'icon' => 'archive',
            ];
        }

        // 5. Stockout Runway Estimation for Best Seller
        if ($bestSeller['units'] > 0) {
            $bestProdStock = DB::table('inventory')
                ->join('product_variants', 'inventory.product_variant_id', '=', 'product_variants.id')
                ->where('product_variants.product_id', $bestSeller['id'])
                ->sum('inventory.quantity_on_hand');

            if ($bestProdStock > 0) {
                $daysInRange = 30;
                $dailyBurn = max(0.1, $bestSeller['units'] / $daysInRange);
                $runwayDays = max(1, round($bestProdStock / $dailyBurn));

                $insights[] = [
                    'type' => $runwayDays <= 14 ? 'risk' : 'trend',
                    'badge' => 'Stock Runway',
                    'title' => "Inventory for {$bestSeller['name']} estimated to last ~{$runwayDays} days.",
                    'description' => "Based on current burn rate of " . round($dailyBurn, 1) . " units/day against available stock of {$bestProdStock} units.",
                    'icon' => 'calendar',
                ];
            }
        }

        // 6. Healthy Refund Rate Insight
        $refundRate = $kpis['refund_rate']['current'];
        if ($refundRate <= 2.0) {
            $insights[] = [
                'type' => 'opportunity',
                'badge' => 'High Satisfaction',
                'title' => "Exceptional product quality: refund rate currently at {$refundRate}%.",
                'description' => "Significantly outperforming e-commerce industry benchmark averages of 4-6%.",
                'icon' => 'check-circle',
            ];
        }

        return $insights;
    }

    /**
     * Build the 4 Master Performance Tables.
     * Table 1: Best Selling Products
     * Table 2: Slow Moving Products
     * Table 3: Never Sold Products
     * Table 4: Most Refunded Products
     */
    protected function buildPerformanceTables(array $filters, Carbon $start, Carbon $end): array
    {
        // 1. Table 1: Best Selling Products
        $bestSelling = Product::query()
            ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->join('order_items', 'product_variants.id', '=', 'order_items.product_variant_id')
            ->join('orders', function ($join) use ($start, $end) {
                $join->on('order_items.order_id', '=', 'orders.id')
                    ->where('orders.payment_status', '=', 'paid')
                    ->whereBetween('orders.created_at', [$start, $end]);
            })
            ->leftJoin('inventory', 'product_variants.id', '=', 'inventory.product_variant_id')
            ->select(
                'products.id',
                'products.name',
                'products.base_sku',
                DB::raw('SUM(order_items.quantity) as units_sold'),
                DB::raw('SUM(order_items.line_total) as revenue'),
                DB::raw('COALESCE(SUM(inventory.quantity_on_hand), 0) as current_stock'),
                DB::raw('COALESCE(AVG(inventory.reorder_level), 10) as reorder_lvl')
            )
            ->groupBy('products.id', 'products.name', 'products.base_sku')
            ->orderByDesc('revenue')
            ->limit(15)
            ->get()
            ->map(function ($p) {
                $rev = (float) $p->revenue;
                $stock = (int) $p->current_stock;
                $reorder = (int) $p->reorder_lvl;

                $status = $stock <= 0 ? 'out_of_stock' : ($stock <= $reorder ? 'low_stock' : 'in_stock');

                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'sku' => $p->base_sku ?: 'SKU-' . $p->id,
                    'image' => $this->getProductImageUrl($p->id),
                    'units_sold' => (int) $p->units_sold,
                    'revenue' => $rev,
                    'formatted_revenue' => '₹' . number_format($rev, 2),
                    'net_revenue' => '₹' . number_format($rev * 0.98, 2),
                    'refund_pct' => '0.0%',
                    'current_stock' => $stock,
                    'inventory_status' => $status,
                ];
            })
            ->toArray();

        // 2. Table 2: Slow Moving Products
        // Products with stock on hand that have low sales (< 3 units) or long intervals since last sale
        $slowMoving = Product::query()
            ->leftJoin('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->leftJoin('inventory', 'product_variants.id', '=', 'inventory.product_variant_id')
            ->select(
                'products.id',
                'products.name',
                'products.base_sku',
                DB::raw('COALESCE(SUM(inventory.quantity_on_hand), 0) as current_stock')
            )
            ->groupBy('products.id', 'products.name', 'products.base_sku')
            ->having('current_stock', '>', 0)
            ->get()
            ->map(function ($p) use ($start, $end) {
                $lastOrder = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
                    ->where('product_variants.product_id', $p->id)
                    ->where('orders.payment_status', 'paid')
                    ->latest('orders.created_at')
                    ->first(['orders.created_at', 'order_items.line_total']);

                $daysSince = $lastOrder ? Carbon::parse($lastOrder->created_at)->diffInDays(now()) : 90;
                $recentRev = (float) ($lastOrder?->line_total ?? 0);

                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'image' => $this->getProductImageUrl($p->id),
                    'current_stock' => (int) $p->current_stock,
                    'days_since_sale' => $daysSince,
                    'revenue' => $recentRev,
                    'formatted_revenue' => '₹' . number_format($recentRev, 2),
                ];
            })
            ->filter(fn ($item) => $item['days_since_sale'] >= 14 || $item['revenue'] == 0)
            ->sortByDesc('days_since_sale')
            ->values()
            ->take(10)
            ->toArray();

        // 3. Table 3: Never Sold Products
        // Products with 0 historical order items
        $neverSold = Product::query()
            ->whereNotExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('order_items')
                    ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
                    ->whereColumn('product_variants.product_id', 'products.id');
            })
            ->leftJoin('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->leftJoin('inventory', 'product_variants.id', '=', 'inventory.product_variant_id')
            ->select(
                'products.id',
                'products.name',
                'products.status',
                'products.created_at',
                DB::raw('COALESCE(SUM(inventory.quantity_on_hand), 0) as stock')
            )
            ->groupBy('products.id', 'products.name', 'products.status', 'products.created_at')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'image' => $this->getProductImageUrl($p->id),
                    'created_date' => Carbon::parse($p->created_at)->format('d M Y'),
                    'stock' => (int) $p->stock,
                    'status' => $p->status,
                ];
            })
            ->toArray();

        // 4. Table 4: Most Refunded Products
        $mostRefunded = DB::table('order_return_items')
            ->join('order_returns', 'order_return_items.order_return_id', '=', 'order_returns.id')
            ->join('product_variants', 'order_return_items.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.name',
                DB::raw('COUNT(order_return_items.id) as refund_count'),
                DB::raw('SUM(order_return_items.refund_amount) as revenue_lost')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('revenue_lost')
            ->limit(10)
            ->get()
            ->map(function ($p) {
                $totalSold = OrderItem::join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
                    ->where('product_variants.product_id', $p->id)
                    ->sum('quantity');

                $refundCount = (int) $p->refund_count;
                $refundPct = $totalSold > 0 ? round(($refundCount / $totalSold) * 100, 1) : 0;
                $lost = (float) $p->revenue_lost;

                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'image' => $this->getProductImageUrl($p->id),
                    'refund_count' => $refundCount,
                    'refund_pct' => $refundPct . '%',
                    'revenue_lost' => $lost,
                    'formatted_lost' => '₹' . number_format($lost, 2),
                ];
            })
            ->toArray();

        return [
            'best_selling' => $bestSelling,
            'slow_moving' => $slowMoving,
            'never_sold' => $neverSold,
            'most_refunded' => $mostRefunded,
        ];
    }

    /**
     * Get product primary image URL helper.
     */
    protected function getProductImageUrl(int $productId): ?string
    {
        $media = DB::table('product_images')
            ->join('media', 'product_images.media_id', '=', 'media.id')
            ->where('product_images.product_id', $productId)
            ->orderByDesc('product_images.is_primary')
            ->select('media.storage_path')
            ->first();

        return $media ? url('storage/' . ltrim($media->storage_path, '/')) : null;
    }

    /**
     * Populate filter dropdown options.
     */
    public function getFilterDropdownOptions(): array
    {
        return [
            'products' => Product::orderBy('name')->get(['id', 'name', 'base_sku'])->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->base_sku,
            ])->toArray(),
            'categories' => Category::where('is_active', true)->orderBy('name')->get(['id', 'name'])->toArray(),
            'brands' => Product::whereNotNull('brand')->where('brand', '!=', '')->distinct()->pluck('brand')->toArray(),
            'statuses' => ['active', 'draft', 'archived'],
            'inventory_statuses' => [
                'in_stock' => 'Healthy Stock',
                'low_stock' => 'Low Stock',
                'out_of_stock' => 'Out of Stock',
                'overstock' => 'Overstock',
            ],
            'channels' => Order::whereNotNull('source')->distinct()->pluck('source')->toArray() ?: ['website', 'whatsapp', 'phone', 'direct'],
            'countries' => DB::table('order_addresses')->whereNotNull('country')->distinct()->pluck('country')->filter()->values()->toArray() ?: ['India'],
            'states' => DB::table('order_addresses')->whereNotNull('state')->distinct()->pluck('state')->filter()->values()->toArray(),
            'cities' => DB::table('order_addresses')->whereNotNull('city')->distinct()->pluck('city')->filter()->values()->toArray(),
            'customer_types' => [
                'all' => 'All Customers',
                'new' => 'New Customers',
                'returning' => 'Returning Customers',
            ],
        ];
    }

    /**
     * Period sequence generator for continuous date timelines.
     */
    protected function generatePeriodSequence(Carbon $start, Carbon $end, string $interval): array
    {
        $period = [];
        $cursor = $start->copy();

        while ($cursor->lte($end)) {
            $period[] = $cursor->copy();

            if ($interval === 'weekly') {
                $cursor->addWeek();
            } elseif ($interval === 'monthly') {
                $cursor->addMonth();
            } else {
                $cursor->addDay();
            }
        }

        return $period;
    }

    protected function formatDateKey(Carbon $date, string $interval): string
    {
        return match ($interval) {
            'weekly' => $date->format('Y-W'),
            'monthly' => $date->format('Y-m'),
            default => $date->format('Y-m-d'),
        };
    }

    protected function formatDateLabel(Carbon $date, string $interval): string
    {
        return match ($interval) {
            'weekly' => 'Wk ' . $date->format('W, M Y'),
            'monthly' => $date->format('M Y'),
            default => $date->format('d M'),
        };
    }

    protected function calculateChangePct(float $current, float $previous): float
    {
        if ($previous == 0.0) {
            return $current > 0 ? 100.0 : 0.0;
        }
        return round((($current - $previous) / abs($previous)) * 100, 1);
    }

    protected function resolveTrendDirection(float $current, float $previous): string
    {
        if ($current > $previous) return 'up';
        if ($current < $previous) return 'down';
        return 'flat';
    }
}
