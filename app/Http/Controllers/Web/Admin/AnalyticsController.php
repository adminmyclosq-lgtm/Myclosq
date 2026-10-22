<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function __construct(
        protected \App\Services\SalesAnalyticsService $salesAnalyticsService,
        protected \App\Services\ProductAnalyticsService $productAnalyticsService,
        protected \App\Services\AdminExcelExportService $excelExportService
    ) {}

    /**
     * Business Overview Dashboard.
     */
    public function overview(Request $request)
    {
        $days = (int) $request->get('days', 30);
        $startDate = now()->subDays($days)->startOfDay();

        $totalRevenue = (float) Order::where('payment_status', 'paid')
            ->where('created_at', '>=', $startDate)
            ->sum('grand_total');

        $totalOrders = Order::where('created_at', '>=', $startDate)->count();

        $paidOrders = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', $startDate)
            ->count();

        $aov = $paidOrders > 0 ? round($totalRevenue / $paidOrders, 2) : 0;

        $newCustomers = User::where('created_at', '>=', $startDate)->count();

        $refundAmount = (float) Order::where('created_at', '>=', $startDate)->sum('refund_amount');

        // Recent performance sparkline data
        $dailySales = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subDays(14))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(grand_total) as daily_total'),
                DB::raw('COUNT(id) as orders_count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.analytics.overview', compact(
            'totalRevenue',
            'totalOrders',
            'paidOrders',
            'aov',
            'newCustomers',
            'refundAmount',
            'dailySales',
            'days'
        ));
    }

    /**
     * Enterprise Sales Analytics Dashboard.
     */
    public function sales(Request $request)
    {
        $filters = $request->all();
        if (empty($filters['date_preset'])) {
            $filters['date_preset'] = 'last_30_days';
        }

        $dashboardData = $this->salesAnalyticsService->getDashboardData($filters);
        $filterOptions = $this->salesAnalyticsService->getFilterDropdownOptions();

        return view('admin.analytics.sales', compact(
            'dashboardData',
            'filterOptions',
            'filters'
        ));
    }

    /**
     * AJAX endpoint for live BI dashboard filter updates.
     */
    public function salesData(Request $request)
    {
        $filters = $request->all();
        $skipCache = $request->boolean('refresh', false);

        $data = $this->salesAnalyticsService->getDashboardData($filters, $skipCache);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Export Sales Analytics table data to CSV / Excel.
     */
    public function salesExport(Request $request)
    {
        $filters = $request->all();
        $data = $this->salesAnalyticsService->getDashboardData($filters);
        $tableData = $data['sales_table'] ?? [];
        $format = strtolower($request->get('format', 'csv'));

        $headings = [
            'Product Name',
            'SKU',
            'Units Sold',
            'Revenue (INR)',
            'Average Selling Price (INR)',
            'Orders Count',
            'Refund Amount (INR)',
            'Growth Rate (%)',
            'Catalog Share (%)',
        ];

        $rows = array_map(function ($item) {
            return [
                $item['product'] ?? '',
                $item['sku'] ?? '',
                $item['units_sold'] ?? 0,
                $item['revenue'] ?? 0,
                $item['average_price'] ?? 0,
                $item['orders'] ?? 0,
                $item['refunds'] ?? 0,
                ($item['growth'] ?? 0) . '%',
                ($item['conversion_share'] ?? 0) . '%',
            ];
        }, $tableData);

        $filename = 'sales_analytics_' . now()->format('Y_m_d_His');

        if ($format === 'excel' || $format === 'xls') {
            return $this->excelExportService->download($filename, $headings, $rows);
        }

        return $this->excelExportService->downloadCsv($filename, $headings, $rows);
    }

    /**
     * Enterprise Product Analytics Dashboard.
     */
    public function products(Request $request)
    {
        $filters = $request->all();
        if (empty($filters['date_preset'])) {
            $filters['date_preset'] = 'last_30_days';
        }

        $dashboardData = $this->productAnalyticsService->getDashboardData($filters);
        $filterOptions = $this->productAnalyticsService->getFilterDropdownOptions();

        return view('admin.analytics.products', compact(
            'dashboardData',
            'filterOptions',
            'filters'
        ));
    }

    /**
     * AJAX endpoint for live Product Analytics filter updates.
     */
    public function productsData(Request $request)
    {
        $filters = $request->all();
        $skipCache = $request->boolean('refresh', false);

        $data = $this->productAnalyticsService->getDashboardData($filters, $skipCache);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Export Product Analytics report to CSV / Excel.
     */
    public function productsExport(Request $request)
    {
        $filters = $request->all();
        $data = $this->productAnalyticsService->getDashboardData($filters);
        $tableData = $data['tables']['best_selling'] ?? [];
        $format = strtolower($request->get('format', 'csv'));

        $headings = [
            'Product Name',
            'SKU',
            'Units Sold',
            'Gross Revenue (INR)',
            'Net Revenue (INR)',
            'Refund Rate',
            'Current Stock',
            'Inventory Status',
        ];

        $rows = array_map(function ($item) {
            return [
                $item['name'] ?? '',
                $item['sku'] ?? '',
                $item['units_sold'] ?? 0,
                $item['revenue'] ?? 0,
                str_replace(['₹', ','], '', $item['net_revenue'] ?? '0'),
                $item['refund_pct'] ?? '0.0%',
                $item['current_stock'] ?? 0,
                ucfirst(str_replace('_', ' ', $item['inventory_status'] ?? 'in_stock')),
            ];
        }, $tableData);

        $filename = 'product_analytics_' . now()->format('Y_m_d_His');

        if ($format === 'excel' || $format === 'xls') {
            return $this->excelExportService->download($filename, $headings, $rows);
        }

        return $this->excelExportService->downloadCsv($filename, $headings, $rows);
    }

    /**
     * Customer Analytics.
     */
    public function customers(Request $request)
    {
        $totalUsers = User::count();
        $orderingUsers = Order::distinct('user_id')->whereNotNull('user_id')->count('user_id');
        $repeatCustomers = DB::table('orders')
            ->select('user_id')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(id) > 1')
            ->get()
            ->count();

        $repeatRate = $orderingUsers > 0 ? round(($repeatCustomers / $orderingUsers) * 100, 1) : 0;

        $topCustomers = User::withCount('orders')
            ->withSum(['orders' => fn ($q) => $q->where('payment_status', 'paid')], 'grand_total')
            ->orderByDesc('orders_sum_grand_total')
            ->limit(15)
            ->get();

        return view('admin.analytics.customers', compact(
            'totalUsers',
            'orderingUsers',
            'repeatCustomers',
            'repeatRate',
            'topCustomers'
        ));
    }

    /**
     * Marketing Analytics.
     */
    public function marketing(Request $request)
    {
        $couponStats = DB::table('coupon_usages')
            ->join('coupons', 'coupon_usages.coupon_id', '=', 'coupons.id')
            ->select(
                'coupons.code',
                DB::raw('COUNT(coupon_usages.id) as uses_count'),
                DB::raw('SUM(coupon_usages.discount_amount) as total_discount')
            )
            ->groupBy('coupons.code')
            ->orderByDesc('uses_count')
            ->limit(10)
            ->get();

        $sourceBreakup = Order::select(
            DB::raw("COALESCE(source, 'website') as source"),
            DB::raw('COUNT(id) as orders_count'),
            DB::raw('SUM(grand_total) as total_revenue')
        )
            ->groupBy('source')
            ->orderByDesc('orders_count')
            ->get();

        return view('admin.analytics.marketing', compact('couponStats', 'sourceBreakup'));
    }
}
