<div class="overflow-x-auto">
    <table class="w-full text-left text-xs border-collapse" style="min-width: 1240px;">
        <thead class="border-b border-stone-200/90 bg-stone-50/80 uppercase tracking-wider text-stone-500 text-[11px] font-semibold select-none">
            <tr>
                {{-- 0. SNO --}}
                <th scope="col" class="py-3.5 px-3 text-center w-14 font-mono">SNO</th>

                {{-- Bulk Select Checkbox --}}
                <th scope="col" class="py-3.5 px-2 text-center w-12">
                    <input type="checkbox" id="select-all-checkbox" onchange="toggleSelectAll(this)" class="h-4 w-4 rounded border-stone-300 text-stone-900 focus:ring-0 cursor-pointer" title="Select All">
                </th>

                {{-- 1. Order ID --}}
                <th scope="col" class="py-3.5 px-3.5 whitespace-nowrap min-w-[170px]">Order ID</th>

                {{-- 2. Date --}}
                <th scope="col" class="py-3.5 px-3.5 whitespace-nowrap min-w-[130px]">Date</th>

                {{-- 3. Customer --}}
                <th scope="col" class="py-3.5 px-3.5 min-w-[190px]">Customer</th>

                {{-- 4. Location --}}
                <th scope="col" class="py-3.5 px-3.5 min-w-[160px]">Shipping Location</th>

                {{-- 5. Items --}}
                <th scope="col" class="py-3.5 px-3.5 min-w-[180px]">Items</th>

                {{-- 6. Total --}}
                <th scope="col" class="py-3.5 px-3.5 text-right whitespace-nowrap min-w-[110px]">Total</th>

                {{-- 7. Payment Status --}}
                <th scope="col" class="py-3.5 px-3.5 text-center whitespace-nowrap min-w-[130px]">Payment</th>

                {{-- 8. Fulfilment Status --}}
                <th scope="col" class="py-3.5 px-3.5 text-center whitespace-nowrap min-w-[130px]">Fulfilment</th>

                {{-- 9. Source --}}
                <th scope="col" class="py-3.5 px-3.5 whitespace-nowrap min-w-[130px]">Source</th>

                {{-- 10. Actions --}}
                <th scope="col" class="py-3.5 px-4 text-right whitespace-nowrap min-w-[140px]">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-100">
            @forelse($orders as $o)
                @php
                    $shippingAddr = $o->shippingAddress;
                @endphp
                <tr class="hover:bg-stone-50/70 transition-colors order-row group" data-order-id="{{ $o->id }}">
                    {{-- 0. SNO --}}
                    <td class="py-4 px-3 text-center text-stone-400 font-mono text-xs font-medium">
                        {{ ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration }}
                    </td>

                    {{-- Bulk Select --}}
                    <td class="py-4 px-2 text-center">
                        <input type="checkbox" class="order-select-checkbox h-4 w-4 rounded border-stone-300 text-stone-900 focus:ring-0 cursor-pointer" value="{{ $o->id }}" onchange="handleRowSelect()">
                    </td>

                    {{-- 1. Order ID --}}
                    <td class="py-4 px-3.5 whitespace-nowrap">
                        <a href="{{ route('admin.orders.show', $o) }}" class="font-mono text-xs font-bold text-stone-900 hover:text-emerald-800 transition tracking-tight">
                            {{ $o->order_number }}
                        </a>
                        <div class="mt-0.5 text-[10px] text-stone-400 font-mono uppercase tracking-wider">
                            {{ $o->payment_method ?? 'Online' }}
                        </div>
                    </td>

                    {{-- 2. Date --}}
                    <td class="py-4 px-3.5 whitespace-nowrap">
                        <div class="text-xs font-semibold text-stone-800">{{ $o->created_at?->format('d M Y') }}</div>
                        <div class="mt-0.5 text-[11px] text-stone-400 font-mono">{{ $o->created_at?->format('h:i A') }}</div>
                    </td>

                    {{-- 3. Customer --}}
                    <td class="py-4 px-3.5">
                        <div class="font-semibold text-stone-900 truncate max-w-[170px]" title="{{ $o->user?->name ?: 'Customer' }}">
                            {{ $o->user?->name ?: 'Guest / Customer' }}
                        </div>
                        <div class="mt-0.5 text-[11px] text-stone-500 truncate max-w-[170px]" title="{{ $o->user?->email ?: $o->user?->mobile }}">
                            {{ $o->user?->email ?: ($o->user?->mobile ?: '—') }}
                        </div>
                    </td>

                    {{-- 4. Location (Geographic info) --}}
                    <td class="py-4 px-3.5">
                        @if($shippingAddr)
                            <div class="font-medium text-stone-800 truncate max-w-[160px]" title="{{ $shippingAddr->city }}, {{ $shippingAddr->state }}">
                                {{ $shippingAddr->city ?? '—' }}, <span class="text-stone-500">{{ $shippingAddr->state ?? '' }}</span>
                            </div>
                            <div class="text-[10px] text-stone-400 font-mono truncate max-w-[160px]">
                                {{ $shippingAddr->area ? $shippingAddr->area . ' • ' : '' }}{{ $shippingAddr->postal_code ?? '' }}
                            </div>
                        @else
                            @php
                                $rawShip = is_array($o->shipping_address_json) ? $o->shipping_address_json : [];
                            @endphp
                            <div class="font-medium text-stone-800 truncate max-w-[160px]">
                                {{ $rawShip['city'] ?? '—' }}, <span class="text-stone-500">{{ $rawShip['state'] ?? '' }}</span>
                            </div>
                            <div class="text-[10px] text-stone-400 font-mono truncate max-w-[160px]">
                                {{ $rawShip['postal_code'] ?? '—' }}
                            </div>
                        @endif
                    </td>

                    {{-- 5. Items --}}
                    <td class="py-4 px-3.5">
                        @if($o->orderItems->isNotEmpty())
                            @php 
                                $firstItem = $o->orderItems->first();
                                $firstName = $firstItem->product_name_snapshot ?? ($firstItem->productVariant?->product?->name ?? 'Product');
                            @endphp
                            <div class="flex items-center gap-1.5 max-w-[170px]">
                                <span class="truncate font-medium text-stone-800" title="{{ $firstName }}">{{ $firstName }}</span>
                                <span class="shrink-0 rounded bg-stone-100 px-1.5 py-0.5 font-mono text-[10px] font-semibold text-stone-600">×{{ $firstItem->quantity }}</span>
                            </div>
                            @if($o->orderItems->count() > 1)
                                <button type="button" onclick="toggleOrderDetails({{ $o->id }})" class="mt-0.5 inline-flex items-center gap-1 text-[10px] font-semibold text-stone-400 hover:text-stone-700 transition">
                                    +{{ $o->orderItems->count() - 1 }} more item{{ $o->orderItems->count() > 2 ? 's' : '' }}
                                </button>
                            @endif
                        @else
                            <span class="text-stone-400 italic">—</span>
                        @endif
                    </td>

                    {{-- 6. Total --}}
                    <td class="py-4 px-3.5 whitespace-nowrap text-right">
                        <span class="font-mono text-sm font-bold text-stone-900 tracking-tight">
                            ₹{{ number_format($o->grand_total, 2) }}
                        </span>
                    </td>

                    {{-- 7. Payment Status --}}
                    <td class="py-4 px-3.5 whitespace-nowrap text-center">
                        @php
                            $pStatus = $o->payment_status ?? 'pending';
                            $pStyle = match($pStatus) {
                                'paid' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'dot' => 'bg-emerald-500'],
                                'pending' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'dot' => 'bg-amber-500'],
                                'partially_refunded' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'border' => 'border-purple-200', 'dot' => 'bg-purple-500'],
                                'refunded' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'border' => 'border-purple-200', 'dot' => 'bg-purple-500'],
                                'failed', 'cancelled' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200', 'dot' => 'bg-rose-500'],
                                default => ['bg' => 'bg-stone-50', 'text' => 'text-stone-700', 'border' => 'border-stone-200', 'dot' => 'bg-stone-400'],
                            };
                        @endphp
                        <div class="inline-flex flex-col items-center">
                            <span class="inline-flex items-center justify-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold capitalize shadow-2xs {{ $pStyle['bg'] }} {{ $pStyle['text'] }} {{ $pStyle['border'] }} min-w-[92px]">
                                <span class="h-1.5 w-1.5 rounded-full {{ $pStyle['dot'] }}"></span>
                                {{ str_replace('_', ' ', $pStatus) }}
                            </span>
                            @if(($o->refund_amount ?? 0) > 0)
                                <span class="mt-1 text-[10px] font-semibold text-purple-700 font-mono">Ref: ₹{{ number_format($o->refund_amount, 2) }}</span>
                            @endif
                        </div>
                    </td>

                    {{-- 8. Fulfilment Status --}}
                    <td class="py-4 px-3.5 whitespace-nowrap text-center">
                        @php
                            $fStatus = $o->fulfilment_status ?? 'pending';
                            $fStyle = match($fStatus) {
                                'delivered' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'dot' => 'bg-emerald-500'],
                                'dispatched' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'dot' => 'bg-blue-500'],
                                'packed', 'processing' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'dot' => 'bg-amber-500'],
                                'pending' => ['bg' => 'bg-stone-100', 'text' => 'text-stone-700', 'border' => 'border-stone-200', 'dot' => 'bg-stone-400'],
                                'cancelled' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200', 'dot' => 'bg-rose-500'],
                                default => ['bg' => 'bg-stone-50', 'text' => 'text-stone-700', 'border' => 'border-stone-200', 'dot' => 'bg-stone-400'],
                            };
                        @endphp
                        <div class="inline-flex flex-col items-center">
                            <span class="inline-flex items-center justify-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold capitalize shadow-2xs {{ $fStyle['bg'] }} {{ $fStyle['text'] }} {{ $fStyle['border'] }} min-w-[92px]">
                                <span class="h-1.5 w-1.5 rounded-full {{ $fStyle['dot'] }}"></span>
                                {{ $fStatus }}
                            </span>
                            @if($o->shipment_status && $o->shipment_status !== $fStatus)
                                <span class="mt-1 text-[10px] text-stone-400 capitalize">{{ str_replace('_', ' ', $o->shipment_status) }}</span>
                            @endif
                        </div>
                    </td>

                    {{-- 9. Source --}}
                    <td class="py-4 px-3.5 whitespace-nowrap">
                        <div class="inline-flex items-center gap-1.5">
                            <select 
                                class="order-source-select rounded-lg border border-stone-200 bg-stone-50/70 px-2.5 py-1 text-xs font-medium text-stone-700 transition hover:bg-white focus:bg-white focus:border-stone-400 focus:outline-none cursor-pointer"
                                data-url="{{ route('admin.orders.update-source', $o) }}"
                                onchange="handleSourceChange(this)">
                                @php $currentSource = strtolower($o->source ?? 'website'); @endphp
                                <option value="website" @selected($currentSource === 'website')>Website</option>
                                <option value="instagram" @selected($currentSource === 'instagram')>Instagram</option>
                                <option value="twitter" @selected($currentSource === 'twitter' || $currentSource === 'x')>Twitter / X</option>
                                <option value="whatsapp" @selected($currentSource === 'whatsapp')>WhatsApp</option>
                                <option value="amazon" @selected($currentSource === 'amazon')>Amazon</option>
                                <option value="direct" @selected($currentSource === 'direct')>Direct</option>
                                <option value="other" @selected($currentSource === 'other')>Other</option>
                            </select>
                            <span class="source-status text-emerald-600 hidden text-xs font-bold" title="Saved">✓</span>
                        </div>
                    </td>

                    {{-- 10. Actions --}}
                    <td class="py-4 px-4 whitespace-nowrap text-right">
                        <div class="inline-flex items-center justify-end gap-1.5">
                            {{-- Primary Action: Open Details --}}
                            <a href="{{ route('admin.orders.show', $o) }}" class="inline-flex items-center gap-1 rounded-lg border border-stone-300 bg-white px-3 py-1.5 text-xs font-semibold text-stone-700 shadow-2xs hover:bg-stone-50 hover:text-stone-900 transition" title="Open Full Order Details">
                                Open
                            </a>

                            {{-- Expand Row Toggle Button --}}
                            <button type="button" 
                                onclick="toggleOrderDetails({{ $o->id }})" 
                                id="expand-btn-{{ $o->id }}" 
                                class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-stone-200 bg-white text-stone-500 hover:bg-stone-50 hover:text-stone-800 transition" 
                                title="Quick Peek Details">
                                <svg class="h-3.5 w-3.5 transition-transform duration-200" id="expand-icon-{{ $o->id }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                            </button>

                            {{-- Actions Dropdown Menu --}}
                            <details class="relative inline-block text-left order-action-menu">
                                <summary class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-stone-200 bg-white text-stone-600 hover:bg-stone-50 hover:text-stone-900 transition cursor-pointer list-none [&::-webkit-details-marker]:hidden" title="More Actions">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg>
                                </summary>
                                <div class="absolute right-0 top-full z-30 mt-1 w-44 rounded-xl border border-stone-200 bg-white p-1 shadow-lg text-left text-xs">
                                    {{-- Quick Fulfilment --}}
                                    <button type="button" 
                                        onclick="openFulfilmentModal({{ $o->id }}, '{{ $o->order_number }}', '{{ $o->fulfilment_status }}', '{{ $o->shipment_status }}'); closeAllMenus();"
                                        class="w-full flex items-center gap-2 rounded-lg px-2.5 py-1.5 text-stone-700 hover:bg-stone-50 hover:text-stone-900 transition">
                                        <svg class="h-3.5 w-3.5 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                                        Update Fulfilment
                                    </button>

                                    {{-- Quick Refund --}}
                                    @if(in_array($o->payment_status, ['paid', 'partially_refunded']) && ($o->refund_status !== 'refunded'))
                                        @if(auth()->user()->hasRole('SUPER_ADMIN') || auth()->user()->hasPermission('ORDER_REFUND'))
                                            <button type="button" 
                                                onclick="openRefundModal({{ $o->id }}, '{{ $o->order_number }}', {{ $o->grand_total }}, {{ $o->refund_amount ?? 0 }}); closeAllMenus();"
                                                class="w-full flex items-center gap-2 rounded-lg px-2.5 py-1.5 text-purple-700 hover:bg-purple-50 transition">
                                                <svg class="h-3.5 w-3.5 text-purple-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                                                Initiate Refund
                                            </button>
                                        @endif
                                    @endif

                                    {{-- Quick Cancel --}}
                                    @if($o->fulfilment_status !== 'delivered' && $o->fulfilment_status !== 'cancelled')
                                        @if(auth()->user()->hasRole('SUPER_ADMIN') || auth()->user()->hasPermission('ORDER_CANCEL'))
                                            <button type="button" 
                                                onclick="openCancelModal({{ $o->id }}, '{{ $o->order_number }}'); closeAllMenus();"
                                                class="w-full flex items-center gap-2 rounded-lg px-2.5 py-1.5 text-rose-700 hover:bg-rose-50 transition">
                                                <svg class="h-3.5 w-3.5 text-rose-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                                Cancel Order
                                            </button>
                                        @endif
                                    @endif

                                    <div class="my-1 border-t border-stone-100"></div>

                                    <a href="{{ route('admin.orders.show', $o) }}" class="w-full flex items-center gap-2 rounded-lg px-2.5 py-1.5 text-stone-600 hover:bg-stone-50 hover:text-stone-900 transition">
                                        <svg class="h-3.5 w-3.5 text-stone-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                        View Full Details
                                    </a>
                                </div>
                            </details>
                        </div>
                    </td>
                </tr>

                {{-- Expandable Drawer Row for Order Details --}}
                <tr id="details-row-{{ $o->id }}" class="hidden bg-stone-50/70 border-y border-stone-200">
                    <td colspan="12" class="p-4 md:p-6">
                        <div class="rounded-xl border border-stone-200 bg-white p-4 md:p-5 shadow-xs">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs">
                                {{-- 1. Purchased Items --}}
                                <div>
                                    <h4 class="font-bold text-stone-900 uppercase tracking-wider text-[11px] mb-2.5 flex items-center gap-1.5">
                                        <svg class="h-3.5 w-3.5 text-stone-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                                        Purchased Items ({{ $o->orderItems->count() }})
                                    </h4>
                                    <div class="space-y-2 divide-y divide-stone-100">
                                        @foreach($o->orderItems as $item)
                                            <div class="pt-2 first:pt-0 flex items-center justify-between gap-2">
                                                <div>
                                                    <div class="font-semibold text-stone-800">{{ $item->product_name_snapshot ?? ($item->productVariant?->product?->name ?? 'Product') }}</div>
                                                    <div class="text-[10px] text-stone-400 font-mono">Qty: {{ $item->quantity }} × ₹{{ number_format($item->unit_price, 2) }}</div>
                                                </div>
                                                <div class="font-mono font-semibold text-stone-900">
                                                    ₹{{ number_format($item->line_total, 2) }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- 2. Shipping Address & Contact --}}
                                <div>
                                    <h4 class="font-bold text-stone-900 uppercase tracking-wider text-[11px] mb-2.5 flex items-center gap-1.5">
                                        <svg class="h-3.5 w-3.5 text-stone-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                        Shipping &amp; Delivery
                                    </h4>
                                    @php
                                        $ship = is_array($o->shipping_address_json) ? $o->shipping_address_json : [];
                                    @endphp
                                    <div class="text-stone-700 space-y-1">
                                        <div class="font-semibold text-stone-900">{{ $shippingAddr?->full_name ?? ($ship['name'] ?? ($o->user?->name ?? 'Recipient')) }}</div>
                                        @if($shippingAddr)
                                            <div>{{ $shippingAddr->address_line1 }}{{ $shippingAddr->address_line2 ? ', '.$shippingAddr->address_line2 : '' }}</div>
                                            <div>{{ implode(', ', array_filter([$shippingAddr->area, $shippingAddr->city, $shippingAddr->state, $shippingAddr->postal_code])) }}</div>
                                            <div class="text-stone-400 text-[10px] uppercase font-bold">{{ $shippingAddr->country ?? 'India' }}</div>
                                        @elseif(!empty($ship['address_line1']))
                                            <div>{{ $ship['address_line1'] }}{{ !empty($ship['address_line2']) ? ', '.$ship['address_line2'] : '' }}</div>
                                            <div>{{ implode(', ', array_filter([$ship['city'] ?? null, $ship['state'] ?? null, $ship['postal_code'] ?? null])) }}</div>
                                        @endif
                                        <div class="font-mono text-[11px] text-stone-500 mt-1">Phone: {{ $shippingAddr?->phone ?? ($ship['phone'] ?? ($o->user?->mobile ?: '—')) }}</div>
                                        <div class="mt-2 text-[11px] text-stone-500">
                                            Shipment Status: <strong class="capitalize text-stone-800">{{ str_replace('_', ' ', $o->shipment_status ?? 'Pending') }}</strong>
                                        </div>
                                    </div>
                                </div>

                                {{-- 3. Payment Breakdown & Quick Actions --}}
                                <div>
                                    <h4 class="font-bold text-stone-900 uppercase tracking-wider text-[11px] mb-2.5 flex items-center gap-1.5">
                                        <svg class="h-3.5 w-3.5 text-stone-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                                        Payment &amp; Quick Actions
                                    </h4>
                                    <div class="space-y-1.5 text-stone-600 border-b border-stone-100 pb-3">
                                        <div class="flex justify-between">
                                            <span>Subtotal:</span>
                                            <span class="font-mono text-stone-900">₹{{ number_format($o->subtotal, 2) }}</span>
                                        </div>
                                        @if(($o->discount_amount ?? 0) > 0)
                                            <div class="flex justify-between text-emerald-700">
                                                <span>Discount:</span>
                                                <span class="font-mono">-₹{{ number_format($o->discount_amount, 2) }}</span>
                                            </div>
                                        @endif
                                        @if(($o->shipping_amount ?? 0) > 0)
                                            <div class="flex justify-between">
                                                <span>Shipping:</span>
                                                <span class="font-mono">₹{{ number_format($o->shipping_amount, 2) }}</span>
                                            </div>
                                        @endif
                                        <div class="flex justify-between font-bold text-stone-900 pt-1 border-t border-stone-100">
                                            <span>Grand Total:</span>
                                            <span class="font-mono text-stone-900">₹{{ number_format($o->grand_total, 2) }}</span>
                                        </div>
                                    </div>

                                    <div class="mt-3 flex flex-wrap items-center gap-2">
                                        <button type="button" 
                                            onclick="openFulfilmentModal({{ $o->id }}, '{{ $o->order_number }}', '{{ $o->fulfilment_status }}', '{{ $o->shipment_status }}')"
                                            class="inline-flex items-center gap-1 rounded-lg border border-blue-200 bg-blue-50 px-2.5 py-1.5 text-[11px] font-semibold text-blue-700 hover:bg-blue-100 transition">
                                            Update Fulfilment
                                        </button>
                                        @if(in_array($o->payment_status, ['paid', 'partially_refunded']) && ($o->refund_status !== 'refunded'))
                                            @if(auth()->user()->hasRole('SUPER_ADMIN') || auth()->user()->hasPermission('ORDER_REFUND'))
                                                <button type="button" 
                                                    onclick="openRefundModal({{ $o->id }}, '{{ $o->order_number }}', {{ $o->grand_total }}, {{ $o->refund_amount ?? 0 }})"
                                                    class="inline-flex items-center gap-1 rounded-lg border border-purple-200 bg-purple-50 px-2.5 py-1.5 text-[11px] font-semibold text-purple-700 hover:bg-purple-100 transition">
                                                    Initiate Refund
                                                </button>
                                            @endif
                                        @endif
                                        @if($o->fulfilment_status !== 'delivered' && $o->fulfilment_status !== 'cancelled')
                                            @if(auth()->user()->hasRole('SUPER_ADMIN') || auth()->user()->hasPermission('ORDER_CANCEL'))
                                                <button type="button" 
                                                    onclick="openCancelModal({{ $o->id }}, '{{ $o->order_number }}')"
                                                    class="inline-flex items-center gap-1 rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1.5 text-[11px] font-semibold text-rose-700 hover:bg-rose-100 transition">
                                                    Cancel Order
                                                </button>
                                            @endif
                                        @endif
                                        <a href="{{ route('admin.orders.show', $o) }}" class="inline-flex items-center gap-1 text-[11px] font-semibold text-stone-600 hover:text-stone-900 underline ml-auto">
                                            Full Page →
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="py-12 px-4 text-center text-stone-500">
                        <div class="mx-auto max-w-sm space-y-3">
                            <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-stone-100 text-stone-400">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                            </div>
                            <p class="text-sm font-semibold text-stone-800">No matching orders found</p>
                            <p class="text-xs text-stone-500">Try adjusting your date range, location, search query, or status filters.</p>
                            @if(($activeFilters ?? 0) > 0)
                                <button type="button" onclick="resetAllFilters()" class="inline-flex items-center gap-1.5 rounded-xl border border-stone-300 bg-white px-3.5 py-1.5 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition shadow-2xs mt-2">
                                    Clear all filters
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
