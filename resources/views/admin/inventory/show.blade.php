@extends('layouts.admin')

@section('title', "Inventory: {$product?->name} ({$variant?->sku})")

@section('content')
<div class="p-6 md:p-8 space-y-6 max-w-[1600px] mx-auto">

    <!-- Top Navigation & Actions Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.inventory.index') }}" class="h-9 w-9 rounded-xl bg-white border border-stone-200 flex items-center justify-center text-stone-500 hover:text-stone-800 hover:bg-stone-50 transition shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-stone-400 font-mono">SKU: {{ $variant?->sku }}</span>
                    <span class="text-xs text-stone-300">•</span>
                    <span class="text-xs font-semibold text-stone-500">Warehouse: {{ $inventory->warehouse_code ?? 'DEFAULT' }}</span>
                    @php $status = $inventory->stock_status; @endphp
                    @if($status === 'out_of_stock')
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                            Out of Stock
                        </span>
                    @elseif($status === 'low_stock')
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                            Low Stock Alert
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            In Stock
                        </span>
                    @endif
                </div>
                <h1 class="text-2xl md:text-3xl font-bold text-stone-900 tracking-tight mt-0.5 font-serif">
                    {{ $product?->name }} <span class="font-normal text-stone-500 text-lg">({{ $variant?->name }})</span>
                </h1>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <!-- Quick Adjust Trigger -->
            <button type="button" onclick="openAdjustModal('{{ $inventory->id }}', '{{ addslashes($product?->name ?? 'Product') }}', '{{ $variant?->sku }}', {{ $inventory->quantity_on_hand }})" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold rounded-xl shadow-xs transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Adjust Stock
            </button>
            <a href="{{ route('admin.products.show', $product?->id) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-stone-200 text-stone-700 hover:bg-stone-50 text-xs font-semibold rounded-xl shadow-xs transition">
                <span>Product Master</span>
                <svg class="w-3.5 h-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
        </div>
    </div>

    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50/80 p-4 text-xs font-medium text-emerald-900 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">✕</button>
        </div>
    @endif

    <!-- Stock & Financial Valuation Metric Strip -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3.5">
        <div class="bg-white p-4 rounded-2xl border border-stone-200/80 shadow-xs">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-500">Current On Hand</span>
            <div class="mt-1.5 text-2xl font-bold text-stone-900 tracking-tight">{{ number_format($inventory->quantity_on_hand) }}</div>
            <div class="mt-0.5 text-[11px] text-stone-400">Total physical units</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-stone-200/80 shadow-xs">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-500">Reserved</span>
            <div class="mt-1.5 text-2xl font-bold text-amber-700 tracking-tight">{{ number_format($inventory->quantity_reserved) }}</div>
            <div class="mt-0.5 text-[11px] text-stone-400">Allocated to pending orders</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-stone-200/80 shadow-xs">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-500">Available</span>
            <div class="mt-1.5 text-2xl font-bold {{ $inventory->available_stock <= 0 ? 'text-rose-600' : 'text-emerald-700' }} tracking-tight">
                {{ number_format($inventory->available_stock) }}
            </div>
            <div class="mt-0.5 text-[11px] text-stone-400">Ready to sell & ship</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-stone-200/80 shadow-xs">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-500">Cost Price</span>
            <div class="mt-1.5 text-2xl font-bold text-stone-900 tracking-tight">₹{{ number_format($valuation['cost_price'], 2) }}</div>
            <div class="mt-0.5 text-[11px] text-stone-400">Per unit purchase cost</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-stone-200/80 shadow-xs">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-500">Selling Price</span>
            <div class="mt-1.5 text-2xl font-bold text-stone-900 tracking-tight">₹{{ number_format($valuation['selling_price'], 2) }}</div>
            <div class="mt-0.5 text-[11px] text-stone-400">Active retail catalog price</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-stone-200/80 shadow-xs">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-500">Cost Valuation</span>
            <div class="mt-1.5 text-xl font-bold text-blue-700 tracking-tight">₹{{ number_format($valuation['total_cost_value'], 2) }}</div>
            <div class="mt-0.5 text-[11px] text-stone-400">Total capital invested</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-stone-200/80 shadow-xs">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-500">Retail Value</span>
            <div class="mt-1.5 text-xl font-bold text-stone-900 tracking-tight">₹{{ number_format($valuation['total_retail_value'], 2) }}</div>
            <div class="mt-0.5 text-[11px] text-stone-400">Gross revenue potential</div>
        </div>
    </div>

    <!-- 6 Tabs Navigation Header -->
    <div class="border-b border-stone-200">
        <nav class="flex space-x-1 sm:space-x-3 overflow-x-auto text-xs font-semibold" aria-label="Inventory Details Tabs">
            <button type="button" onclick="switchInventoryTab('overview')" id="tab-btn-overview" class="inv-tab-btn py-3 px-3.5 border-b-2 border-emerald-600 text-emerald-800 whitespace-nowrap transition">
                Overview
            </button>
            <button type="button" onclick="switchInventoryTab('movements')" id="tab-btn-movements" class="inv-tab-btn py-3 px-3.5 border-b-2 border-transparent text-stone-500 hover:text-stone-800 hover:border-stone-300 whitespace-nowrap transition">
                Stock Movements ({{ $movements->total() }})
            </button>
            <button type="button" onclick="switchInventoryTab('orders')" id="tab-btn-orders" class="inv-tab-btn py-3 px-3.5 border-b-2 border-transparent text-stone-500 hover:text-stone-800 hover:border-stone-300 whitespace-nowrap transition">
                Orders ({{ $orders->total() }})
            </button>
            <button type="button" onclick="switchInventoryTab('adjustments')" id="tab-btn-adjustments" class="inv-tab-btn py-3 px-3.5 border-b-2 border-transparent text-stone-500 hover:text-stone-800 hover:border-stone-300 whitespace-nowrap transition">
                Adjustments ({{ $adjustments->total() }})
            </button>
            <button type="button" onclick="switchInventoryTab('purchases')" id="tab-btn-purchases" class="inv-tab-btn py-3 px-3.5 border-b-2 border-transparent text-stone-500 hover:text-stone-800 hover:border-stone-300 whitespace-nowrap transition">
                Purchase History ({{ $batches->count() }})
            </button>
            <button type="button" onclick="switchInventoryTab('history')" id="tab-btn-history" class="inv-tab-btn py-3 px-3.5 border-b-2 border-transparent text-stone-500 hover:text-stone-800 hover:border-stone-300 whitespace-nowrap transition">
                History
            </button>
        </nav>
    </div>

    <!-- TAB 1: OVERVIEW -->
    <div id="inv-tab-overview" class="inv-tab-content space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Stock Control & Thresholds -->
            <div class="bg-white rounded-2xl border border-stone-200/80 p-5 shadow-xs">
                <h3 class="text-sm font-bold text-stone-900 mb-4 pb-2 border-b border-stone-100 flex items-center justify-between">
                    <span>Stock Thresholds & Levels</span>
                    <span class="text-xs font-normal text-stone-400">Inventory ID #{{ $inventory->id }}</span>
                </h3>
                <dl class="grid grid-cols-2 gap-y-3.5 gap-x-6 text-xs">
                    <div>
                        <dt class="text-stone-400 font-medium">Reorder Trigger Level</dt>
                        <dd class="text-stone-900 font-bold mt-0.5">{{ $inventory->reorder_level ?? 10 }} units</dd>
                    </div>
                    <div>
                        <dt class="text-stone-400 font-medium">Safety Minimum Level</dt>
                        <dd class="text-stone-900 font-bold mt-0.5">{{ $inventory->min_level ?? 5 }} units</dd>
                    </div>
                    <div>
                        <dt class="text-stone-400 font-medium">Max Storage Capacity</dt>
                        <dd class="text-stone-900 font-bold mt-0.5">{{ $inventory->max_level ?? 1000 }} units</dd>
                    </div>
                    <div>
                        <dt class="text-stone-400 font-medium">Stock Tracking Mode</dt>
                        <dd class="text-emerald-700 font-bold mt-0.5">Automated Deduction</dd>
                    </div>
                    <div>
                        <dt class="text-stone-400 font-medium">Warehouse Code</dt>
                        <dd class="text-stone-900 font-bold mt-0.5">{{ $inventory->warehouse_code ?? 'DEFAULT' }}</dd>
                    </div>
                    <div>
                        <dt class="text-stone-400 font-medium">Batch Association</dt>
                        <dd class="text-stone-900 font-bold mt-0.5">{{ $inventory->batch?->batch_number ?? 'General Inventory' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Product Specifications -->
            <div class="bg-white rounded-2xl border border-stone-200/80 p-5 shadow-xs">
                <h3 class="text-sm font-bold text-stone-900 mb-4 pb-2 border-b border-stone-100 flex items-center justify-between">
                    <span>Product Logistics & Specifications</span>
                    <span class="text-xs font-normal text-stone-400">Product #{{ $product?->id }}</span>
                </h3>
                <dl class="grid grid-cols-2 gap-y-3.5 gap-x-6 text-xs">
                    <div>
                        <dt class="text-stone-400 font-medium">Product Category</dt>
                        <dd class="text-stone-900 font-semibold mt-0.5">{{ $product?->category?->name ?? 'Uncategorized' }}</dd>
                    </div>
                    <div>
                        <dt class="text-stone-400 font-medium">Brand</dt>
                        <dd class="text-stone-900 font-semibold mt-0.5">{{ $product?->brand ?? 'Guided Wellness' }}</dd>
                    </div>
                    <div>
                        <dt class="text-stone-400 font-medium">Weight</dt>
                        <dd class="text-stone-900 font-semibold mt-0.5">{{ $variant?->weight_grams ? "{$variant->weight_grams} g" : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-stone-400 font-medium">Dimensions (L×W×H)</dt>
                        <dd class="text-stone-900 font-semibold mt-0.5">
                            @if($variant?->length_cm)
                                {{ $variant->length_cm }} × {{ $variant->width_cm }} × {{ $variant->height_cm }} cm
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-stone-400 font-medium">HS Customs Tariff</dt>
                        <dd class="text-stone-900 font-semibold mt-0.5">{{ $product?->hs_code ?: '3004.90' }}</dd>
                    </div>
                    <div>
                        <dt class="text-stone-400 font-medium">Barcode / UPC</dt>
                        <dd class="text-stone-900 font-mono mt-0.5">{{ $variant?->barcode ?: '—' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- TAB 2: STOCK MOVEMENTS (FULL AUDIT LEDGER) -->
    <div id="inv-tab-movements" class="inv-tab-content hidden space-y-4">
        <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-stone-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-stone-900">Stock Movements Log</h3>
                    <p class="text-xs text-stone-400">Complete immutable transaction ledger for this inventory item.</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-stone-600">
                    <thead class="bg-stone-50 border-b border-stone-200 text-[11px] font-semibold uppercase tracking-wider text-stone-500">
                        <tr>
                            <th class="py-3 px-4">Date & Time</th>
                            <th class="py-3 px-3">Movement Type</th>
                            <th class="py-3 px-3 text-center">Previous Qty</th>
                            <th class="py-3 px-3 text-center">Difference</th>
                            <th class="py-3 px-3 text-center">New Qty</th>
                            <th class="py-3 px-4">Reason / Remarks</th>
                            <th class="py-3 px-4 text-right">User / Staff</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($movements as $m)
                            <tr class="hover:bg-stone-50/70 transition">
                                <td class="py-3.5 px-4 font-mono text-stone-500 whitespace-nowrap">
                                    {{ $m->created_at ? $m->created_at->format('d M Y, h:i:s A') : '—' }}
                                </td>
                                <td class="py-3.5 px-3 whitespace-nowrap">
                                    @php
                                        $type = $m->transaction_type;
                                        $badgeColor = match($type) {
                                            'sale' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            'purchase', 'opening_stock' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'refund', 'cancellation' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'damage' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            default => 'bg-purple-50 text-purple-700 border-purple-200',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold border {{ $badgeColor }}">
                                        {{ ucfirst(str_replace('_', ' ', $type)) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 text-center font-medium text-stone-700 whitespace-nowrap">
                                    {{ $m->previous_quantity ?? ($m->balance_after - $m->quantity) }}
                                </td>
                                <td class="py-3.5 px-3 text-center font-bold text-sm whitespace-nowrap {{ $m->quantity >= 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                                    {{ $m->diff_label }}
                                </td>
                                <td class="py-3.5 px-3 text-center font-bold text-stone-900 whitespace-nowrap">
                                    {{ $m->balance_after }}
                                </td>
                                <td class="py-3.5 px-4 text-stone-700 max-w-[280px]">
                                    <div class="truncate" title="{{ $m->remarks }}">{{ $m->remarks ?: '—' }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-right text-stone-700 whitespace-nowrap font-medium">
                                    {{ $m->creator?->name ?? 'System' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-stone-400 italic">No movement transactions logged yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($movements->hasPages())
                <div class="p-3 border-t border-stone-100">
                    {{ $movements->appends(request()->except('movements_page'))->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- TAB 3: ORDERS -->
    <div id="inv-tab-orders" class="inv-tab-content hidden space-y-4">
        <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-stone-100">
                <h3 class="text-sm font-bold text-stone-900">Orders Containing this Item</h3>
                <p class="text-xs text-stone-400">All customer purchase orders that included this product variant.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-stone-600">
                    <thead class="bg-stone-50 border-b border-stone-200 text-[11px] font-semibold uppercase tracking-wider text-stone-500">
                        <tr>
                            <th class="py-3 px-4">Order</th>
                            <th class="py-3 px-3">Date</th>
                            <th class="py-3 px-3">Customer</th>
                            <th class="py-3 px-3 text-center">Qty Purchased</th>
                            <th class="py-3 px-3 text-right">Line Total</th>
                            <th class="py-3 px-3 text-center">Payment Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($orders as $oi)
                            <tr class="hover:bg-stone-50/70 transition">
                                <td class="py-3.5 px-4 font-semibold text-stone-900">
                                    <a href="{{ route('admin.orders.show', $oi->order_id) }}" class="text-emerald-700 hover:underline">
                                        #{{ $oi->order?->order_number ?? $oi->order_id }}
                                    </a>
                                </td>
                                <td class="py-3.5 px-3 text-stone-500 whitespace-nowrap">
                                    {{ $oi->created_at ? \Carbon\Carbon::parse($oi->created_at)->format('d M Y, h:i A') : '—' }}
                                </td>
                                <td class="py-3.5 px-3 font-medium text-stone-800">
                                    {{ $oi->order?->user?->name ?? 'Guest Customer' }}
                                </td>
                                <td class="py-3.5 px-3 text-center font-bold text-stone-900">
                                    {{ $oi->quantity }}
                                </td>
                                <td class="py-3.5 px-3 text-right font-bold text-stone-900">
                                    ₹{{ number_format($oi->line_total, 2) }}
                                </td>
                                <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium {{ $oi->order?->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                        {{ ucfirst($oi->order?->payment_status ?? 'pending') }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.orders.show', $oi->order_id) }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900">
                                        View Order →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-stone-400 italic">No orders logged for this variant.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($orders->hasPages())
                <div class="p-3 border-t border-stone-100">
                    {{ $orders->appends(request()->except('orders_page'))->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- TAB 4: ADJUSTMENTS -->
    <div id="inv-tab-adjustments" class="inv-tab-content hidden space-y-4">
        <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-stone-100">
                <h3 class="text-sm font-bold text-stone-900">Manual Stock Adjustments & Damage Audits</h3>
                <p class="text-xs text-stone-400">Inventory reconciliation records and physical count corrections.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-stone-600">
                    <thead class="bg-stone-50 border-b border-stone-200 text-[11px] font-semibold uppercase tracking-wider text-stone-500">
                        <tr>
                            <th class="py-3 px-4">Date & Time</th>
                            <th class="py-3 px-3">Adjustment Type</th>
                            <th class="py-3 px-3 text-center">Previous Qty</th>
                            <th class="py-3 px-3 text-center">Adjusted Delta</th>
                            <th class="py-3 px-3 text-center">New Balance</th>
                            <th class="py-3 px-4">Staff Remarks</th>
                            <th class="py-3 px-4 text-right">Adjusted By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($adjustments as $adj)
                            <tr class="hover:bg-stone-50/70 transition">
                                <td class="py-3.5 px-4 font-mono text-stone-500 whitespace-nowrap">
                                    {{ $adj->created_at ? $adj->created_at->format('d M Y, h:i A') : '—' }}
                                </td>
                                <td class="py-3.5 px-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                        {{ ucfirst(str_replace('_', ' ', $adj->transaction_type)) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 text-center font-medium text-stone-700">
                                    {{ $adj->previous_quantity ?? ($adj->balance_after - $adj->quantity) }}
                                </td>
                                <td class="py-3.5 px-3 text-center font-bold text-sm {{ $adj->quantity >= 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                                    {{ $adj->diff_label }}
                                </td>
                                <td class="py-3.5 px-3 text-center font-bold text-stone-900">
                                    {{ $adj->balance_after }}
                                </td>
                                <td class="py-3.5 px-4 text-stone-700">
                                    {{ $adj->remarks ?: 'No remarks provided' }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-medium text-stone-800">
                                    {{ $adj->creator?->name ?? 'Admin Staff' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-stone-400 italic">No manual adjustments logged for this item.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 5: PURCHASE HISTORY (BATCH REPLENISHMENTS) -->
    <div id="inv-tab-purchases" class="inv-tab-content hidden space-y-4">
        <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-stone-100">
                <h3 class="text-sm font-bold text-stone-900">Purchase / Inward Batch Receipts</h3>
                <p class="text-xs text-stone-400">Supplier inward shipments and manufacturing batch records.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-stone-600">
                    <thead class="bg-stone-50 border-b border-stone-200 text-[11px] font-semibold uppercase tracking-wider text-stone-500">
                        <tr>
                            <th class="py-3 px-4">Batch Number</th>
                            <th class="py-3 px-3 text-center">Quantity Received</th>
                            <th class="py-3 px-3">Manufacture Date</th>
                            <th class="py-3 px-3">Expiry Date</th>
                            <th class="py-3 px-3 text-center">Batch Status</th>
                            <th class="py-3 px-4 text-right">Received Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($batches as $batch)
                            <tr class="hover:bg-stone-50/70 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-stone-900">
                                    {{ $batch->batch_number }}
                                </td>
                                <td class="py-3.5 px-3 text-center font-bold text-emerald-700 text-sm">
                                    +{{ number_format($batch->quantity_received) }}
                                </td>
                                <td class="py-3.5 px-3 text-stone-600 whitespace-nowrap">
                                    {{ $batch->manufacture_date ? \Carbon\Carbon::parse($batch->manufacture_date)->format('d M Y') : '—' }}
                                </td>
                                <td class="py-3.5 px-3 text-stone-600 whitespace-nowrap">
                                    {{ $batch->expiry_date ? \Carbon\Carbon::parse($batch->expiry_date)->format('d M Y') : '—' }}
                                </td>
                                <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        {{ ucfirst($batch->status ?? 'Active') }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right text-stone-500 whitespace-nowrap">
                                    {{ $batch->created_at ? \Carbon\Carbon::parse($batch->created_at)->format('d M Y') : '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-stone-400 italic">No purchase batches recorded for this variant.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 6: HISTORY (AUDIT TRAIL) -->
    <div id="inv-tab-history" class="inv-tab-content hidden space-y-4">
        <div class="bg-white rounded-2xl border border-stone-200/80 p-6 shadow-xs">
            <h3 class="text-sm font-bold text-stone-900 mb-4 pb-2 border-b border-stone-100">
                Complete Item Lifecycle Audit Trail
            </h3>
            <div class="relative pl-6 space-y-4 before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-stone-200">
                @foreach($movements->take(15) as $ev)
                    <div class="relative">
                        <div class="absolute -left-6 top-1 h-5 w-5 rounded-full border-2 border-white flex items-center justify-center text-[10px] font-bold shadow-xs {{ $ev->quantity >= 0 ? 'bg-emerald-600 text-white' : 'bg-rose-500 text-white' }}"></div>
                        <div class="bg-stone-50/70 p-3 rounded-xl border border-stone-200/80 text-xs">
                            <div class="flex items-center justify-between text-stone-500 mb-1">
                                <span class="font-bold text-stone-900 uppercase tracking-wide text-[11px]">{{ ucfirst(str_replace('_', ' ', $ev->transaction_type)) }}</span>
                                <span class="text-[11px]">{{ $ev->created_at ? $ev->created_at->format('d M Y, h:i A') : '—' }}</span>
                            </div>
                            <p class="text-stone-700">{{ $ev->remarks ?: 'Inventory transaction recorded.' }}</p>
                            <div class="flex items-center gap-3 text-[11px] text-stone-400 mt-2 pt-1.5 border-t border-stone-200/50">
                                <span>Change: <strong class="{{ $ev->quantity >= 0 ? 'text-emerald-700' : 'text-rose-600' }}">{{ $ev->diff_label }}</strong></span>
                                <span>•</span>
                                <span>Balance After: <strong>{{ $ev->balance_after }}</strong></span>
                                <span>•</span>
                                <span>User: <strong>{{ $ev->creator?->name ?? 'System' }}</strong></span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

<!-- Quick Adjust Stock Modal -->
<div id="adjust-stock-modal" class="fixed inset-0 z-50 bg-stone-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-stone-200">
        <div class="flex items-center justify-between pb-3 border-b border-stone-100">
            <div>
                <h3 class="text-sm font-bold text-stone-900">Adjust Inventory Stock</h3>
                <p id="modal-product-subtitle" class="text-xs text-stone-500 mt-0.5">Product Name • SKU</p>
            </div>
            <button type="button" onclick="closeAdjustModal()" class="text-stone-400 hover:text-stone-600">✕</button>
        </div>

        <form id="adjust-stock-form" method="POST" action="" class="mt-4 space-y-4">
            @csrf

            <!-- Mode Selector -->
            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1.5">Adjustment Action</label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="flex items-center justify-center gap-1.5 p-2 rounded-xl border border-stone-200 text-xs font-medium cursor-pointer hover:bg-stone-50 has-checked:border-emerald-600 has-checked:bg-emerald-50 has-checked:text-emerald-900">
                        <input type="radio" name="adjustment_mode" value="add" checked class="hidden">
                        <span>+ Add Stock</span>
                    </label>
                    <label class="flex items-center justify-center gap-1.5 p-2 rounded-xl border border-stone-200 text-xs font-medium cursor-pointer hover:bg-stone-50 has-checked:border-rose-600 has-checked:bg-rose-50 has-checked:text-rose-900">
                        <input type="radio" name="adjustment_mode" value="remove" class="hidden">
                        <span>- Remove Stock</span>
                    </label>
                    <label class="flex items-center justify-center gap-1.5 p-2 rounded-xl border border-stone-200 text-xs font-medium cursor-pointer hover:bg-stone-50 has-checked:border-blue-600 has-checked:bg-blue-50 has-checked:text-blue-900">
                        <input type="radio" name="adjustment_mode" value="set" class="hidden">
                        <span>= Set Exact</span>
                    </label>
                </div>
            </div>

            <!-- Current Stock vs Input Quantity -->
            <div class="grid grid-cols-2 gap-3 bg-stone-50 p-3 rounded-xl border border-stone-200/60">
                <div>
                    <span class="text-[11px] text-stone-500 font-medium block">Current On Hand</span>
                    <span id="modal-current-stock" class="text-lg font-bold text-stone-900">0</span>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-stone-700 mb-1">Quantity</label>
                    <input type="number" name="quantity" min="0" placeholder="Qty" class="w-full text-xs p-2 rounded-lg border border-stone-200 bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition font-bold" required>
                </div>
            </div>

            <!-- Reason Selector -->
            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1">Reason / Movement Type</label>
                <select name="reason" class="w-full text-xs p-2.5 rounded-xl border border-stone-200 bg-stone-50 focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition" required>
                    <option value="manual_adjustment">Manual Reconciliation / Recount</option>
                    <option value="purchase">Purchase / Supplier Inward Replenishment</option>
                    <option value="damage">Damaged / Expired Write-off</option>
                    <option value="refund">Customer Return Restock</option>
                    <option value="transfer">Warehouse Transfer</option>
                </select>
            </div>

            <!-- Remarks -->
            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1">Audit Notes / Remarks</label>
                <textarea name="remarks" rows="2" placeholder="e.g. PO-409 receipt, cycle count correction, or damaged bottle..." class="w-full text-xs p-2.5 rounded-xl border border-stone-200 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"></textarea>
            </div>

            <!-- Modal Action Buttons -->
            <div class="flex items-center justify-end gap-2 pt-2 border-t border-stone-100">
                <button type="button" onclick="closeAdjustModal()" class="px-4 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-semibold rounded-xl transition">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold rounded-xl transition shadow-xs">
                    Apply Adjustment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function switchInventoryTab(tabName) {
        document.querySelectorAll('.inv-tab-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.inv-tab-btn').forEach(btn => {
            btn.classList.remove('border-emerald-600', 'text-emerald-800');
            btn.classList.add('border-transparent', 'text-stone-500');
        });

        const activeContent = document.getElementById(`inv-tab-${tabName}`);
        const activeBtn = document.getElementById(`tab-btn-${tabName}`);

        if (activeContent) activeContent.classList.remove('hidden');
        if (activeBtn) {
            activeBtn.classList.add('border-emerald-600', 'text-emerald-800');
            activeBtn.classList.remove('border-transparent', 'text-stone-500');
        }

        window.location.hash = `tab-${tabName}`;
    }

    document.addEventListener('DOMContentLoaded', function () {
        const hash = window.location.hash;
        if (hash && hash.startsWith('#tab-')) {
            const tabName = hash.replace('#tab-', '');
            if (document.getElementById(`inv-tab-${tabName}`)) {
                switchInventoryTab(tabName);
            }
        }
    });

    function openAdjustModal(id, productName, sku, currentStock) {
        const modal = document.getElementById('adjust-stock-modal');
        const form = document.getElementById('adjust-stock-form');
        const subtitle = document.getElementById('modal-product-subtitle');
        const stockDisplay = document.getElementById('modal-current-stock');

        form.action = `/admin/inventory/${id}/adjust`;
        subtitle.textContent = `${productName} • ${sku}`;
        stockDisplay.textContent = currentStock;

        modal.classList.remove('hidden');
    }

    function closeAdjustModal() {
        document.getElementById('adjust-stock-modal').classList.add('hidden');
    }
</script>
@endsection
