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
     * Sales Analytics.
     */
    public function sales(Request $request)
    {
        $days = (int) $request->get('days', 30);
        $startDate = now()->subDays($days)->startOfDay();

        $grossSales = (float) Order::where('created_at', '>=', $startDate)->sum('grand_total');
        $netSales = (float) Order::where('payment_status', 'paid')->where('created_at', '>=', $startDate)->sum('grand_total')
            - (float) Order::where('created_at', '>=', $startDate)->sum('refund_amount');

        $topProducts = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'paid')
            ->where('orders.created_at', '>=', $startDate)
            ->select(
                'order_items.product_name_snapshot',
                'order_items.sku_snapshot',
                DB::raw('SUM(order_items.quantity) as units_sold'),
                DB::raw('SUM(order_items.line_total) as total_revenue')
            )
            ->groupBy('order_items.product_name_snapshot', 'order_items.sku_snapshot')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        $paymentGateways = Payment::where('created_at', '>=', $startDate)
            ->select(
                DB::raw("COALESCE(gateway, 'manual') as gateway"),
                DB::raw('COUNT(id) as count'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('gateway')
            ->get();

        return view('admin.analytics.sales', compact(
            'grossSales',
            'netSales',
            'topProducts',
            'paymentGateways',
            'days'
        ));
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
