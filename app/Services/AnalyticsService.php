<?php

namespace App\Services;

use App\Models\CustomerProfile;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function dashboard(): array
    {
        return [
            'customers' => User::whereHas('roles', fn ($q) => $q->where('code', '!=', 'SUPER_ADMIN'))->count(),
            'orders' => Order::count(),
            'paid_orders' => Order::where('payment_status', 'paid')->count(),
            'gross_sales' => (float) Order::where('payment_status', 'paid')->sum('grand_total'),
            'average_order_value' => (float) Order::where('payment_status', 'paid')->avg('grand_total'),
            'active_resets' => CustomerProfile::whereHas('user.resetProfile', fn ($q) => $q->whereIn('status', ['active','started']))->count(),
            'sales_by_day' => Order::selectRaw('DATE(placed_at) as day, SUM(grand_total) as sales, COUNT(*) as orders')
                ->where('payment_status', 'paid')
                ->whereNotNull('placed_at')
                ->groupByRaw('DATE(placed_at)')
                ->orderBy('day')
                ->limit(31)
                ->get(),
        ];
    }
}
