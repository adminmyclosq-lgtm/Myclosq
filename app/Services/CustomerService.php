<?php

namespace App\Services;

use App\Models\CustomerAddress;
use App\Models\CustomerNote;
use App\Models\CustomerProfile;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReturn;
use App\Models\Payment;
use App\Models\Role;
use App\Models\Shipment;
use App\Models\User;
use App\Models\WhatsappContact;
use App\Models\WhatsappMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CustomerService
{
    /**
     * Retrieve paginated customers with 6 KPI cards, advanced filters, and aggregated metrics.
     */
    public function getCustomerList(Request $request): array
    {
        // Base query: all users that are customers (having CUSTOMER role or orders)
        $query = User::query()
            ->where(function ($q) {
                $q->whereHas('roles', fn ($r) => $r->where('code', 'CUSTOMER'))
                  ->orWhereHas('orders');
            })
            ->with(['customerProfile', 'defaultShippingAddress', 'whatsappContact']);

        // Aggregated subqueries for lifetime spend, orders count, and last order
        $ordersSub = DB::table('orders')
            ->select(
                'user_id',
                DB::raw('COUNT(id) as total_orders_count'),
                DB::raw('SUM(CASE WHEN payment_status = "paid" THEN grand_total ELSE 0 END) as lifetime_spend_computed'),
                DB::raw('MAX(created_at) as last_order_date')
            )
            ->groupBy('user_id');

        $query->leftJoinSub($ordersSub, 'order_stats', function ($join) {
            $join->on('users.id', '=', 'order_stats.user_id');
        });

        // 1. Text Search: Name, Email, Phone
        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('users.email', 'like', "%{$search}%")
                  ->orWhere('users.mobile', 'like', "%{$search}%")
                  ->orWhereHas('customerProfile', function ($qp) use ($search) {
                      $qp->where('display_name', 'like', "%{$search}%")
                         ->orWhere('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('addresses', function ($qa) use ($search) {
                      $qa->where('recipient_name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        // 2. Status Filter
        if ($status = $request->input('status')) {
            $query->where('users.status', $status);
        }

        // 3. Location Filters: Country, State, City
        if ($country = $request->input('country')) {
            $query->whereHas('addresses', fn ($q) => $q->where('country', $country));
        }

        if ($state = $request->input('state')) {
            $query->whereHas('addresses', fn ($q) => $q->where('state', $state));
        }

        if ($city = $request->input('city')) {
            $query->whereHas('addresses', fn ($q) => $q->where('city', $city));
        }

        // 4. Order Count Filter
        if ($orderTier = $request->input('order_count')) {
            if ($orderTier === '0') {
                $query->where(fn ($q) => $q->whereNull('order_stats.total_orders_count')->orWhere('order_stats.total_orders_count', 0));
            } elseif ($orderTier === '1') {
                $query->where('order_stats.total_orders_count', 1);
            } elseif ($orderTier === '2-5') {
                $query->whereBetween('order_stats.total_orders_count', [2, 5]);
            } elseif ($orderTier === '5+') {
                $query->where('order_stats.total_orders_count', '>', 5);
            }
        }

        // 5. Lifetime Spend Filter
        if ($spendTier = $request->input('lifetime_spend')) {
            if ($spendTier === '0-1000') {
                $query->whereBetween('order_stats.lifetime_spend_computed', [0, 1000]);
            } elseif ($spendTier === '1000-5000') {
                $query->whereBetween('order_stats.lifetime_spend_computed', [1000, 5000]);
            } elseif ($spendTier === '5000+') {
                $query->where('order_stats.lifetime_spend_computed', '>=', 5000);
            }
        }

        // 6. Tag Filter
        if ($tag = $request->input('tag')) {
            $query->whereHas('customerProfile', fn ($q) => $q->where('tags', 'like', "%{$tag}%"));
        }

        // 7. VIP Filter
        if ($request->boolean('vip')) {
            $query->where(function ($q) {
                $q->whereHas('customerProfile', fn ($qp) => $qp->where('is_vip', true)->orWhere('tags', 'like', '%VIP%'))
                  ->orWhere('order_stats.lifetime_spend_computed', '>=', 5000);
            });
        }

        // 8. Sorting
        $sortField = $request->input('sort', 'users.id');
        $sortDirection = $request->input('direction', 'desc');

        if ($sortField === 'spend') {
            $query->orderBy('order_stats.lifetime_spend_computed', $sortDirection);
        } elseif ($sortField === 'orders') {
            $query->orderBy('order_stats.total_orders_count', $sortDirection);
        } elseif ($sortField === 'last_order') {
            $query->orderBy('order_stats.last_order_date', $sortDirection);
        } else {
            $query->orderBy('users.id', $sortDirection);
        }

        // Select columns and paginate
        $customers = $query->select(
            'users.*',
            DB::raw('COALESCE(order_stats.total_orders_count, 0) as total_orders'),
            DB::raw('COALESCE(order_stats.lifetime_spend_computed, 0) as lifetime_spend'),
            'order_stats.last_order_date'
        )->paginate(15)->withQueryString();

        // 9. Compute the 6 Enterprise KPI Cards
        $kpis = $this->calculateCustomerKpis();

        // 10. Distinct values for filter menus
        $filterOptions = [
            'countries' => CustomerAddress::whereNotNull('country')->where('country', '!=', '')->distinct()->pluck('country'),
            'states' => CustomerAddress::whereNotNull('state')->where('state', '!=', '')->distinct()->pluck('state'),
            'cities' => CustomerAddress::whereNotNull('city')->where('city', '!=', '')->distinct()->pluck('city'),
            'tags' => CustomerProfile::whereNotNull('tags')->pluck('tags')->flatMap(function ($tagString) {
                return array_filter(array_map('trim', explode(',', (string) $tagString)));
            })->unique()->values(),
        ];

        return [
            'customers' => $customers,
            'kpis' => $kpis,
            'filterOptions' => $filterOptions,
            'filters' => $request->only(['search', 'status', 'country', 'state', 'city', 'order_count', 'lifetime_spend', 'tag', 'vip']),
        ];
    }

    /**
     * Compute 6 Master Customer KPI Cards
     */
    public function calculateCustomerKpis(): array
    {
        $baseCustomers = User::query()
            ->where(function ($q) {
                $q->whereHas('roles', fn ($r) => $r->where('code', 'CUSTOMER'))
                  ->orWhereHas('orders');
            });

        $totalCustomers = (clone $baseCustomers)->count();

        $activeCustomers = (clone $baseCustomers)
            ->where('status', 'active')
            ->count();

        $thirtyDaysAgo = Carbon::now()->subDays(30);
        $newCustomers = (clone $baseCustomers)
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->count();

        // Returning Customers: > 1 orders
        $returningCustomers = DB::table('orders')
            ->select('user_id')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(id) > 1')
            ->get()
            ->count();

        // VIP Customers: is_vip = true OR tags like VIP OR spend >= 5000
        $vipProfileUserIds = CustomerProfile::where('is_vip', true)
            ->orWhere('tags', 'like', '%VIP%')
            ->pluck('user_id')
            ->toArray();

        $highSpendUserIds = DB::table('orders')
            ->select('user_id')
            ->where('payment_status', 'paid')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->havingRaw('SUM(grand_total) >= 5000')
            ->pluck('user_id')
            ->toArray();

        $vipCustomers = count(array_unique(array_merge($vipProfileUserIds, $highSpendUserIds)));

        // WhatsApp Opt-ins
        $optInViaProfile = CustomerProfile::where('whatsapp_opt_in', true)->count();
        $optInViaContact = WhatsappContact::where('opt_in', true)->count();
        $whatsappOptIns = max($optInViaProfile, $optInViaContact);

        return [
            'total_customers' => $totalCustomers,
            'active_customers' => $activeCustomers,
            'new_customers' => $newCustomers,
            'returning_customers' => $returningCustomers,
            'vip_customers' => $vipCustomers,
            'whatsapp_opt_ins' => $whatsappOptIns,
        ];
    }

    /**
     * Retrieve 360-degree Customer Profile with all 8 live tabs.
     */
    public function getCustomerDetails(User $customer): array
    {
        // Eager load core customer relations
        $customer->load([
            'customerProfile',
            'customerAddresses',
            'addresses',
            'defaultShippingAddress',
            'whatsappContact.messages',
            'customerNotes.author',
        ]);

        $customerId = $customer->id;

        // 1. Orders Tab Data: All customer orders
        $orders = Order::where('user_id', $customerId)
            ->with(['items.productVariant.product', 'payments', 'shipments', 'shippingAddress'])
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'orders_page');

        $orderIds = Order::where('user_id', $customerId)->pluck('id')->toArray();

        // 2. Financial & Purchasing Overview Aggregations
        $paidOrders = Order::where('user_id', $customerId)
            ->where('payment_status', 'paid')
            ->get();

        $totalOrdersCount = Order::where('user_id', $customerId)->count();
        $lifetimeSpend = (float) $paidOrders->sum('grand_total');
        $averageOrderValue = $paidOrders->count() > 0 ? ($lifetimeSpend / $paidOrders->count()) : 0.0;

        $lastOrder = Order::where('user_id', $customerId)->latest('created_at')->first();

        // 3. Products Purchased Tab Data: Aggregated breakdown
        $productsPurchased = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->where('orders.user_id', $customerId)
            ->where('orders.payment_status', '!=', 'failed')
            ->groupBy('products.id', 'products.name', 'products.base_sku', 'product_variants.name', 'product_variants.sku')
            ->select(
                'products.id as product_id',
                'products.name as product_name',
                'products.base_sku',
                'product_variants.name as variant_name',
                'product_variants.sku as variant_sku',
                DB::raw('SUM(order_items.quantity) as total_units_bought'),
                DB::raw('SUM(order_items.line_total) as total_spent'),
                DB::raw('AVG(order_items.unit_price) as avg_unit_price'),
                DB::raw('MAX(orders.created_at) as last_purchased_date'),
                DB::raw('COUNT(DISTINCT orders.id) as orders_count')
            )
            ->orderByDesc('total_spent')
            ->get();

        $totalUnitsPurchased = (int) $productsPurchased->sum('total_units_bought');

        // 4. Addresses Tab Data
        $addresses = $customer->customerAddresses;
        if ($addresses->isEmpty() && $customer->addresses->isNotEmpty()) {
            $addresses = $customer->addresses;
        }

        // Preferred Shipping Address
        $preferredAddress = $customer->defaultShippingAddress ?: $addresses->first();

        // 5. Payments Tab Data: All customer payment transactions
        $payments = Payment::whereIn('order_id', $orderIds)
            ->with(['order'])
            ->orderByDesc('created_at')
            ->get();

        // Any returns or refunds linked to this customer's orders
        $orderReturns = OrderReturn::whereIn('order_id', $orderIds)
            ->with('order')
            ->orderByDesc('created_at')
            ->get();

        // Preferred Payment Gateway
        $preferredPaymentGateway = DB::table('payments')
            ->whereIn('order_id', $orderIds)
            ->where('status', 'success')
            ->select('gateway', DB::raw('COUNT(id) as usage_count'), DB::raw('SUM(amount) as total_gateway_amount'))
            ->groupBy('gateway')
            ->orderByDesc('usage_count')
            ->first();

        // 6. Shipping Tab Data: All shipments
        $shipments = Shipment::whereIn('order_id', $orderIds)
            ->with(['order.shippingAddress'])
            ->orderByDesc('created_at')
            ->get();

        // 7. Notes Tab Data: CRM Notes
        $notes = CustomerNote::where('customer_id', $customerId)
            ->with('author')
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->get();

        // 8. Timeline Tab Data: Chronological 360-degree activity feed
        $timeline = $this->buildCustomerTimeline($customer, $orders, $payments, $shipments, $orderReturns, $notes);

        // Tags parsing
        $tags = $customer->customerProfile?->tags_array ?? [];

        return [
            'customer' => $customer,
            'metrics' => [
                'lifetime_spend' => $lifetimeSpend,
                'aov' => $averageOrderValue,
                'total_orders' => $totalOrdersCount,
                'paid_orders_count' => $paidOrders->count(),
                'total_units' => $totalUnitsPurchased,
                'last_purchase_date' => $lastOrder?->created_at,
                'last_order_number' => $lastOrder?->order_number,
                'member_since' => $customer->created_at,
                'preferred_payment' => $preferredPaymentGateway?->gateway ?? ($lastOrder?->payment_gateway ?? 'N/A'),
                'preferred_payment_spent' => $preferredPaymentGateway?->total_gateway_amount ?? 0,
                'preferred_address' => $preferredAddress,
                'is_vip' => ($customer->customerProfile?->is_vip || in_array('VIP', $tags) || $lifetimeSpend >= 5000),
                'tags' => $tags,
            ],
            'orders' => $orders,
            'productsPurchased' => $productsPurchased,
            'addresses' => $addresses,
            'payments' => $payments,
            'orderReturns' => $orderReturns,
            'shipments' => $shipments,
            'notes' => $notes,
            'timeline' => $timeline,
        ];
    }

    /**
     * Build unified 360-degree chronological activity feed for Customer Timeline.
     */
    protected function buildCustomerTimeline(
        User $customer,
        $orders,
        $payments,
        $shipments,
        $orderReturns,
        $notes
    ): array {
        $events = [];

        // 1. Account Created
        $events[] = [
            'type' => 'account_created',
            'title' => 'Customer Account Created',
            'description' => "Customer registered with email {$customer->email}" . ($customer->mobile ? " and phone {$customer->mobile}" : ''),
            'timestamp' => $customer->created_at,
            'badge' => 'Registration',
            'badge_color' => 'blue',
            'icon' => 'user-plus',
            'meta' => [
                'Email' => $customer->email,
                'Status' => ucfirst($customer->status ?? 'active'),
            ],
        ];

        // 2. Orders Placed
        foreach ($orders as $order) {
            $events[] = [
                'type' => 'order_placed',
                'title' => "Order #{$order->order_number} Placed",
                'description' => "Order containing {$order->items->count()} item(s) totaling ₹" . number_format($order->grand_total, 2),
                'timestamp' => $order->created_at,
                'badge' => ucfirst($order->fulfilment_status ?? 'Placed'),
                'badge_color' => $order->fulfilment_status === 'delivered' ? 'emerald' : ($order->fulfilment_status === 'cancelled' ? 'rose' : 'amber'),
                'icon' => 'shopping-bag',
                'link' => route('admin.orders.show', $order->id),
                'meta' => [
                    'Order ID' => $order->order_number,
                    'Payment Status' => ucfirst($order->payment_status),
                    'Grand Total' => '₹' . number_format($order->grand_total, 2),
                ],
            ];
        }

        // 3. Payments
        foreach ($payments as $payment) {
            $isPaid = $payment->status === 'success';
            $events[] = [
                'type' => 'payment_event',
                'title' => "Payment of ₹" . number_format($payment->amount, 2) . " via " . ucfirst($payment->gateway ?? 'Gateway'),
                'description' => "Payment ID: " . ($payment->gateway_order_id ?: "PAY-{$payment->id}") . " (" . ucfirst($payment->status) . ")",
                'timestamp' => $payment->paid_at ?: $payment->created_at,
                'badge' => ucfirst($payment->status),
                'badge_color' => $isPaid ? 'emerald' : ($payment->status === 'pending' ? 'amber' : 'rose'),
                'icon' => 'credit-card',
                'meta' => [
                    'Gateway' => ucfirst($payment->gateway ?? 'Razorpay'),
                    'Amount' => '₹' . number_format($payment->amount, 2),
                    'Status' => ucfirst($payment->status),
                ],
            ];
        }

        // 4. Shipments
        foreach ($shipments as $shipment) {
            $events[] = [
                'type' => 'shipment_event',
                'title' => "Shipment #{$shipment->shipment_number} (" . ucfirst($shipment->status) . ")",
                'description' => "Courier: " . ($shipment->courier ?? 'Express') . ($shipment->tracking_number ? " | AWB: {$shipment->tracking_number}" : ''),
                'timestamp' => $shipment->dispatch_date ?: $shipment->created_at,
                'badge' => ucfirst($shipment->status),
                'badge_color' => $shipment->status === 'delivered' ? 'emerald' : 'sky',
                'icon' => 'truck',
                'meta' => [
                    'Courier' => $shipment->courier ?? 'Standard Delivery',
                    'Tracking' => $shipment->tracking_number ?? 'Not Assigned',
                    'Status' => ucfirst($shipment->status),
                ],
            ];
        }

        // 5. WhatsApp Communications
        if ($customer->whatsappContact && $customer->whatsappContact->relationLoaded('messages')) {
            foreach ($customer->whatsappContact->messages as $msg) {
                $isInbound = $msg->direction === 'inbound';
                $events[] = [
                    'type' => 'whatsapp_event',
                    'title' => $isInbound ? "WhatsApp Received from Customer" : "WhatsApp Sent to Customer",
                    'description' => $msg->message_body ?: "Template: " . ($msg->template_name ?: 'System Notification'),
                    'timestamp' => $msg->sent_at ?: $msg->created_at,
                    'badge' => $isInbound ? 'Inbound' : 'Outbound',
                    'badge_color' => 'emerald',
                    'icon' => 'message-circle',
                    'meta' => [
                        'Type' => ucfirst($msg->message_type ?? 'Text'),
                        'Status' => ucfirst($msg->status ?? 'Sent'),
                    ],
                ];
            }
        }

        // 6. Refunds & Returns
        foreach ($orderReturns as $ret) {
            $events[] = [
                'type' => 'return_event',
                'title' => "Return/Refund Request #{$ret->return_number}",
                'description' => "Reason: " . ($ret->reason ?: 'Customer requested') . " | Refund: ₹" . number_format($ret->refund_amount, 2),
                'timestamp' => $ret->requested_at ?: $ret->created_at,
                'badge' => ucfirst($ret->status),
                'badge_color' => $ret->status === 'approved' ? 'emerald' : ($ret->status === 'rejected' ? 'rose' : 'amber'),
                'icon' => 'rotate-ccw',
                'meta' => [
                    'Refund Amount' => '₹' . number_format($ret->refund_amount, 2),
                    'Status' => ucfirst($ret->status),
                ],
            ];
        }

        // 7. Admin CRM Notes
        foreach ($notes as $note) {
            $authorName = $note->author?->name ?? 'Admin Staff';
            $events[] = [
                'type' => 'admin_note_event',
                'title' => "CRM Note logged by {$authorName}",
                'description' => $note->note,
                'timestamp' => $note->created_at,
                'badge' => ucfirst($note->category ?? 'General'),
                'badge_color' => $note->is_pinned ? 'purple' : 'stone',
                'icon' => 'file-text',
                'meta' => [
                    'Author' => $authorName,
                    'Category' => ucfirst($note->category ?? 'General'),
                    'Pinned' => $note->is_pinned ? 'Yes' : 'No',
                ],
            ];
        }

        // Sort descending by timestamp
        usort($events, function ($a, $b) {
            $tA = $a['timestamp'] ? Carbon::parse($a['timestamp'])->timestamp : 0;
            $tB = $b['timestamp'] ? Carbon::parse($b['timestamp'])->timestamp : 0;
            return $tB <=> $tA;
        });

        return $events;
    }

    /**
     * Store new CRM Note on Customer Profile.
     */
    public function storeNote(User $customer, array $data, ?int $authorId): CustomerNote
    {
        return CustomerNote::create([
            'customer_id' => $customer->id,
            'author_id' => $authorId,
            'note' => $data['note'],
            'category' => $data['category'] ?? 'general',
            'is_pinned' => !empty($data['is_pinned']),
        ]);
    }

    /**
     * Delete CRM Note.
     */
    public function deleteNote(User $customer, CustomerNote $note): bool
    {
        if ($note->customer_id === $customer->id) {
            return (bool) $note->delete();
        }
        return false;
    }

    /**
     * Update customer tags and VIP status.
     */
    public function updateTags(User $customer, $tags): CustomerProfile
    {
        $tagString = is_array($tags) ? implode(', ', array_filter(array_map('trim', $tags))) : (string) $tags;

        $profile = $customer->customerProfile;
        if (! $profile) {
            $profile = CustomerProfile::create([
                'user_id' => $customer->id,
                'display_name' => $customer->name,
                'tags' => $tagString,
            ]);
        } else {
            $profile->tags = $tagString;
        }

        // Auto-check VIP tag
        if (stripos($tagString, 'VIP') !== false) {
            $profile->is_vip = true;
        }

        $profile->save();
        return $profile;
    }

    /**
     * Execute bulk actions on selected customers.
     */
    public function bulkAction(string $action, array $customerIds, $payload = null, ?int $authorId = null): int
    {
        if (empty($customerIds)) {
            return 0;
        }

        $affected = 0;

        if ($action === 'status') {
            $newStatus = in_array($payload, ['active', 'inactive', 'suspended']) ? $payload : 'active';
            $affected = User::whereIn('id', $customerIds)->update(['status' => $newStatus]);
        } elseif ($action === 'add_tag') {
            $tagToAdd = trim((string) $payload);
            if ($tagToAdd) {
                $customers = User::whereIn('id', $customerIds)->with('customerProfile')->get();
                foreach ($customers as $c) {
                    $currentTags = $c->customerProfile?->tags_array ?? [];
                    if (! in_array($tagToAdd, $currentTags)) {
                        $currentTags[] = $tagToAdd;
                        $this->updateTags($c, $currentTags);
                        $affected++;
                    }
                }
            }
        } elseif ($action === 'mark_vip') {
            $affected = CustomerProfile::whereIn('user_id', $customerIds)->update(['is_vip' => true]);
        }

        return $affected;
    }

    /**
     * Generate dataset for CSV/Excel export.
     */
    public function getExportDataset(Request $request): array
    {
        $headers = [
            'Customer ID',
            'Full Name',
            'Email',
            'Phone',
            'Status',
            'Total Orders',
            'Lifetime Spend (INR)',
            'Last Order Date',
            'City',
            'State',
            'Country',
            'WhatsApp Opt-in',
            'Tags',
            'Member Since',
        ];

        // Fetch all matching customers with filters
        $result = $this->getCustomerList($request->merge(['limit' => 10000]));
        $customers = $result['customers'];

        $rows = [];
        foreach ($customers as $c) {
            $profile = $c->customerProfile;
            $addr = $c->defaultShippingAddress ?: $c->addresses->first();

            $rows[] = [
                $c->id,
                $c->name,
                $c->email,
                $c->mobile ?: '—',
                ucfirst($c->status ?? 'active'),
                $c->total_orders ?? 0,
                number_format((float) ($c->lifetime_spend ?? 0), 2, '.', ''),
                $c->last_order_date ? Carbon::parse($c->last_order_date)->format('Y-m-d H:i') : 'No Orders',
                $addr?->city ?? '—',
                $addr?->state ?? '—',
                $addr?->country ?? 'India',
                ($profile?->whatsapp_opt_in || $c->whatsappContact?->opt_in) ? 'Yes' : 'No',
                $profile?->tags ?? '—',
                $c->created_at->format('Y-m-d'),
            ];
        }

        return [
            'headers' => $headers,
            'rows' => $rows,
        ];
    }
}
