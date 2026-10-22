<?php
namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\OrderReturnItem;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Models\ResetProfile;
use App\Services\AdminExcelExportService;
use App\Services\InventoryService;
use App\Services\OrderLocationFilterService;
use App\Services\ShipmentService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function __construct(
        protected OrderLocationFilterService $locationFilterService
    ) {}

    /**
     * Build the filtered query used for both listing and exports.
     */
    private function buildFilteredQuery(Request $request): Builder
    {
        $query = Order::with(['user.customerProfile', 'orderItems.productVariant.product', 'payments', 'shippingAddress']);

        // 1. Date filter preset / custom
        $dateRange = $request->query('date_range');
        if ($dateRange === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($dateRange === 'last_7_days') {
            $query->where('created_at', '>=', Carbon::now()->subDays(7)->startOfDay());
        } elseif ($dateRange === 'last_30_days') {
            $query->where('created_at', '>=', Carbon::now()->subDays(30)->startOfDay());
        } elseif ($dateRange === 'custom') {
            if ($request->filled('from_date')) {
                $query->where('created_at', '>=', Carbon::parse($request->from_date)->startOfDay());
            }
            if ($request->filled('to_date')) {
                $query->where('created_at', '<=', Carbon::parse($request->to_date)->endOfDay());
            }
        }

        // 2. Payment Status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // 3. Fulfilment Status (backward compatible with ?status=)
        $fulfilmentStatus = $request->query('fulfilment_status') ?: $request->query('status');
        if ($fulfilmentStatus) {
            $query->where('fulfilment_status', $fulfilmentStatus);
        }

        // 4. Delivery / Shipment Status
        if ($request->filled('shipment_status')) {
            $query->where('shipment_status', $request->shipment_status);
        }

        // 5. Payment Method
        if ($request->filled('payment_method')) {
            $method = $request->payment_method;
            $query->whereHas('payments', function ($pq) use ($method) {
                $pq->where('gateway', $method);
            });
        }

        // 6. Sales Source
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        // 7. Refund Status
        if ($request->filled('refund_status')) {
            $refundStatus = $request->refund_status;
            if ($refundStatus === 'none') {
                $query->where(function ($q) {
                    $q->where('refund_status', 'none')
                      ->orWhereNull('refund_status');
                });
            } else {
                $query->where('refund_status', $refundStatus);
            }
        }

        // 8. Search query (order number, customer name, email, mobile)
        if ($request->filled('search')) {
            $term = trim($request->search);
            $query->where(function ($q) use ($term) {
                $q->where('order_number', 'like', "%{$term}%")
                  ->orWhereHas('user', function ($uq) use ($term) {
                      $uq->where('name', 'like', "%{$term}%")
                         ->orWhere('email', 'like', "%{$term}%")
                         ->orWhere('mobile', 'like', "%{$term}%");
                  });
            });
        }

        // 9. Location Filters (Country, State, City, Area, Postal Code)
        $this->locationFilterService->applyOrderFilters(
            $query,
            $request->only(['country', 'state', 'city', 'area', 'postal_code'])
        );

        return $query->latest('id');
    }

    /**
     * Display a listing of orders with filters and pagination.
     */
    public function index(Request $request)
    {
        $orders = $this->buildFilteredQuery($request)->paginate(25)->withQueryString();

        // Calculate active filter count for the UI badge
        $activeFilters = collect([
            'date_range' => $request->filled('date_range') && $request->date_range !== 'all',
            'from_date' => $request->filled('from_date'),
            'to_date' => $request->filled('to_date'),
            'payment_status' => $request->filled('payment_status'),
            'fulfilment_status' => $request->filled('fulfilment_status') || $request->filled('status'),
            'shipment_status' => $request->filled('shipment_status'),
            'payment_method' => $request->filled('payment_method'),
            'source' => $request->filled('source'),
            'refund_status' => $request->filled('refund_status'),
            'search' => $request->filled('search'),
            'country' => $request->filled('country'),
            'state' => $request->filled('state'),
            'city' => $request->filled('city'),
            'area' => $request->filled('area'),
            'postal_code' => $request->filled('postal_code'),
        ])->filter()->count();

        // Distinct payment methods available in DB
        $paymentMethods = Payment::distinct()->whereNotNull('gateway')->pluck('gateway')->all();
        if (empty($paymentMethods)) {
            $paymentMethods = ['razorpay', 'cod', 'manual'];
        }

        // Location cascading dynamic options
        $locationOptions = $this->locationFilterService->getCascadingOptions(
            $request->only(['country', 'state', 'city', 'area', 'postal_code'])
        );

        // Compute Dynamic KPI metrics tied to filtered orders
        $baseKpiQuery = $this->buildFilteredQuery($request);

        $totalOrders = (clone $baseKpiQuery)->count();
        $grossSales = (float) ((clone $baseKpiQuery)->whereIn('payment_status', ['paid', 'partially_refunded'])->sum('grand_total'));
        $totalRefunds = (float) ((clone $baseKpiQuery)->sum('refund_amount'));
        $netSales = max(0, $grossSales - $totalRefunds);

        $pendingPaymentsCount = (clone $baseKpiQuery)->where('payment_status', 'pending')->count();
        $pendingPaymentsAmount = (float) ((clone $baseKpiQuery)->where('payment_status', 'pending')->sum('grand_total'));

        $pendingFulfillmentCount = (clone $baseKpiQuery)->whereIn('fulfilment_status', ['pending', 'processing', 'unfulfilled'])->count();

        $paidOrdersCount = (clone $baseKpiQuery)->whereIn('payment_status', ['paid', 'partially_refunded'])->count();
        $averageOrderValue = $paidOrdersCount > 0 ? round($grossSales / $paidOrdersCount, 2) : 0.00;

        $refundsCount = (clone $baseKpiQuery)->where(function ($q) {
            $q->where('refund_amount', '>', 0)
              ->orWhereIn('refund_status', ['partially_refunded', 'refunded', 'requested', 'processing', 'partial', 'full']);
        })->count();

        // Active Reset Cycles (filtered cohort if filters applied, else system-wide active)
        $activeCyclesQuery = ResetProfile::whereIn('status', ['created', 'active', 'started', 'in_progress']);
        if ($activeFilters > 0) {
            $cohortUserIds = (clone $baseKpiQuery)->whereNotNull('user_id')->pluck('user_id')->unique();
            $activeResetCycles = $cohortUserIds->isNotEmpty()
                ? (clone $activeCyclesQuery)->whereIn('user_id', $cohortUserIds)->count()
                : 0;
        } else {
            $activeResetCycles = (clone $activeCyclesQuery)->count();
        }

        $kpis = [
            'total_orders' => $totalOrders,
            'net_sales' => $netSales,
            'gross_sales' => $grossSales,
            'pending_payments_count' => $pendingPaymentsCount,
            'pending_payments_amount' => $pendingPaymentsAmount,
            'pending_fulfillment_count' => $pendingFulfillmentCount,
            'average_order_value' => $averageOrderValue,
            'refunds_count' => $refundsCount,
            'refunds_amount' => $totalRefunds,
            'active_reset_cycles' => $activeResetCycles,
        ];

        // AJAX / JSON response for seamless without-page-refresh filtering
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'kpis' => $kpis,
                'active_filters' => $activeFilters,
                'cascading' => $locationOptions,
                'table_html' => view('admin.orders.partials.orders_table', compact('orders', 'activeFilters'))->render(),
                'pagination_html' => view('admin.orders.partials.orders_pagination', compact('orders'))->render(),
                'total_count' => $orders->total(),
            ]);
        }

        return view('admin.orders.index', compact('orders', 'activeFilters', 'paymentMethods', 'kpis', 'locationOptions'));
    }

    /**
     * AJAX endpoint returning cascading location options.
     */
    public function locationsCascading(Request $request)
    {
        $cascading = $this->locationFilterService->getCascadingOptions(
            $request->only(['country', 'state', 'city', 'area', 'postal_code'])
        );

        return response()->json([
            'success' => true,
            'cascading' => $cascading,
        ]);
    }

    /**
     * Export filtered or selected orders to CSV / Excel.
     */
    public function export(Request $request, AdminExcelExportService $exporter)
    {
        $query = $this->buildFilteredQuery($request);

        if ($request->filled('order_ids') && is_array($request->order_ids)) {
            $query->whereIn('id', $request->order_ids);
        }

        $orders = $query->get();

        $headings = [
            'SNO',
            'Order ID',
            'Date',
            'Customer Name',
            'Customer Email',
            'Customer Mobile',
            'Items',
            'Total (₹)',
            'Payment Status',
            'Payment Method',
            'Fulfilment Status',
            'Delivery Status',
            'Sales Source',
            'Refund Status',
            'Refund Amount (₹)'
        ];

        $rows = $orders->map(function (Order $order, int $index) {
            $primaryPayment = $order->payments->first();
            return [
                $index + 1,
                $order->order_number,
                $order->created_at?->format('Y-m-d H:i:s'),
                $order->user?->name ?? 'Customer',
                $order->user?->email,
                $order->user?->mobile,
                $order->orderItems->map(fn($item) => ($item->productVariant?->product?->name ?? 'Product').' (x'.$item->quantity.')')->implode(', '),
                $order->grand_total,
                $order->payment_status,
                $primaryPayment?->gateway ?? 'N/A',
                $order->fulfilment_status,
                $order->shipment_status,
                $order->source ?? 'website',
                $order->refund_status ?? 'none',
                $order->refund_amount ?? 0.00,
            ];
        });

        $filename = 'orders-export-'.now()->format('Y-m-d-His');

        return $exporter->downloadCsv($filename, $headings, $rows);
    }

    /**
     * Update order source tag (AJAX / Form).
     */
    public function updateSource(Request $request, Order $order)
    {
        $data = $request->validate([
            'source' => ['required', 'string', 'max:50'],
        ]);
        $order->update(['source' => $data['source']]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Order source updated.',
                'source' => $order->source,
            ]);
        }

        return back()->with('success', 'Order source updated.');
    }

    /**
     * Quick update of fulfilment / shipment status.
     */
    public function updateFulfilment(Request $request, Order $order, ShipmentService $shipments)
    {
        $data = $request->validate([
            'fulfilment_status' => ['required', 'string', 'in:pending,processing,packed,dispatched,delivered,cancelled'],
            'shipment_status' => ['nullable', 'string', 'in:pending,dispatched,in_transit,out_for_delivery,delivered,cancelled,failed'],
            'courier' => ['nullable', 'string', 'max:100'],
            'tracking_number' => ['nullable', 'string', 'max:100'],
        ]);

        $requiresShipment = in_array($data['fulfilment_status'], ['packed', 'dispatched', 'delivered'], true);
        if ($requiresShipment && $order->payment_status !== 'paid' && $order->payment_status !== 'partially_refunded') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Only paid orders can be packed or dispatched.'], 422);
            }
            return back()->withErrors(['fulfilment_status' => 'Only paid orders can be packed or dispatched.']);
        }

        DB::transaction(function () use ($order, $data, $requiresShipment, $shipments) {
            $oldFulfilment = $order->fulfilment_status;
            $oldShipment = $order->shipment_status;

            $updatePayload = ['fulfilment_status' => $data['fulfilment_status']];
            if (!empty($data['shipment_status'])) {
                $updatePayload['shipment_status'] = $data['shipment_status'];
            } elseif ($data['fulfilment_status'] === 'dispatched') {
                $updatePayload['shipment_status'] = 'in_transit';
            } elseif ($data['fulfilment_status'] === 'delivered') {
                $updatePayload['shipment_status'] = 'delivered';
            }

            $order->update($updatePayload);

            if ($oldFulfilment !== $order->fulfilment_status) {
                $order->statusHistory()->create([
                    'status_type' => 'fulfilment_status',
                    'old_status' => $oldFulfilment,
                    'new_status' => $order->fulfilment_status,
                    'changed_by' => auth()->id(),
                    'remarks' => 'Fulfilment status updated to '.$order->fulfilment_status,
                    'created_at' => now(),
                ]);
            }

            if ($requiresShipment) {
                $shipment = $order->shipments()->first() ?? $shipments->createForOrder($order);

                if (!empty($data['courier'])) {
                    $shipment->courier = $data['courier'];
                }
                if (!empty($data['tracking_number'])) {
                    $shipment->tracking_number = $data['tracking_number'];
                }
                $shipment->save();

                $shipments->recordTracking($shipment, [
                    'status' => $order->shipment_status,
                    'description' => 'Fulfilment stage updated to '.$order->fulfilment_status,
                    'event_time' => now(),
                ]);
            }
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Fulfilment status updated.',
                'fulfilment_status' => $order->fulfilment_status,
                'shipment_status' => $order->shipment_status,
            ]);
        }

        return back()->with('success', 'Order fulfilment status updated.');
    }

    /**
     * Cancel an active order safely.
     */
    public function cancel(Request $request, Order $order, InventoryService $inventory)
    {
        $user = $request->user();
        if (!($user->hasRole('SUPER_ADMIN') || $user->hasPermission('ORDER_CANCEL'))) {
            abort(403, 'You do not have permission to cancel orders.');
        }

        if ($order->fulfilment_status === 'delivered') {
            $msg = 'Delivered orders cannot be cancelled. Use return/refund workflow instead.';
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->withErrors(['error' => $msg]);
        }

        if ($order->fulfilment_status === 'cancelled') {
            $msg = 'Order is already cancelled.';
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('info', $msg);
        }

        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
            'restock' => ['nullable', 'boolean'],
        ]);

        $reason = $data['reason'] ?? 'Cancelled by administrator';

        DB::transaction(function () use ($order, $reason, $data, $inventory) {
            $oldFulfilment = $order->fulfilment_status;
            $oldPayment = $order->payment_status;

            $updatePayload = [
                'fulfilment_status' => 'cancelled',
                'shipment_status' => 'cancelled',
            ];

            if (in_array($oldPayment, ['pending', 'failed'])) {
                $updatePayload['payment_status'] = 'cancelled';
            }

            $order->update($updatePayload);

            $order->statusHistory()->create([
                'status_type' => 'fulfilment_status',
                'old_status' => $oldFulfilment,
                'new_status' => 'cancelled',
                'changed_by' => auth()->id(),
                'remarks' => $reason,
                'created_at' => now(),
            ]);

            if (!empty($data['restock'])) {
                foreach ($order->orderItems as $item) {
                    if ($item->product_variant_id) {
                        try {
                            $inventory->release($item->product_variant_id, $item->quantity, 'order_cancellation', $order->id);
                        } catch (\Throwable $e) {
                            // Inventory might already be consumed or unmanaged
                        }
                    }
                }
            }
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully.',
                'order' => $order->fresh(),
            ]);
        }

        return back()->with('success', 'Order cancelled successfully.');
    }

    /**
     * Initiate and process an order refund.
     */
    public function refund(Request $request, Order $order, InventoryService $inventory)
    {
        $user = $request->user();
        if (!($user->hasRole('SUPER_ADMIN') || $user->hasPermission('ORDER_REFUND'))) {
            abort(403, 'You do not have permission to initiate refunds.');
        }

        if (!in_array($order->payment_status, ['paid', 'partially_refunded'])) {
            $msg = 'Only paid or partially refunded orders can be refunded.';
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->withErrors(['error' => $msg]);
        }

        $refundableBalance = round((float)$order->grand_total - (float)($order->refund_amount ?? 0), 2);
        if ($refundableBalance <= 0) {
            $msg = 'This order has already been fully refunded.';
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->withErrors(['error' => $msg]);
        }

        $data = $request->validate([
            'refund_type' => ['required', 'string', 'in:full,partial'],
            'amount' => ['nullable', 'numeric', 'min:0.01', 'max:'.$refundableBalance],
            'mode' => ['required', 'string', 'in:online,manual'],
            'reason' => ['required', 'string', 'max:500'],
            'restock' => ['nullable', 'boolean'],
        ]);

        $refundAmount = $data['refund_type'] === 'full' ? $refundableBalance : round((float)$data['amount'], 2);
        if ($refundAmount <= 0 || $refundAmount > $refundableBalance) {
            $msg = "Invalid refund amount. Maximum refundable is ₹{$refundableBalance}.";
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->withErrors(['error' => $msg]);
        }

        DB::transaction(function () use ($order, $refundAmount, $data, $inventory) {
            $payment = $order->payments()->latest('id')->first();
            if (!$payment) {
                $payment = $order->payments()->create([
                    'gateway' => 'manual',
                    'amount' => $order->grand_total,
                    'currency' => $order->currency ?: 'INR',
                    'status' => 'paid',
                    'idempotency_key' => (string) Str::uuid(),
                    'paid_at' => $order->created_at,
                ]);
            }

            $gatewayRefId = 'REF-'.strtoupper(Str::random(10));

            $payment->transactions()->create([
                'transaction_type' => 'refund',
                'gateway_transaction_id' => $gatewayRefId,
                'gateway_status' => 'processed',
                'amount' => $refundAmount,
                'currency' => $order->currency ?: 'INR',
                'signature_verified' => true,
                'raw_response' => [
                    'mode' => $data['mode'],
                    'reason' => $data['reason'],
                    'processed_by' => auth()->id(),
                    'refund_type' => $data['refund_type'],
                ],
                'processed_at' => now(),
            ]);

            $newRefundAmount = round((float)($order->refund_amount ?? 0) + $refundAmount, 2);
            $isFullyRefunded = $newRefundAmount >= round((float)$order->grand_total, 2);

            $newRefundStatus = $isFullyRefunded ? 'refunded' : 'partially_refunded';
            $newPaymentStatus = $isFullyRefunded ? 'refunded' : 'partially_refunded';

            $order->update([
                'refund_amount' => $newRefundAmount,
                'refund_status' => $newRefundStatus,
                'payment_status' => $newPaymentStatus,
            ]);

            // AUTOMATION: Release / restore inventory if restock requested
            if (!empty($data['restock'])) {
                foreach ($order->orderItems as $item) {
                    if ($item->product_variant_id) {
                        try {
                            $inventory->release($item->product_variant_id, $item->quantity, 'order_refund', $order->id);
                        } catch (\Throwable $e) {
                            // Inventory might not be managed for variant
                        }
                    }
                }
            }

            $order->statusHistory()->create([
                'status_type' => 'payment_status',
                'old_status' => $order->payment_status,
                'new_status' => $newPaymentStatus,
                'changed_by' => auth()->id(),
                'remarks' => "Refund of ₹{$refundAmount} processed ({$data['mode']})" . (!empty($data['restock']) ? " [Items Restocked]" : "") . ": {$data['reason']}",
                'created_at' => now(),
            ]);
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Refund of ₹{$refundAmount} processed successfully.",
                'order' => $order->fresh(),
            ]);
        }

        return back()->with('success', "Refund of ₹{$refundAmount} processed successfully.");
    }

    /**
     * Handle safe bulk actions across selected orders.
     */
    public function bulkAction(Request $request, AdminExcelExportService $exporter, ShipmentService $shipments)
    {
        $data = $request->validate([
            'action' => ['required', 'string', 'in:update_fulfilment,update_source,export'],
            'order_ids' => ['required', 'array', 'min:1'],
            'order_ids.*' => ['integer', 'exists:orders,id'],
            'fulfilment_status' => ['nullable', 'string', 'in:pending,processing,packed,dispatched,delivered'],
            'source' => ['nullable', 'string', 'max:50'],
        ]);

        $orderIds = $data['order_ids'];

        if ($data['action'] === 'export') {
            return $this->export($request, $exporter);
        }

        if ($data['action'] === 'update_fulfilment') {
            $newStatus = $data['fulfilment_status'];
            if (!$newStatus) {
                return back()->withErrors(['error' => 'Please select a fulfilment status for bulk update.']);
            }

            $count = 0;
            DB::transaction(function () use ($orderIds, $newStatus, $shipments, &$count) {
                $orders = Order::whereIn('id', $orderIds)->get();
                foreach ($orders as $order) {
                    $oldStatus = $order->fulfilment_status;
                    $order->update(['fulfilment_status' => $newStatus]);

                    if ($oldStatus !== $newStatus) {
                        $order->statusHistory()->create([
                            'status_type' => 'fulfilment_status',
                            'old_status' => $oldStatus,
                            'new_status' => $newStatus,
                            'changed_by' => auth()->id(),
                            'remarks' => 'Bulk fulfilment update to '.$newStatus,
                            'created_at' => now(),
                        ]);
                    }

                    if (in_array($newStatus, ['packed', 'dispatched', 'delivered'])) {
                        $shipment = $order->shipments()->first() ?? $shipments->createForOrder($order);
                        $shipments->recordTracking($shipment, [
                            'status' => $newStatus === 'dispatched' ? 'in_transit' : $newStatus,
                            'description' => 'Bulk update to '.$newStatus,
                            'event_time' => now(),
                        ]);
                    }
                    $count++;
                }
            });

            return back()->with('success', "Successfully updated fulfilment status for {$count} order(s).");
        }

        if ($data['action'] === 'update_source') {
            $newSource = $data['source'];
            if (!$newSource) {
                return back()->withErrors(['error' => 'Please select a source for bulk update.']);
            }

            Order::whereIn('id', $orderIds)->update(['source' => $newSource]);

            return back()->with('success', "Successfully updated sales source for ".count($orderIds)." order(s).");
        }

        return back();
    }

    /**
     * Show single order details.
     */
    public function show(Order $order)
    {
        $order->load([
            'coupon',
            'user.customerProfile',
            'orderItems.productVariant.product',
            'orderItems.productVariant.images.media',
            'payments.transactions',
            'shipments.trackingEvents',
            'statusHistory.changedby',
            'returns.items.orderItem.productVariant',
            'returns.processor',
            'orderAddresses',
        ]);

        $totalMrp = $order->orderItems->sum(function ($item) {
            return (float)($item->mrp_snapshot ?: $item->unit_price) * $item->quantity;
        });
        $totalSavings = max(0, $totalMrp - (float)$order->subtotal + (float)$order->discount_amount);

        $customerStats = [
            'totalOrders' => $order->user_id ? Order::where('user_id', $order->user_id)->count() : 1,
            'lifetimeSpend' => $order->user_id
                ? (float) Order::where('user_id', $order->user_id)->whereIn('payment_status', ['paid', 'partially_refunded'])->sum('grand_total')
                : (float) $order->grand_total,
            'customerSince' => $order->user?->created_at ?? $order->created_at,
        ];

        return view('admin.orders.show', compact('order', 'customerStats', 'totalMrp', 'totalSavings'));
    }

    /**
     * Mark an order as paid.
     */
    public function markAsPaid(Request $request, Order $order)
    {
        if ($order->payment_status === 'paid') {
            return back()->with('info', 'Order is already marked as paid.');
        }

        DB::transaction(function () use ($order) {
            $oldStatus = $order->payment_status;
            $order->update(['payment_status' => 'paid']);

            $payment = $order->payments()->latest('id')->first();
            if ($payment) {
                $payment->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);
            } else {
                $order->payments()->create([
                    'gateway' => 'manual',
                    'amount' => $order->grand_total,
                    'currency' => $order->currency ?: 'INR',
                    'status' => 'paid',
                    'idempotency_key' => (string) Str::uuid(),
                    'paid_at' => now(),
                ]);
            }

            $order->statusHistory()->create([
                'status_type' => 'payment_status',
                'old_status' => $oldStatus,
                'new_status' => 'paid',
                'changed_by' => auth()->id(),
                'remarks' => 'Order marked as paid by administrator',
                'created_at' => now(),
            ]);

            // AUTOMATION: Reduce Inventory on Paid Order
            app(\App\Services\InventoryService::class)->reduceStockForOrder($order);
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Order marked as paid successfully.',
                'order' => $order->fresh(),
            ]);
        }

        return back()->with('success', 'Order marked as paid successfully.');
    }

    /**
     * Add an internal admin note to the order.
     */
    public function addNote(Request $request, Order $order)
    {
        $data = $request->validate([
            'note' => ['required', 'string', 'max:1000'],
        ]);

        $order->statusHistory()->create([
            'status_type' => 'internal_note',
            'old_status' => null,
            'new_status' => 'note_added',
            'changed_by' => auth()->id(),
            'remarks' => trim($data['note']),
            'created_at' => now(),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Internal note added.',
            ]);
        }

        return back()->with('success', 'Internal note added successfully.');
    }

    /**
     * Update order shipping and/or billing address.
     */
    public function updateAddress(Request $request, Order $order, AddressService $addressService)
    {
        $data = $request->validate([
            'type' => ['required', 'string', 'in:shipping,billing,both'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'landmark' => ['nullable', 'string', 'max:150'],
            'area' => ['nullable', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'delivery_instructions' => ['nullable', 'string', 'max:500'],
            'address_type' => ['nullable', 'string', 'in:home,office,other'],
        ]);

        $addressPayload = [
            'full_name' => $data['recipient_name'],
            'recipient_name' => $data['recipient_name'],
            'phone' => $data['phone'] ?? '',
            'email' => $data['email'] ?? null,
            'address_line1' => $data['address_line1'],
            'address_line2' => $data['address_line2'] ?? '',
            'landmark' => $data['landmark'] ?? null,
            'area' => $data['area'] ?? null,
            'city' => $data['city'],
            'district' => $data['district'] ?? null,
            'state' => $data['state'],
            'postal_code' => $data['postal_code'],
            'country' => $data['country'] ?? 'India',
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'delivery_instructions' => $data['delivery_instructions'] ?? null,
            'address_type' => $data['address_type'] ?? 'home',
        ];

        if ($data['type'] === 'shipping' || $data['type'] === 'both') {
            $addressService->updateOrderAddress($order, $addressPayload, 'shipping');
        }
        if ($data['type'] === 'billing' || $data['type'] === 'both') {
            $addressService->updateOrderAddress($order, $addressPayload, 'billing');
        }

        $order->statusHistory()->create([
            'status_type' => 'address_update',
            'old_status' => null,
            'new_status' => 'address_modified',
            'changed_by' => auth()->id(),
            'remarks' => ucfirst($data['type']).' address updated by administrator',
            'created_at' => now(),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Address updated successfully.',
                'order' => $order->fresh(['orderAddresses']),
            ]);
        }

        return back()->with('success', 'Address updated successfully.');
    }

    /**
     * Legacy update endpoint.
     */
    public function update(Request $request, Order $order, ShipmentService $shipments)
    {
        $data = $request->validate([
            'payment_status' => ['required', 'string', 'max:30'],
            'fulfilment_status' => ['required', 'string', 'max:30'],
            'shipment_status' => ['required', 'string', 'max:30'],
            'source' => ['nullable', 'string', 'max:50'],
        ]);
        $requiresShipment = in_array($data['shipment_status'], ['packed', 'dispatched', 'in_transit', 'out_for_delivery', 'delivered'], true);

        if ($requiresShipment && $data['payment_status'] !== 'paid') {
            return back()->withErrors(['payment_status' => 'Only paid orders can be sent to fulfilment or dispatched.']);
        }

        DB::transaction(function () use ($order, $data, $requiresShipment, $shipments) {
            $old = $order->only(array_keys($data));
            $order->update($data);
            foreach ($data as $key => $new) {
                if (($old[$key] ?? null) !== $new) {
                    $order->statusHistory()->create([
                        'status_type' => $key,
                        'old_status' => $old[$key] ?? null,
                        'new_status' => $new,
                        'changed_by' => auth()->id(),
                        'remarks' => 'Admin status update',
                        'created_at' => now(),
                    ]);
                }
            }

            if ($data['payment_status'] === 'paid' && ($requiresShipment || !$order->shipments()->exists())) {
                $shipment = $shipments->createForOrder($order);

                if ($requiresShipment) {
                    $shipments->recordTracking($shipment, [
                        'status' => $data['shipment_status'],
                        'description' => 'Status updated from the order dashboard',
                        'event_time' => now(),
                    ]);
                }
            }
        });
        return back()->with('success', 'Order status updated.');
    }

    /**
     * Printable invoice view for an order.
     */
    public function invoice(Order $order)
    {
        $order->load([
            'user.customerProfile',
            'orderItems.productVariant.product',
            'payments.transactions',
            'shipments',
        ]);

        return view('admin.orders.invoice', compact('order'));
    }

    /**
     * Record a customer return for an order.
     */
    public function storeReturn(Request $request, Order $order)
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:requested,approved,in_transit,received,inspected,completed,rejected'],
            'outcome' => ['required', 'string', 'in:pending,refunded,replaced,rejected'],
            'refund_amount' => ['nullable', 'numeric', 'min:0', 'max:'.$order->grand_total],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.order_item_id' => ['required', 'integer', 'exists:order_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.condition' => ['nullable', 'string', 'max:50'],
            'items.*.reason' => ['nullable', 'string', 'max:255'],
        ]);

        $refundAmount = round((float)($data['refund_amount'] ?? 0), 2);

        DB::transaction(function () use ($order, $data, $refundAmount) {
            $returnNumber = 'RET-'.date('ymd').'-'.strtoupper(Str::random(4));

            $orderReturn = $order->returns()->create([
                'return_number' => $returnNumber,
                'reason' => $data['reason'],
                'status' => $data['status'],
                'outcome' => $data['outcome'],
                'refund_amount' => $refundAmount,
                'admin_notes' => $data['admin_notes'] ?? null,
                'processed_by' => auth()->id(),
                'requested_at' => now(),
                'processed_at' => in_array($data['status'], ['completed', 'rejected']) ? now() : null,
            ]);

            foreach ($data['items'] as $itemData) {
                $orderItem = $order->orderItems()->find($itemData['order_item_id']);
                if ($orderItem) {
                    $orderReturn->items()->create([
                        'order_item_id' => $orderItem->id,
                        'product_variant_id' => $orderItem->product_variant_id,
                        'quantity' => min($itemData['quantity'], $orderItem->quantity),
                        'refund_amount' => $refundAmount > 0 ? $refundAmount : 0.00,
                        'reason' => $itemData['reason'] ?? $data['reason'],
                        'condition' => $itemData['condition'] ?? 'unopened',
                    ]);
                }
            }

            if ($data['outcome'] === 'refunded' && $refundAmount > 0) {
                $newRefundAmount = round((float)($order->refund_amount ?? 0) + $refundAmount, 2);
                $isFullyRefunded = $newRefundAmount >= round((float)$order->grand_total, 2);

                $order->update([
                    'refund_amount' => $newRefundAmount,
                    'refund_status' => $isFullyRefunded ? 'refunded' : 'partially_refunded',
                    'payment_status' => $isFullyRefunded ? 'refunded' : 'partially_refunded',
                ]);
            }

            $order->statusHistory()->create([
                'status_type' => 'order_return',
                'old_status' => null,
                'new_status' => $data['status'],
                'changed_by' => auth()->id(),
                'remarks' => "Return #{$returnNumber} recorded ({$data['reason']}) - Outcome: {$data['outcome']}",
                'created_at' => now(),
            ]);

            // AUTOMATION: Restore stock if return is approved/refunded
            if (in_array($data['status'], ['approved', 'completed']) || $data['outcome'] === 'refunded') {
                app(\App\Services\InventoryService::class)->restoreStockForReturn($orderReturn);
            }
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Order return recorded successfully.',
                'order' => $order->fresh(['returns.items', 'statusHistory']),
            ]);
        }

        return back()->with('success', 'Order return recorded successfully.');
    }

    /**
     * Update status or outcome of an existing order return.
     */
    public function updateReturn(Request $request, Order $order, OrderReturn $return)
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:requested,approved,in_transit,received,inspected,completed,rejected'],
            'outcome' => ['required', 'string', 'in:pending,refunded,replaced,rejected'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $return->update([
            'status' => $data['status'],
            'outcome' => $data['outcome'],
            'admin_notes' => $data['admin_notes'] ?? $return->admin_notes,
            'processed_by' => auth()->id(),
            'processed_at' => in_array($data['status'], ['completed', 'rejected']) ? now() : $return->processed_at,
        ]);

        $order->statusHistory()->create([
            'status_type' => 'return_status',
            'old_status' => $return->status,
            'new_status' => $data['status'],
            'changed_by' => auth()->id(),
            'remarks' => "Return #{$return->return_number} status updated to {$data['status']} ({$data['outcome']})",
            'created_at' => now(),
        ]);

        return back()->with('success', "Return #{$return->return_number} updated.");
    }
}
