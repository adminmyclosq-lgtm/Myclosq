@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-[1400px] p-6 lg:p-8">
    {{-- Flash Notifications --}}
    @if(session('success'))
        <div class="mb-6 flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="h-4 w-4 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">&times;</button>
        </div>
    @endif

    @if(session('info'))
        <div class="mb-6 flex items-center justify-between rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-800 shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="h-4 w-4 shrink-0 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('info') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-sky-600 hover:text-sky-900">&times;</button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 shadow-xs">
            <div class="font-semibold mb-1">Please address the following errors:</div>
            <ul class="list-disc list-inside space-y-0.5 text-xs">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Top Navigation & Header --}}
    <div class="mb-6">
        <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-stone-600 hover:text-stone-900 transition mb-3">
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Orders
        </a>

        <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-x-3.5 gap-y-2">
                    <h1 class="font-serif text-2xl font-bold tracking-tight text-stone-900 md:text-3xl whitespace-nowrap">
                        Order #{{ $order->order_number }}
                    </h1>

                    <div class="inline-flex flex-wrap items-center gap-2">
                        {{-- Payment Status Badge --}}
                        @php
                            $paymentBg = match($order->payment_status) {
                                'paid' => 'bg-emerald-50 text-emerald-800 border-emerald-200/80',
                                'pending' => 'bg-amber-50 text-amber-800 border-amber-200/80',
                                'partially_refunded', 'refunded' => 'bg-purple-50 text-purple-800 border-purple-200/80',
                                'failed', 'cancelled' => 'bg-rose-50 text-rose-800 border-rose-200/80',
                                default => 'bg-stone-100 text-stone-700 border-stone-200',
                            };
                            $paymentDot = match($order->payment_status) {
                                'paid' => 'bg-emerald-500',
                                'pending' => 'bg-amber-500',
                                'partially_refunded', 'refunded' => 'bg-purple-500',
                                'failed', 'cancelled' => 'bg-rose-500',
                                default => 'bg-stone-400',
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full border px-2.5 py-1 text-xs font-medium {{ $paymentBg }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $paymentDot }}"></span>
                            Payment {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                        </span>

                        {{-- Fulfillment Status Badge --}}
                        @php
                            $fulfilBg = match($order->fulfilment_status) {
                                'delivered' => 'bg-emerald-50 text-emerald-800 border-emerald-200/80',
                                'packed', 'dispatched' => 'bg-blue-50 text-blue-800 border-blue-200/80',
                                'cancelled' => 'bg-rose-50 text-rose-800 border-rose-200/80',
                                default => 'bg-stone-100 text-stone-700 border-stone-200',
                            };
                            $fulfilDot = match($order->fulfilment_status) {
                                'delivered' => 'bg-emerald-500',
                                'packed', 'dispatched' => 'bg-blue-500',
                                'cancelled' => 'bg-rose-500',
                                default => 'bg-stone-400',
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full border px-2.5 py-1 text-xs font-medium {{ $fulfilBg }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $fulfilDot }}"></span>
                            Fulfillment {{ ucfirst($order->fulfilment_status) }}
                        </span>

                        {{-- Shipment Status Badge --}}
                        @php
                            $shipBg = match($order->shipment_status) {
                                'delivered' => 'bg-emerald-50 text-emerald-800 border-emerald-200/80',
                                'in_transit', 'out_for_delivery', 'dispatched' => 'bg-sky-50 text-sky-800 border-sky-200/80',
                                'cancelled' => 'bg-rose-50 text-rose-800 border-rose-200/80',
                                default => 'bg-stone-100 text-stone-700 border-stone-200',
                            };
                            $shipDot = match($order->shipment_status) {
                                'delivered' => 'bg-emerald-500',
                                'in_transit', 'out_for_delivery', 'dispatched' => 'bg-sky-500',
                                'cancelled' => 'bg-rose-500',
                                default => 'bg-stone-400',
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full border px-2.5 py-1 text-xs font-medium {{ $shipBg }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $shipDot }}"></span>
                            Shipment {{ ucfirst(str_replace('_', ' ', $order->shipment_status)) }}
                        </span>

                        {{-- Returns Badge if any --}}
                        @if($order->returns->isNotEmpty())
                            <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full border border-orange-200 bg-orange-50 px-2.5 py-1 text-xs font-medium text-orange-800">
                                <span class="h-1.5 w-1.5 rounded-full bg-orange-500"></span>
                                Return {{ ucfirst($order->returns->first()->status) }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="mt-2 text-xs text-stone-500">
                    Placed on {{ $order->placed_at ? $order->placed_at->format('d M Y \a\t h:i A') : $order->created_at->format('d M Y \a\t h:i A') }}
                    <span class="mx-1.5 text-stone-300">·</span>
                    Source: <span class="font-semibold text-stone-700">{{ ucfirst($order->source ?? 'Website') }}</span>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center gap-2.5 shrink-0 flex-nowrap self-start xl:self-center">
                @if($order->payment_status !== 'paid' && $order->payment_status !== 'cancelled')
                    <button type="button" onclick="document.getElementById('markPaidModal').showModal()" class="inline-flex items-center gap-2 whitespace-nowrap rounded-xl bg-[#204b34] px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-[#183927] transition">
                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Mark as Paid
                    </button>
                @endif

                <button type="button" onclick="document.getElementById('fulfillModal').showModal()" class="inline-flex items-center gap-2 whitespace-nowrap rounded-xl border border-stone-200 bg-white px-4 py-2.5 text-xs font-semibold text-stone-700 shadow-sm hover:bg-stone-50 hover:text-stone-900 transition">
                    <svg class="h-4 w-4 shrink-0 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Fulfill Order
                </button>

                <details class="relative">
                    <summary class="list-none inline-flex cursor-pointer items-center gap-1.5 whitespace-nowrap rounded-xl border border-stone-200 bg-white px-3.5 py-2.5 text-xs font-semibold text-stone-700 shadow-sm hover:bg-stone-50 transition select-none">
                        <svg class="h-4 w-4 shrink-0 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
                        More
                        <svg class="h-3 w-3 shrink-0 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </summary>
                    <div class="absolute right-0 z-30 mt-2 min-w-[220px] w-56 origin-top-right rounded-xl border border-stone-200 bg-white p-1.5 shadow-xl ring-1 ring-black/5" style="min-width: 220px;">
                        <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank" class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2.5 text-left text-xs font-medium text-stone-700 hover:bg-stone-50 transition whitespace-nowrap">
                            <svg class="h-4 w-4 shrink-0 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Print / Download Invoice
                        </a>
                        <button type="button" onclick="document.getElementById('recordReturnModal').showModal()" class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2.5 text-left text-xs font-medium text-amber-700 hover:bg-amber-50 transition whitespace-nowrap">
                            <svg class="h-4 w-4 shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 15v-1a4 4 0 00-4-4H4m0 0l3-3m-3 3l3 3m5 4v1a3 3 0 003 3h4"/></svg>
                            Record Customer Return
                        </button>
                        <button type="button" onclick="document.getElementById('editShippingModal').showModal()" class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2.5 text-left text-xs font-medium text-stone-700 hover:bg-stone-50 transition whitespace-nowrap">
                            <svg class="h-4 w-4 shrink-0 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit Shipping Address
                        </button>
                        @if(in_array($order->payment_status, ['paid', 'partially_refunded']) && (float)$order->grand_total > (float)($order->refund_amount ?? 0))
                            <button type="button" onclick="document.getElementById('refundModal').showModal()" class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2.5 text-left text-xs font-medium text-purple-700 hover:bg-purple-50 transition whitespace-nowrap">
                                <svg class="h-4 w-4 shrink-0 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                Process Refund
                            </button>
                        @endif
                        @if($order->fulfilment_status !== 'cancelled' && $order->fulfilment_status !== 'delivered')
                            <div class="my-1 border-t border-stone-100"></div>
                            <button type="button" onclick="document.getElementById('cancelModal').showModal()" class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2.5 text-left text-xs font-medium text-rose-600 hover:bg-rose-50 transition whitespace-nowrap">
                                <svg class="h-4 w-4 shrink-0 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12"/></svg>
                                Cancel Order
                            </button>
                        @endif
                    </div>
                </details>
            </div>
        </div>
    </div>

    {{-- Main 2-Column Layout --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        {{-- LEFT COLUMN (~65% width / 8 cols) --}}
        <div class="space-y-6 lg:col-span-8">

            {{-- 1. Order Items Card with Complete Financial Breakup --}}
            <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-base font-semibold text-stone-900">Order Items</h2>
                    <span class="text-xs font-medium text-stone-500">{{ $order->orderItems->sum('quantity') }} items</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-stone-100 text-[11px] font-medium uppercase tracking-[.08em] text-stone-400">
                                <th class="pb-3 pr-4">Product</th>
                                <th class="pb-3 px-4">SKU</th>
                                <th class="pb-3 px-4 text-right">Price</th>
                                <th class="pb-3 px-4 text-center">Quantity</th>
                                <th class="pb-3 pl-4 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100 text-sm">
                            @foreach($order->orderItems as $item)
                                @php
                                    $variantImage = $item->productVariant?->images?->first()?->media?->url
                                        ?? $item->productVariant?->product?->productImages?->first()?->media?->url
                                        ?? null;
                                    $variantLabel = $item->productVariant?->name
                                        ?? ($item->productVariant?->unit_value ? ($item->productVariant->unit_value . $item->productVariant->unit_label) : '100g');
                                @endphp
                                <tr>
                                    <td class="py-4 pr-4">
                                        <div class="flex items-center gap-3.5">
                                            <div class="h-14 w-14 shrink-0 overflow-hidden rounded-xl border border-stone-200 bg-stone-50 flex items-center justify-center">
                                                @if($variantImage)
                                                    <img src="{{ $variantImage }}" alt="{{ $item->product_name_snapshot }}" class="h-full w-full object-cover">
                                                @else
                                                    <div class="p-2 text-center text-[10px] font-bold text-stone-400 uppercase tracking-wider">
                                                        <svg class="h-6 w-6 mx-auto text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-medium text-stone-900 leading-snug">
                                                    {{ $item->product_name_snapshot }}
                                                </div>
                                                <div class="text-xs text-stone-500 mt-1">
                                                    Variant: {{ $variantLabel }}
                                                </div>
                                                @if((float)$item->mrp_snapshot > (float)$item->unit_price)
                                                    <div class="text-[11px] text-stone-400 mt-0.5">
                                                        MRP: <span class="line-through">₹{{ number_format($item->mrp_snapshot, 2) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 font-mono text-xs text-stone-600">
                                        {{ $item->sku_snapshot ?: ($item->productVariant?->sku ?: '—') }}
                                    </td>
                                    <td class="py-4 px-4 text-right font-medium text-stone-800">
                                        ₹{{ number_format($item->unit_price, 2) }}
                                    </td>
                                    <td class="py-4 px-4 text-center text-stone-700">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="py-4 pl-4 text-right font-semibold text-stone-900">
                                        ₹{{ number_format($item->line_total, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Full Explicit Financial Breakup (Right-Aligned) --}}
                <div class="mt-6 border-t border-stone-100 pt-5 flex justify-end">
                    <div class="w-full max-w-sm space-y-2.5 text-xs text-stone-600">
                        @if(isset($totalMrp) && $totalMrp > (float)$order->subtotal)
                            <div class="flex justify-between text-stone-400">
                                <span>Original MRP Total</span>
                                <span class="line-through">₹{{ number_format($totalMrp, 2) }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span class="font-medium text-stone-900">₹{{ number_format($order->subtotal, 2) }}</span>
                        </div>

                        {{-- Always show Discounts / Savings --}}
                        <div class="flex justify-between {{ (float)$order->discount_amount > 0 ? 'text-emerald-700 font-medium' : 'text-stone-500' }}">
                            <span class="flex items-center gap-1.5">
                                Discounts / Savings
                                @if($order->coupon)
                                    <span class="rounded bg-emerald-100 px-1.5 py-0.5 text-[10px] font-bold text-emerald-800 uppercase tracking-wider">{{ $order->coupon->code }}</span>
                                @endif
                            </span>
                            <span>{{ (float)$order->discount_amount > 0 ? '-₹'.number_format($order->discount_amount, 2) : '₹0.00' }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span>Shipping Charges</span>
                            <span class="font-medium text-stone-900">{{ (float)$order->shipping_amount > 0 ? '₹'.number_format($order->shipping_amount, 2) : 'Free (₹0.00)' }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span>Taxes (GST Included)</span>
                            <span class="font-medium text-stone-900">₹{{ number_format($order->tax_amount, 2) }}</span>
                        </div>

                        <div class="border-t border-stone-200 pt-3 flex items-baseline justify-between text-base font-bold text-[#204b34]">
                            <span>Final Total</span>
                            <span class="text-xl">₹{{ number_format($order->grand_total, 2) }}</span>
                        </div>

                        @if((float)$order->refund_amount > 0)
                            <div class="flex justify-between text-purple-700 pt-1.5 border-t border-dashed border-stone-200">
                                <span>Total Refunded to Customer</span>
                                <span class="font-semibold">-₹{{ number_format($order->refund_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-stone-800 font-semibold">
                                <span>Net Amount Retained</span>
                                <span>₹{{ number_format(max(0, (float)$order->grand_total - (float)$order->refund_amount), 2) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 2. 5-Stage Order Timeline Card (Placed -> Payment -> Processing -> Shipped -> Delivered + Cancellations/Refunds) --}}
            <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                <h2 class="text-base font-semibold text-stone-900 mb-6">Order Timeline</h2>

                <div class="space-y-0">
                    {{-- 1. Order Placed --}}
                    <div class="flex items-stretch gap-4">
                        <div class="flex flex-col items-center shrink-0">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 ring-4 ring-white border border-emerald-300">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <div class="w-0.5 flex-1 bg-stone-200 my-1 min-h-[28px]"></div>
                        </div>
                        <div class="flex-1 min-w-0 pt-0.5 pb-6">
                            <div class="flex flex-wrap items-baseline justify-between gap-2">
                                <span class="text-sm font-semibold text-stone-900">Order Placed</span>
                                <span class="text-xs text-stone-400">{{ $order->created_at->format('d M Y, h:i A') }}</span>
                            </div>
                            <p class="text-xs text-stone-500 mt-1">
                                Placed by {{ $order->shipping_address_json['recipient_name'] ?? $order->user?->name ?? 'Demo Customer' }} via {{ ucfirst($order->source ?? 'Website') }}.
                            </p>
                        </div>
                    </div>

                    {{-- 2. Payment Confirmed / Pending --}}
                    @php
                        $isPaid = $order->payment_status === 'paid';
                        $paymentIconBg = $isPaid ? 'bg-emerald-100 text-emerald-700 border-emerald-300' : 'bg-amber-100 text-amber-700 border-amber-300';
                    @endphp
                    <div class="flex items-stretch gap-4">
                        <div class="flex flex-col items-center shrink-0">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ $paymentIconBg }} ring-4 ring-white border">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            </div>
                            <div class="w-0.5 flex-1 bg-stone-200 my-1 min-h-[28px]"></div>
                        </div>
                        <div class="flex-1 min-w-0 pt-0.5 pb-6">
                            <div class="flex flex-wrap items-baseline justify-between gap-2">
                                <span class="text-sm font-semibold text-stone-900">
                                    {{ $isPaid ? 'Payment Confirmed' : 'Payment Pending' }}
                                </span>
                                <span class="text-xs text-stone-400">
                                    {{ $order->payments->first()?->paid_at ? $order->payments->first()->paid_at->format('d M Y, h:i A') : ($isPaid ? $order->updated_at->format('d M Y, h:i A') : $order->created_at->format('d M Y, h:i A')) }}
                                </span>
                            </div>
                            <p class="text-xs text-stone-500 mt-1">
                                @if($isPaid)
                                    Payment of ₹{{ number_format($order->grand_total, 2) }} confirmed via {{ ucfirst(str_replace('_', ' ', $order->payments->first()?->gateway ?? 'Online')) }}.
                                @else
                                    Awaiting payment confirmation.
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- 3. Processing / Packed --}}
                    @php
                        $isProcessingOrPacked = in_array($order->fulfilment_status, ['processing', 'packed', 'dispatched', 'delivered']);
                        $processingIconBg = $isProcessingOrPacked ? 'bg-blue-100 text-blue-700 border-blue-300' : 'bg-stone-100 text-stone-400 border-stone-200';
                    @endphp
                    <div class="flex items-stretch gap-4">
                        <div class="flex flex-col items-center shrink-0">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ $processingIconBg }} ring-4 ring-white border">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            <div class="w-0.5 flex-1 bg-stone-200 my-1 min-h-[28px]"></div>
                        </div>
                        <div class="flex-1 min-w-0 pt-0.5 pb-6">
                            <div class="flex flex-wrap items-baseline justify-between gap-2">
                                <span class="text-sm font-semibold text-stone-900">
                                    {{ in_array($order->fulfilment_status, ['packed', 'dispatched', 'delivered']) ? 'Packed for Dispatch' : ($order->fulfilment_status === 'processing' ? 'Processing Order' : 'Awaiting Processing') }}
                                </span>
                                <span class="text-xs text-stone-400">
                                    {{ $isProcessingOrPacked ? $order->updated_at->format('d M Y, h:i A') : '—' }}
                                </span>
                            </div>
                            <p class="text-xs text-stone-500 mt-1">
                                @if($isProcessingOrPacked)
                                    Items assembled and prepared for courier handover.
                                @else
                                    Order awaiting warehouse fulfillment processing.
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- 4. Shipped --}}
                    @php
                        $isShipped = in_array($order->shipment_status, ['in_transit', 'out_for_delivery', 'dispatched', 'delivered']);
                        $shipIconBg = $isShipped ? 'bg-sky-100 text-sky-700 border-sky-300' : 'bg-stone-100 text-stone-400 border-stone-200';
                        $shipment = $order->shipments->first();
                    @endphp
                    <div class="flex items-stretch gap-4">
                        <div class="flex flex-col items-center shrink-0">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ $shipIconBg }} ring-4 ring-white border">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                            </div>
                            <div class="w-0.5 flex-1 bg-stone-200 my-1 min-h-[28px]"></div>
                        </div>
                        <div class="flex-1 min-w-0 pt-0.5 pb-6">
                            <div class="flex flex-wrap items-baseline justify-between gap-2">
                                <span class="text-sm font-semibold text-stone-900">
                                    {{ $isShipped ? 'Shipped & Dispatched' : 'Awaiting Shipment' }}
                                </span>
                                <span class="text-xs text-stone-400">
                                    {{ $shipment?->dispatch_date ? $shipment->dispatch_date->format('d M Y, h:i A') : ($isShipped ? $order->updated_at->format('d M Y, h:i A') : '—') }}
                                </span>
                            </div>
                            <p class="text-xs text-stone-500 mt-1">
                                @if($isShipped)
                                    Handed over to {{ $shipment?->carrier_name ?: ($shipment?->courier ?: 'Courier Partner') }}.
                                    @if($shipment?->tracking_number)
                                        (AWB: <span class="font-mono font-medium text-stone-700">{{ $shipment->tracking_number }}</span>)
                                    @endif
                                @else
                                    Package not yet dispatched.
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- 5. Delivered --}}
                    @php
                        $isDelivered = $order->shipment_status === 'delivered' || $order->fulfilment_status === 'delivered';
                        $deliveredIconBg = $isDelivered ? 'bg-emerald-100 text-emerald-700 border-emerald-300' : 'bg-stone-100 text-stone-400 border-stone-200';
                        $hasBranches = $order->fulfilment_status === 'cancelled' || (float)$order->refund_amount > 0 || $order->returns->isNotEmpty();
                    @endphp
                    <div class="flex items-stretch gap-4">
                        <div class="flex flex-col items-center shrink-0">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ $deliveredIconBg }} ring-4 ring-white border">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            @if($hasBranches)
                                <div class="w-0.5 flex-1 bg-stone-200 my-1 min-h-[28px]"></div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0 pt-0.5 {{ $hasBranches ? 'pb-6' : 'pb-1' }}">
                            <div class="flex flex-wrap items-baseline justify-between gap-2">
                                <span class="text-sm font-semibold text-stone-900">
                                    {{ $isDelivered ? 'Delivered to Customer' : 'Delivery Pending' }}
                                </span>
                                <span class="text-xs text-stone-400">
                                    {{ $shipment?->delivered_at ? $shipment->delivered_at->format('d M Y, h:i A') : ($isDelivered ? $order->updated_at->format('d M Y, h:i A') : '—') }}
                                </span>
                            </div>
                            <p class="text-xs text-stone-500 mt-1">
                                @if($isDelivered)
                                    Package successfully delivered to {{ $order->shipping_address_json['city'] ?? 'destination' }}.
                                @else
                                    Expected once transit cycle is completed.
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Cancellation Branch if Order Cancelled --}}
                    @if($order->fulfilment_status === 'cancelled')
                        <div class="flex items-stretch gap-4">
                            <div class="flex flex-col items-center shrink-0">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-rose-100 text-rose-700 ring-4 ring-white border border-rose-300">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </div>
                                @if((float)$order->refund_amount > 0 || $order->returns->isNotEmpty())
                                    <div class="w-0.5 flex-1 bg-stone-200 my-1 min-h-[28px]"></div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0 pt-0.5 pb-6">
                                <div class="flex flex-wrap items-baseline justify-between gap-2">
                                    <span class="text-sm font-semibold text-rose-800">Order Cancelled</span>
                                    <span class="text-xs text-stone-400">{{ $order->updated_at->format('d M Y, h:i A') }}</span>
                                </div>
                                <p class="text-xs text-rose-700 mt-1">
                                    Order was cancelled. Reason: {{ $order->statusHistory->where('new_status', 'cancelled')->last()?->remarks ?: 'Cancelled by administrator' }}
                                </p>
                            </div>
                        </div>
                    @endif

                    {{-- Refund Event Branch if Order Refunded --}}
                    @if((float)$order->refund_amount > 0)
                        <div class="flex items-stretch gap-4">
                            <div class="flex flex-col items-center shrink-0">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-purple-100 text-purple-700 ring-4 ring-white border border-purple-300">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                </div>
                                @if($order->returns->isNotEmpty())
                                    <div class="w-0.5 flex-1 bg-stone-200 my-1 min-h-[28px]"></div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0 pt-0.5 pb-6">
                                <div class="flex flex-wrap items-baseline justify-between gap-2">
                                    <span class="text-sm font-semibold text-purple-900">Refund Processed (₹{{ number_format($order->refund_amount, 2) }})</span>
                                    <span class="text-xs text-stone-400">{{ $order->updated_at->format('d M Y, h:i A') }}</span>
                                </div>
                                <p class="text-xs text-purple-700 mt-1">
                                    Refund issued to customer. Status: {{ ucfirst(str_replace('_', ' ', $order->refund_status)) }}.
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 3. Returns & Refunds Management Card --}}
            <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-base font-semibold text-stone-900">Returns &amp; Refunds</h2>
                        <p class="text-xs text-stone-500 mt-0.5">Track return requests, items received, inspection status, and refund outcomes.</p>
                    </div>
                    <button type="button" onclick="document.getElementById('recordReturnModal').showModal()" class="inline-flex items-center gap-1.5 rounded-xl border border-amber-300 bg-amber-50 px-3.5 py-2 text-xs font-semibold text-amber-800 hover:bg-amber-100 transition shadow-xs">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Record Return
                    </button>
                </div>

                @if($order->returns->isEmpty())
                    <div class="rounded-xl border border-dashed border-stone-200 p-8 text-center text-xs text-stone-500">
                        <svg class="mx-auto h-8 w-8 text-stone-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 15v-1a4 4 0 00-4-4H4m0 0l3-3m-3 3l3 3m5 4v1a3 3 0 003 3h4"/></svg>
                        <p class="font-medium text-stone-700">No return requests recorded for this order.</p>
                        <p class="text-stone-400 mt-1">If the customer has requested a return or replacement, click "Record Return" above.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($order->returns as $ret)
                            @php
                                $statusBadgeClass = match($ret->status) {
                                    'completed' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                    'approved', 'received' => 'bg-blue-50 text-blue-800 border-blue-200',
                                    'rejected' => 'bg-rose-50 text-rose-800 border-rose-200',
                                    default => 'bg-amber-50 text-amber-800 border-amber-200',
                                };
                                $outcomeBadgeClass = match($ret->outcome) {
                                    'refunded' => 'bg-purple-50 text-purple-800 border-purple-200',
                                    'replaced' => 'bg-sky-50 text-sky-800 border-sky-200',
                                    'rejected' => 'bg-rose-50 text-rose-800 border-rose-200',
                                    default => 'bg-stone-100 text-stone-700 border-stone-200',
                                };
                            @endphp
                            <div class="rounded-xl border border-stone-200 bg-stone-50/50 p-4 text-xs">
                                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-stone-200 pb-3">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-bold text-stone-900">{{ $ret->return_number }}</span>
                                        <span class="rounded-full border px-2 py-0.5 text-[11px] font-medium {{ $statusBadgeClass }}">
                                            Status: {{ ucfirst($ret->status) }}
                                        </span>
                                        <span class="rounded-full border px-2 py-0.5 text-[11px] font-medium {{ $outcomeBadgeClass }}">
                                            Outcome: {{ ucfirst($ret->outcome) }}
                                        </span>
                                    </div>
                                    <div class="text-stone-400">
                                        Requested: {{ $ret->requested_at ? $ret->requested_at->format('d M Y, h:i A') : $ret->created_at->format('d M Y, h:i A') }}
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 py-3">
                                    <div>
                                        <div class="text-stone-400 uppercase tracking-wider text-[10px] font-semibold">Reason</div>
                                        <div class="font-medium text-stone-800 mt-0.5">{{ $ret->reason }}</div>
                                        @if($ret->admin_notes)
                                            <div class="text-stone-500 text-[11px] mt-1 bg-white p-2 rounded border border-stone-100">{{ $ret->admin_notes }}</div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-stone-400 uppercase tracking-wider text-[10px] font-semibold">Refund Amount</div>
                                        <div class="font-semibold text-stone-900 mt-0.5">₹{{ number_format($ret->refund_amount, 2) }}</div>
                                        <div class="text-[11px] text-stone-400 mt-0.5">Processed By: {{ $ret->processor?->name ?? 'Admin' }}</div>
                                    </div>
                                </div>

                                {{-- Returned Items Sub-Table --}}
                                @if($ret->items->isNotEmpty())
                                    <div class="mt-2 border-t border-stone-200 pt-3">
                                        <div class="text-[10px] font-bold uppercase tracking-wider text-stone-400 mb-2">Items Returned</div>
                                        <div class="space-y-1.5">
                                            @foreach($ret->items as $retItem)
                                                <div class="flex items-center justify-between bg-white rounded-lg p-2 border border-stone-100">
                                                    <div>
                                                        <span class="font-medium text-stone-800">{{ $retItem->orderItem?->product_name_snapshot ?? 'Item' }}</span>
                                                        <span class="text-stone-400 ml-1">× {{ $retItem->quantity }}</span>
                                                    </div>
                                                    <div class="text-stone-500 text-[11px]">
                                                        Condition: <span class="capitalize font-medium text-stone-700">{{ $retItem->condition ?? 'Unopened' }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- 4. Payment Transactions & Event History Card --}}
            <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-semibold text-stone-900">Transaction Events &amp; Refund History</h2>
                    <span class="text-xs text-stone-500">Gateway Ledger</span>
                </div>

                @php
                    $allTransactions = $order->payments->flatMap->transactions->sortByDesc('created_at');
                @endphp

                @if($allTransactions->isEmpty())
                    <div class="rounded-xl border border-dashed border-stone-200 p-6 text-center text-xs text-stone-500">
                        No gateway transactions recorded for this order yet.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="border-b border-stone-100 text-[11px] font-medium uppercase tracking-[.08em] text-stone-400">
                                    <th class="pb-2.5 pr-3">Type</th>
                                    <th class="pb-2.5 px-3">Gateway Ref</th>
                                    <th class="pb-2.5 px-3">Status</th>
                                    <th class="pb-2.5 px-3 text-right">Amount</th>
                                    <th class="pb-2.5 pl-3 text-right">Date &amp; Time</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-100 text-stone-700">
                                @foreach($allTransactions as $tx)
                                    @php
                                        $typeBadge = $tx->transaction_type === 'refund'
                                            ? 'bg-purple-50 text-purple-800 border-purple-200'
                                            : 'bg-emerald-50 text-emerald-800 border-emerald-200';
                                    @endphp
                                    <tr>
                                        <td class="py-3 pr-3">
                                            <span class="inline-flex rounded-full border px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $typeBadge }}">
                                                {{ $tx->transaction_type }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 font-mono text-stone-800">
                                            {{ $tx->gateway_transaction_id ?: '—' }}
                                        </td>
                                        <td class="py-3 px-3">
                                            <span class="capitalize text-stone-600">{{ $tx->gateway_status }}</span>
                                        </td>
                                        <td class="py-3 px-3 text-right font-medium {{ $tx->transaction_type === 'refund' ? 'text-purple-700' : 'text-stone-900' }}">
                                            {{ $tx->transaction_type === 'refund' ? '-' : '' }}₹{{ number_format($tx->amount, 2) }}
                                        </td>
                                        <td class="py-3 pl-3 text-right text-stone-400">
                                            {{ $tx->processed_at ? $tx->processed_at->format('d M Y, h:i A') : $tx->created_at->format('d M Y, h:i A') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- 5. Carrier Tracking-Events Scan History (if any) --}}
            @php
                $shipment = $order->shipments->first();
                $trackingEvents = $shipment?->trackingEvents?->sortByDesc('event_time') ?? collect();
            @endphp
            @if($trackingEvents->isNotEmpty())
                <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                    <h2 class="text-base font-semibold text-stone-900 mb-4">Carrier Checkpoint Scan History</h2>
                    <div class="space-y-3 divide-y divide-stone-100 text-xs">
                        @foreach($trackingEvents as $event)
                            <div class="pt-2 first:pt-0 flex items-start justify-between gap-3">
                                <div>
                                    <div class="font-medium text-stone-800 capitalize">{{ $event->status }}</div>
                                    <div class="text-stone-500 mt-0.5">{{ $event->description ?: 'Checkpoint scan recorded.' }}</div>
                                    @if($event->location)
                                        <div class="text-[11px] text-stone-400 mt-0.5">Location: {{ $event->location }}</div>
                                    @endif
                                </div>
                                <div class="text-stone-400 text-right whitespace-nowrap">
                                    {{ $event->event_time ? $event->event_time->format('d M Y, h:i A') : $event->created_at->format('d M Y, h:i A') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- 6. Internal Notes Card --}}
            <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                <h2 class="text-base font-semibold text-stone-900 mb-4">Internal Notes</h2>

                {{-- Previous Notes List --}}
                @php
                    $internalNotes = $order->statusHistory->where('status_type', 'internal_note');
                @endphp
                @if($internalNotes->isNotEmpty())
                    <div class="mb-4 space-y-2.5 divide-y divide-stone-100">
                        @foreach($internalNotes as $noteItem)
                            <div class="pt-2 first:pt-0">
                                <div class="flex items-center justify-between text-xs text-stone-500 mb-1">
                                    <span class="font-medium text-stone-700">{{ $noteItem->changedby?->name ?? 'Admin' }}</span>
                                    <span>{{ $noteItem->created_at->format('d M Y, h:i A') }}</span>
                                </div>
                                <p class="text-xs text-stone-800 bg-stone-50 rounded-lg p-2.5 border border-stone-100">{{ $noteItem->remarks }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.orders.note', $order) }}" class="space-y-3">
                    @csrf
                    <textarea name="note" rows="3" required placeholder="Add a private note (only visible to admins)..." class="w-full rounded-xl border border-stone-200 p-3 text-xs text-stone-800 placeholder-stone-400 focus:border-[#204b34] focus:outline-none focus:ring-1 focus:ring-[#204b34] transition"></textarea>
                    <div class="flex justify-end">
                        <button type="submit" class="rounded-xl bg-[#204b34] px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-[#183927] transition">
                            Add Note
                        </button>
                    </div>
                </form>
            </div>

        </div>

        {{-- RIGHT COLUMN (~35% width / 4 cols) --}}
        <div class="space-y-6 lg:col-span-4">

            {{-- 1. Customer Information Card with Verification Badge --}}
            <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-semibold text-stone-900">Customer Information</h2>
                    <button type="button" onclick="document.getElementById('editCustomerModal').showModal()" class="inline-flex items-center gap-1 text-xs font-medium text-stone-600 hover:text-stone-900 transition">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </button>
                </div>

                @php
                    $customerName = $order->shipping_address_json['recipient_name']
                        ?? $order->user?->customerProfile?->display_name
                        ?? $order->user?->name
                        ?? 'Demo Customer';
                    $words = explode(' ', trim($customerName));
                    $initials = strtoupper(substr($words[0] ?? 'D', 0, 1) . substr($words[1] ?? 'C', 0, 1));
                    $customerPhone = $order->shipping_address_json['phone'] ?? ($order->user?->mobile ?? '+91 98765 43210');
                    $customerEmail = $order->user?->email ?? 'customer@gutreset.local';
                    $isVerified = (bool)($order->user?->email_verified_at || $order->user?->mobile_verified_at);
                @endphp

                <div class="flex items-center gap-3.5 pb-5 border-b border-stone-100">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-800 text-sm font-bold border border-emerald-200/60">
                        {{ $initials }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5">
                            <span class="font-semibold text-stone-900 text-sm truncate">{{ $customerName }}</span>
                            @if($isVerified)
                                <span title="Verified Account" class="text-emerald-600">
                                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                </span>
                            @endif
                        </div>
                        <div class="text-xs text-stone-500 truncate mt-0.5">{{ $customerEmail }}</div>
                        <div class="text-xs text-stone-500 mt-0.5">{{ $customerPhone }}</div>
                    </div>
                </div>

                {{-- Stats Row --}}
                <div class="grid grid-cols-3 gap-2 pt-4 text-center">
                    <div>
                        <div class="text-[10px] font-medium uppercase tracking-wider text-stone-400">Customer Since</div>
                        <div class="mt-1 text-xs font-semibold text-stone-800">
                            {{ $customerStats['customerSince'] ? \Carbon\Carbon::parse($customerStats['customerSince'])->format('d M Y') : '13 Oct 2026' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-[10px] font-medium uppercase tracking-wider text-stone-400">Total Orders</div>
                        <div class="mt-1 text-xs font-semibold text-stone-800">
                            {{ $customerStats['totalOrders'] }}
                        </div>
                    </div>
                    <div>
                        <div class="text-[10px] font-medium uppercase tracking-wider text-stone-400">Lifetime Spend</div>
                        <div class="mt-1 text-xs font-semibold text-stone-800">
                            ₹{{ number_format($customerStats['lifetimeSpend'], 2) }}
                        </div>
                    </div>
                </div>

                @if($order->user_id)
                    <a href="{{ route('admin.customers.show', $order->user_id) }}" class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl border border-stone-200 bg-white py-2 text-xs font-semibold text-stone-700 hover:bg-stone-50 hover:text-stone-900 transition shadow-xs">
                        <svg class="h-3.5 w-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        View Full Customer Profile
                    </a>
                @else
                    <div class="mt-4 rounded-lg bg-stone-50 p-2 text-center text-[11px] text-stone-500">
                        Guest Checkout (No registered profile account)
                    </div>
                @endif
            </div>

            {{-- 2. Shipping Address Card --}}
            <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                @php
                    $shipAddr = $order->shippingAddress ?: $order->getEffectiveShippingAddress();
                    $shipData = $shipAddr ? $shipAddr->toArray() : ($order->shipping_address_json ?: []);
                    $addrType = $shipData['address_type'] ?? 'home';
                @endphp
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <h2 class="text-base font-semibold text-stone-900">Shipping Address</h2>
                        <span class="rounded-full border border-stone-200 bg-stone-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-stone-600">
                            {{ ucfirst($addrType) }}
                        </span>
                        @if(!empty($shipData['is_verified']))
                            <span class="inline-flex items-center gap-0.5 text-emerald-600" title="Address Verified">
                                <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            </span>
                        @endif
                    </div>
                    <button type="button" onclick="document.getElementById('editShippingModal').showModal()" class="inline-flex items-center gap-1 text-xs font-medium text-stone-600 hover:text-stone-900 transition">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </button>
                </div>

                <div class="text-xs text-stone-600 leading-relaxed space-y-1">
                    <div class="font-semibold text-stone-900">{{ $shipData['full_name'] ?? ($shipData['recipient_name'] ?? 'Demo Customer') }}</div>
                    <div>{{ $shipData['address_line1'] ?? '123 Wellness Street' }}</div>
                    @if(!empty($shipData['address_line2']))
                        <div>{{ $shipData['address_line2'] }}</div>
                    @endif
                    @if(!empty($shipData['landmark']) || !empty($shipData['area']))
                        <div class="text-stone-500">
                            @if(!empty($shipData['landmark'])) Landmark: {{ $shipData['landmark'] }} @endif
                            @if(!empty($shipData['area'])) ({{ $shipData['area'] }}) @endif
                        </div>
                    @endif
                    <div>
                        {{ $shipData['city'] ?? 'Hyderabad' }}@if(!empty($shipData['district'])), {{ $shipData['district'] }}@endif,
                        {{ $shipData['state'] ?? 'Telangana' }}
                        {{ $shipData['postal_code'] ?? '500001' }}
                    </div>
                    <div>{{ $shipData['country'] ?? 'India' }}</div>
                    <div class="pt-1 text-stone-500 font-medium">
                        {{ $shipData['phone'] ?? '+91 98765 43210' }}
                        @if(!empty($shipData['email']))
                            <span class="mx-1 text-stone-300">·</span> {{ $shipData['email'] }}
                        @endif
                    </div>

                    @if(!empty($shipData['delivery_instructions']))
                        <div class="mt-2 rounded-lg bg-amber-50/70 p-2 border border-amber-200/60 text-[11px] text-amber-900">
                            <span class="font-bold">Instructions:</span> {{ $shipData['delivery_instructions'] }}
                        </div>
                    @endif

                    <div class="pt-2 border-t border-stone-100 flex items-center justify-between">
                        @if($shipAddr && $shipAddr->google_maps_url)
                            <a href="{{ $shipAddr->google_maps_url }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#204b34] hover:underline">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Open in Google Maps
                            </a>
                        @endif

                        @if($shipAddr && $shipAddr->has_coordinates)
                            <span class="font-mono text-[10px] text-stone-400">
                                {{ number_format($shipAddr->latitude, 4) }}, {{ number_format($shipAddr->longitude, 4) }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 3. Billing Address Card --}}
            <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-base font-semibold text-stone-900">Billing Address</h2>
                    <button type="button" onclick="document.getElementById('editBillingModal').showModal()" class="inline-flex items-center gap-1 text-xs font-medium text-stone-600 hover:text-stone-900 transition">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </button>
                </div>

                @php
                    $isSameBilling = empty($order->billing_address_json) ||
                        (json_encode($order->billing_address_json) === json_encode($order->shipping_address_json));
                @endphp

                @if($isSameBilling)
                    <p class="text-xs text-stone-500">Same as shipping address.</p>
                @else
                    <div class="text-xs text-stone-600 leading-relaxed space-y-1">
                        <div class="font-semibold text-stone-900">{{ $order->billing_address_json['recipient_name'] ?? '' }}</div>
                        <div>{{ $order->billing_address_json['address_line1'] ?? '' }}</div>
                        @if(!empty($order->billing_address_json['address_line2']))
                            <div>{{ $order->billing_address_json['address_line2'] }}</div>
                        @endif
                        <div>
                            {{ $order->billing_address_json['city'] ?? '' }},
                            {{ $order->billing_address_json['state'] ?? '' }}
                            {{ $order->billing_address_json['postal_code'] ?? '' }}
                        </div>
                        <div>{{ $order->billing_address_json['country'] ?? 'India' }}</div>
                        <div class="pt-1 text-stone-500 font-medium">{{ $order->billing_address_json['phone'] ?? '' }}</div>
                    </div>
                @endif
            </div>

            {{-- 4. Payment Information Summary with Razorpay References --}}
            <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                <h2 class="text-base font-semibold text-stone-900 mb-4">Payment Information</h2>

                @php
                    $activePayment = $order->payments->first();
                    $amountPaid = $order->payment_status === 'paid' ? (float)$order->grand_total : (float)($order->payments->where('status', 'paid')->sum('amount') ?: 0);
                    $outstanding = max(0, (float)$order->grand_total - $amountPaid);
                @endphp

                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-stone-500">Payment Status</span>
                        <span class="inline-flex items-center gap-1.5 rounded-full border px-2 py-0.5 text-[11px] font-medium {{ $paymentBg }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $paymentDot }}"></span>
                            {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-stone-500">Payment Method</span>
                        <span class="font-medium text-stone-800">
                            {{ $activePayment?->gateway === 'cash_on_delivery' ? 'Cash on Delivery' : 'Online' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-stone-500">Gateway</span>
                        <span class="font-medium text-stone-800">
                            {{ $activePayment?->gateway ? ucfirst(str_replace('_', ' ', $activePayment->gateway)) : 'Online' }}
                        </span>
                    </div>

                    {{-- Razorpay Order Reference ID --}}
                    <div class="flex items-center justify-between">
                        <span class="text-stone-500">Gateway Order ID</span>
                        <span class="font-mono text-stone-700">
                            {{ $activePayment?->gateway_order_id ?? '—' }}
                        </span>
                    </div>

                    {{-- Razorpay Payment / Transaction ID --}}
                    <div class="flex items-center justify-between">
                        <span class="text-stone-500">Transaction Ref ID</span>
                        <span class="font-mono text-stone-700">
                            {{ $activePayment?->transactions->first()?->gateway_transaction_id ?? '—' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between border-t border-stone-100 pt-2.5">
                        <span class="text-stone-500">Amount Paid</span>
                        <span class="font-semibold text-stone-900">₹{{ number_format($amountPaid, 2) }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-stone-500">Outstanding Balance</span>
                        <span class="font-semibold {{ $outstanding > 0 ? 'text-amber-700' : 'text-stone-900' }}">
                            ₹{{ number_format($outstanding, 2) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- 5. Fulfillment & Shipping Card with Dispatch Date --}}
            <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                <h2 class="text-base font-semibold text-stone-900 mb-4">Fulfillment &amp; Shipping</h2>

                @php
                    $shipment = $order->shipments->first();
                @endphp

                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-stone-500">Fulfillment Status</span>
                        <span class="inline-flex items-center gap-1.5 rounded-full border px-2 py-0.5 text-[11px] font-medium {{ $fulfilBg }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $fulfilDot }}"></span>
                            {{ ucfirst($order->fulfilment_status) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-stone-500">Shipment Status</span>
                        <span class="inline-flex items-center gap-1.5 rounded-full border px-2 py-0.5 text-[11px] font-medium {{ $shipBg }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $shipDot }}"></span>
                            {{ ucfirst(str_replace('_', ' ', $order->shipment_status)) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-stone-500">Courier Partner</span>
                        <span class="font-medium text-stone-800">{{ $shipment?->carrier_name ?: ($shipment?->courier ?: '—') }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-stone-500">Tracking Number / AWB</span>
                        <span class="font-mono text-stone-700">{{ $shipment?->tracking_number ?? '—' }}</span>
                    </div>

                    {{-- Dispatch Date --}}
                    <div class="flex items-center justify-between">
                        <span class="text-stone-500">Dispatch Date</span>
                        <span class="text-stone-800">{{ $shipment?->dispatch_date ? $shipment->dispatch_date->format('d M Y, h:i A') : '—' }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-stone-500">Tracking Link</span>
                        @if($shipment?->tracking_url)
                            <a href="{{ $shipment->tracking_url }}" target="_blank" class="text-[#204b34] font-medium hover:underline inline-flex items-center gap-1">
                                Track Package
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        @else
                            <span class="text-stone-400">—</span>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- MODALS --}}

{{-- Record Customer Return Modal --}}
<dialog id="recordReturnModal" class="rounded-2xl border border-stone-200 bg-white p-0 shadow-2xl backdrop:bg-black/40 w-full max-w-lg">
    <div class="p-6">
        <h3 class="text-base font-bold text-stone-900 mb-1">Record Customer Return</h3>
        <p class="text-xs text-stone-500 mb-4">Record items returned, return reason, inspection status, and refund outcome.</p>

        <form method="POST" action="{{ route('admin.orders.returns.store', $order) }}" class="space-y-4 text-xs">
            @csrf

            <div>
                <label class="block font-medium text-stone-700 mb-1">Return Reason</label>
                <select name="reason" required class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
                    <option value="Damaged item received">Damaged item received</option>
                    <option value="Defective / Quality issue">Defective / Quality issue</option>
                    <option value="Wrong item delivered">Wrong item delivered</option>
                    <option value="Customer changed mind / Dissatisfaction">Customer changed mind / Dissatisfaction</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div>
                <label class="block font-medium text-stone-700 mb-1.5">Select Items Returned</label>
                <div class="space-y-2 border border-stone-200 rounded-xl p-3 bg-stone-50/50">
                    @foreach($order->orderItems as $itemIdx => $orderItem)
                        <div class="flex items-center justify-between gap-3 bg-white p-2.5 rounded-lg border border-stone-100">
                            <input type="hidden" name="items[{{ $itemIdx }}][order_item_id]" value="{{ $orderItem->id }}">
                            <div class="min-w-0 flex-1">
                                <div class="font-medium text-stone-900 truncate">{{ $orderItem->product_name_snapshot }}</div>
                                <div class="text-[11px] text-stone-500">Ordered Qty: {{ $orderItem->quantity }} · Unit Price: ₹{{ number_format($orderItem->unit_price, 2) }}</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <label class="text-[11px] text-stone-500">Return Qty:</label>
                                <input type="number" name="items[{{ $itemIdx }}][quantity]" min="1" max="{{ $orderItem->quantity }}" value="1" class="w-14 rounded-lg border border-stone-200 p-1.5 text-center text-xs">
                                <select name="items[{{ $itemIdx }}][condition]" class="rounded-lg border border-stone-200 p-1.5 text-xs">
                                    <option value="unopened">Unopened</option>
                                    <option value="opened">Opened</option>
                                    <option value="damaged">Damaged</option>
                                </select>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-medium text-stone-700 mb-1">Processing Status</label>
                    <select name="status" required class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
                        <option value="requested">Requested</option>
                        <option value="approved">Approved</option>
                        <option value="in_transit">In Transit (Return Courier)</option>
                        <option value="received">Item Received</option>
                        <option value="inspected">Inspected</option>
                        <option value="completed">Completed</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div>
                    <label class="block font-medium text-stone-700 mb-1">Final Outcome</label>
                    <select name="outcome" required class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
                        <option value="pending">Pending</option>
                        <option value="refunded">Refunded</option>
                        <option value="replaced">Replaced</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-medium text-stone-700 mb-1">Refund Amount (₹) if applicable</label>
                <input type="number" step="0.01" min="0" max="{{ $order->grand_total }}" name="refund_amount" placeholder="0.00" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
                <span class="text-[11px] text-stone-400">If set and outcome is 'Refunded', this will be automatically recorded in order refunds.</span>
            </div>

            <div>
                <label class="block font-medium text-stone-700 mb-1">Internal Admin Notes</label>
                <textarea name="admin_notes" rows="2" placeholder="e.g. Return package received, seal unbroken, restocking approved." class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800"></textarea>
            </div>

            <div class="flex justify-end gap-2.5 pt-3 border-t border-stone-100">
                <button type="button" onclick="document.getElementById('recordReturnModal').close()" class="rounded-xl border border-stone-200 bg-white px-4 py-2 text-xs font-semibold text-stone-700 hover:bg-stone-50">
                    Cancel
                </button>
                <button type="submit" class="rounded-xl bg-[#204b34] px-4 py-2 text-xs font-semibold text-white hover:bg-[#183927]">
                    Save Return Record
                </button>
            </div>
        </form>
    </div>
</dialog>

{{-- Mark as Paid Modal --}}
<dialog id="markPaidModal" class="rounded-2xl border border-stone-200 bg-white p-0 shadow-2xl backdrop:bg-black/40 w-full max-w-md">
    <div class="p-6">
        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 mb-4">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h3 class="text-base font-bold text-stone-900">Mark Order as Paid?</h3>
        <p class="mt-2 text-xs text-stone-500">
            This will mark order <strong>#{{ $order->order_number }}</strong> as fully paid (₹{{ number_format($order->grand_total, 2) }}). This action is recorded in order lifecycle logs.
        </p>
        <form method="POST" action="{{ route('admin.orders.mark-as-paid', $order) }}" class="mt-6 flex justify-end gap-2.5">
            @csrf
            <button type="button" onclick="document.getElementById('markPaidModal').close()" class="rounded-xl border border-stone-200 bg-white px-4 py-2 text-xs font-semibold text-stone-700 hover:bg-stone-50">
                Cancel
            </button>
            <button type="submit" class="rounded-xl bg-[#204b34] px-4 py-2 text-xs font-semibold text-white hover:bg-[#183927]">
                Confirm & Mark as Paid
            </button>
        </form>
    </div>
</dialog>

{{-- Fulfill Order Modal --}}
<dialog id="fulfillModal" class="rounded-2xl border border-stone-200 bg-white p-0 shadow-2xl backdrop:bg-black/40 w-full max-w-lg">
    <div class="p-6">
        <h3 class="text-base font-bold text-stone-900 mb-1">Update Fulfillment & Shipping</h3>
        <p class="text-xs text-stone-500 mb-4">Update the fulfillment stage, dispatch status, or courier information.</p>

        <form method="POST" action="{{ route('admin.orders.update-fulfilment', $order) }}" class="space-y-4 text-xs">
            @csrf
            @method('PATCH')

            <div>
                <label class="block font-medium text-stone-700 mb-1">Fulfillment Status</label>
                <select name="fulfilment_status" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800 focus:border-[#204b34] focus:outline-none">
                    @foreach(['pending' => 'Pending', 'processing' => 'Processing', 'packed' => 'Packed', 'dispatched' => 'Dispatched', 'delivered' => 'Delivered'] as $val => $lbl)
                        <option value="{{ $val }}" @selected($order->fulfilment_status === $val)>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-medium text-stone-700 mb-1">Shipment Status (Optional)</label>
                <select name="shipment_status" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800 focus:border-[#204b34] focus:outline-none">
                    <option value="">Auto-sync with fulfillment</option>
                    @foreach(['pending' => 'Pending', 'dispatched' => 'Dispatched', 'in_transit' => 'In Transit', 'out_for_delivery' => 'Out for Delivery', 'delivered' => 'Delivered'] as $val => $lbl)
                        <option value="{{ $val }}" @selected($order->shipment_status === $val)>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-medium text-stone-700 mb-1">Courier Partner (Optional)</label>
                <input type="text" name="courier" value="{{ $order->shipments->first()?->courier ?? '' }}" placeholder="e.g. BlueDart, Delhivery, DTDC" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
            </div>

            <div>
                <label class="block font-medium text-stone-700 mb-1">Tracking Number / AWB (Optional)</label>
                <input type="text" name="tracking_number" value="{{ $order->shipments->first()?->tracking_number ?? '' }}" placeholder="e.g. 1234567890" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
            </div>

            <div class="flex justify-end gap-2.5 pt-3 border-t border-stone-100">
                <button type="button" onclick="document.getElementById('fulfillModal').close()" class="rounded-xl border border-stone-200 bg-white px-4 py-2 text-xs font-semibold text-stone-700 hover:bg-stone-50">
                    Cancel
                </button>
                <button type="submit" class="rounded-xl bg-[#204b34] px-4 py-2 text-xs font-semibold text-white hover:bg-[#183927]">
                    Save Fulfillment
                </button>
            </div>
        </form>
    </div>
</dialog>

{{-- Edit Shipping Address Modal --}}
<dialog id="editShippingModal" class="rounded-2xl border border-stone-200 bg-white p-0 shadow-2xl backdrop:bg-black/40 w-full max-w-lg">
    <div class="p-6">
        @php
            $editShip = $order->shippingAddress ?: $order->getEffectiveShippingAddress();
            $editData = $editShip ? $editShip->toArray() : ($order->shipping_address_json ?: []);
        @endphp
        <h3 class="text-base font-bold text-stone-900 mb-1">Edit Shipping Address</h3>
        <p class="text-xs text-stone-500 mb-4">Update recipient information, delivery destination, and geocoding details.</p>

        <form method="POST" action="{{ route('admin.orders.update-address', $order) }}" class="space-y-3 text-xs">
            @csrf
            @method('PATCH')
            <input type="hidden" name="type" value="shipping">

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block font-medium text-stone-700 mb-1">Recipient Name</label>
                    <input type="text" name="recipient_name" required value="{{ $editData['full_name'] ?? ($editData['recipient_name'] ?? '') }}" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
                </div>
                <div>
                    <label class="block font-medium text-stone-700 mb-1">Address Type</label>
                    <select name="address_type" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
                        <option value="home" {{ ($editData['address_type'] ?? '') === 'home' ? 'selected' : '' }}>Home</option>
                        <option value="office" {{ ($editData['address_type'] ?? '') === 'office' ? 'selected' : '' }}>Office / Commercial</option>
                        <option value="other" {{ ($editData['address_type'] ?? '') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block font-medium text-stone-700 mb-1">Phone Number</label>
                    <input type="text" name="phone" value="{{ $editData['phone'] ?? '' }}" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
                </div>
                <div>
                    <label class="block font-medium text-stone-700 mb-1">Email (Optional)</label>
                    <input type="email" name="email" value="{{ $editData['email'] ?? '' }}" placeholder="customer@email.com" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
                </div>
            </div>

            <div>
                <label class="block font-medium text-stone-700 mb-1">Address Line 1</label>
                <input type="text" name="address_line1" required value="{{ $editData['address_line1'] ?? '' }}" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
            </div>

            <div>
                <label class="block font-medium text-stone-700 mb-1">Address Line 2 (Optional)</label>
                <input type="text" name="address_line2" value="{{ $editData['address_line2'] ?? '' }}" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block font-medium text-stone-700 mb-1">Landmark</label>
                    <input type="text" name="landmark" value="{{ $editData['landmark'] ?? '' }}" placeholder="e.g. Near Metro Station" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
                </div>
                <div>
                    <label class="block font-medium text-stone-700 mb-1">Area / Locality</label>
                    <input type="text" name="area" value="{{ $editData['area'] ?? '' }}" placeholder="e.g. Madhapur" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-2">
                <div>
                    <label class="block font-medium text-stone-700 mb-1">City</label>
                    <input type="text" name="city" required value="{{ $editData['city'] ?? '' }}" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
                </div>
                <div>
                    <label class="block font-medium text-stone-700 mb-1">District</label>
                    <input type="text" name="district" value="{{ $editData['district'] ?? '' }}" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
                </div>
                <div>
                    <label class="block font-medium text-stone-700 mb-1">State</label>
                    <input type="text" name="state" required value="{{ $editData['state'] ?? '' }}" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block font-medium text-stone-700 mb-1">PIN / Postal Code</label>
                    <input type="text" name="postal_code" required value="{{ $editData['postal_code'] ?? '' }}" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
                </div>
                <div>
                    <label class="block font-medium text-stone-700 mb-1">Country</label>
                    <input type="text" name="country" value="{{ $editData['country'] ?? 'India' }}" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
                </div>
            </div>

            <div>
                <label class="block font-medium text-stone-700 mb-1">Delivery Instructions</label>
                <input type="text" name="delivery_instructions" value="{{ $editData['delivery_instructions'] ?? '' }}" placeholder="e.g. Call before delivery, leave with security" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block font-medium text-stone-700 mb-1">Latitude (Optional)</label>
                    <input type="number" step="any" name="latitude" value="{{ $editData['latitude'] ?? '' }}" placeholder="e.g. 17.385044" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
                </div>
                <div>
                    <label class="block font-medium text-stone-700 mb-1">Longitude (Optional)</label>
                    <input type="number" step="any" name="longitude" value="{{ $editData['longitude'] ?? '' }}" placeholder="e.g. 78.486671" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
                </div>
            </div>

            <div class="flex justify-end gap-2.5 pt-3 border-t border-stone-100">
                <button type="button" onclick="document.getElementById('editShippingModal').close()" class="rounded-xl border border-stone-200 bg-white px-4 py-2 text-xs font-semibold text-stone-700 hover:bg-stone-50">
                    Cancel
                </button>
                <button type="submit" class="rounded-xl bg-[#204b34] px-4 py-2 text-xs font-semibold text-white hover:bg-[#183927]">
                    Save Address
                </button>
            </div>
        </form>
    </div>
</dialog>

{{-- Edit Billing Address Modal --}}
<dialog id="editBillingModal" class="rounded-2xl border border-stone-200 bg-white p-0 shadow-2xl backdrop:bg-black/40 w-full max-w-md">
    <div class="p-6">
        <h3 class="text-base font-bold text-stone-900 mb-1">Edit Billing Address</h3>
        <p class="text-xs text-stone-500 mb-4">Update billing details for this order.</p>

        <form method="POST" action="{{ route('admin.orders.update-address', $order) }}" class="space-y-3 text-xs">
            @csrf
            @method('PATCH')
            <input type="hidden" name="type" value="billing">

            <div>
                <label class="block font-medium text-stone-700 mb-1">Recipient Name</label>
                <input type="text" name="recipient_name" required value="{{ $order->billing_address_json['recipient_name'] ?? ($order->shipping_address_json['recipient_name'] ?? '') }}" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
            </div>

            <div>
                <label class="block font-medium text-stone-700 mb-1">Phone Number</label>
                <input type="text" name="phone" value="{{ $order->billing_address_json['phone'] ?? ($order->shipping_address_json['phone'] ?? '') }}" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
            </div>

            <div>
                <label class="block font-medium text-stone-700 mb-1">Address Line 1</label>
                <input type="text" name="address_line1" required value="{{ $order->billing_address_json['address_line1'] ?? ($order->shipping_address_json['address_line1'] ?? '') }}" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
            </div>

            <div>
                <label class="block font-medium text-stone-700 mb-1">Address Line 2 (Optional)</label>
                <input type="text" name="address_line2" value="{{ $order->billing_address_json['address_line2'] ?? ($order->shipping_address_json['address_line2'] ?? '') }}" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block font-medium text-stone-700 mb-1">City</label>
                    <input type="text" name="city" required value="{{ $order->billing_address_json['city'] ?? ($order->shipping_address_json['city'] ?? '') }}" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
                </div>
                <div>
                    <label class="block font-medium text-stone-700 mb-1">State</label>
                    <input type="text" name="state" required value="{{ $order->billing_address_json['state'] ?? ($order->shipping_address_json['state'] ?? '') }}" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block font-medium text-stone-700 mb-1">PIN / Postal Code</label>
                    <input type="text" name="postal_code" required value="{{ $order->billing_address_json['postal_code'] ?? ($order->shipping_address_json['postal_code'] ?? '') }}" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
                </div>
                <div>
                    <label class="block font-medium text-stone-700 mb-1">Country</label>
                    <input type="text" name="country" value="{{ $order->billing_address_json['country'] ?? 'India' }}" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
                </div>
            </div>

            <div class="flex justify-end gap-2.5 pt-3 border-t border-stone-100">
                <button type="button" onclick="document.getElementById('editBillingModal').close()" class="rounded-xl border border-stone-200 bg-white px-4 py-2 text-xs font-semibold text-stone-700 hover:bg-stone-50">
                    Cancel
                </button>
                <button type="submit" class="rounded-xl bg-[#204b34] px-4 py-2 text-xs font-semibold text-white hover:bg-[#183927]">
                    Save Address
                </button>
            </div>
        </form>
    </div>
</dialog>

{{-- Edit Customer Modal --}}
<dialog id="editCustomerModal" class="rounded-2xl border border-stone-200 bg-white p-0 shadow-2xl backdrop:bg-black/40 w-full max-w-md">
    <div class="p-6">
        <h3 class="text-base font-bold text-stone-900 mb-1">Edit Customer Details</h3>
        <p class="text-xs text-stone-500 mb-4">Update contact information for this order.</p>

        <form method="POST" action="{{ route('admin.orders.update-address', $order) }}" class="space-y-3 text-xs">
            @csrf
            @method('PATCH')
            <input type="hidden" name="type" value="both">
            <input type="hidden" name="address_line1" value="{{ $order->shipping_address_json['address_line1'] ?? '123 Wellness Street' }}">
            <input type="hidden" name="address_line2" value="{{ $order->shipping_address_json['address_line2'] ?? '' }}">
            <input type="hidden" name="city" value="{{ $order->shipping_address_json['city'] ?? 'Hyderabad' }}">
            <input type="hidden" name="state" value="{{ $order->shipping_address_json['state'] ?? 'Telangana' }}">
            <input type="hidden" name="postal_code" value="{{ $order->shipping_address_json['postal_code'] ?? '500001' }}">
            <input type="hidden" name="country" value="{{ $order->shipping_address_json['country'] ?? 'India' }}">

            <div>
                <label class="block font-medium text-stone-700 mb-1">Customer / Recipient Name</label>
                <input type="text" name="recipient_name" required value="{{ $customerName }}" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
            </div>

            <div>
                <label class="block font-medium text-stone-700 mb-1">Phone Number</label>
                <input type="text" name="phone" value="{{ $customerPhone }}" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
            </div>

            <div class="flex justify-end gap-2.5 pt-3 border-t border-stone-100">
                <button type="button" onclick="document.getElementById('editCustomerModal').close()" class="rounded-xl border border-stone-200 bg-white px-4 py-2 text-xs font-semibold text-stone-700 hover:bg-stone-50">
                    Cancel
                </button>
                <button type="submit" class="rounded-xl bg-[#204b34] px-4 py-2 text-xs font-semibold text-white hover:bg-[#183927]">
                    Save Details
                </button>
            </div>
        </form>
    </div>
</dialog>

{{-- Cancel Order Modal --}}
<dialog id="cancelModal" class="rounded-2xl border border-stone-200 bg-white p-0 shadow-2xl backdrop:bg-black/40 w-full max-w-md">
    <div class="p-6">
        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-rose-100 text-rose-700 mb-4">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-base font-bold text-stone-900">Cancel Order #{{ $order->order_number }}</h3>
        <p class="mt-2 text-xs text-stone-500">
            Are you sure you want to cancel this order? This will stop fulfillment and adjust order status.
        </p>

        <form method="POST" action="{{ route('admin.orders.cancel', $order) }}" class="mt-4 space-y-3 text-xs">
            @csrf
            <div>
                <label class="block font-medium text-stone-700 mb-1">Reason for cancellation</label>
                <textarea name="reason" rows="2" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800" placeholder="e.g. Customer requested cancellation before dispatch"></textarea>
            </div>

            <label class="flex items-center gap-2 cursor-pointer pt-1">
                <input type="checkbox" name="restock" value="1" checked class="rounded border-stone-300 text-[#204b34]">
                <span class="text-stone-700">Restock inventory for items in this order</span>
            </label>

            <div class="flex justify-end gap-2.5 pt-3 border-t border-stone-100">
                <button type="button" onclick="document.getElementById('cancelModal').close()" class="rounded-xl border border-stone-200 bg-white px-4 py-2 text-xs font-semibold text-stone-700 hover:bg-stone-50">
                    Nevermind
                </button>
                <button type="submit" class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-semibold text-white hover:bg-rose-700">
                    Confirm Cancellation
                </button>
            </div>
        </form>
    </div>
</dialog>

{{-- Refund Order Modal --}}
<dialog id="refundModal" class="rounded-2xl border border-stone-200 bg-white p-0 shadow-2xl backdrop:bg-black/40 w-full max-w-md">
    <div class="p-6">
        <h3 class="text-base font-bold text-stone-900 mb-1">Process Order Refund</h3>
        @php
            $refundableBalance = round((float)$order->grand_total - (float)($order->refund_amount ?? 0), 2);
        @endphp
        <p class="text-xs text-stone-500 mb-4">
            Refundable balance: <strong>₹{{ number_format($refundableBalance, 2) }}</strong>
        </p>

        <form method="POST" action="{{ route('admin.orders.refund', $order) }}" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="block font-medium text-stone-700 mb-1">Refund Type</label>
                <select name="refund_type" id="refund_type" onchange="document.getElementById('custom_refund_div').style.display = this.value === 'partial' ? 'block' : 'none'" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
                    <option value="full">Full Refund (₹{{ number_format($refundableBalance, 2) }})</option>
                    <option value="partial">Partial Refund</option>
                </select>
            </div>

            <div id="custom_refund_div" style="display: none;">
                <label class="block font-medium text-stone-700 mb-1">Partial Refund Amount (₹)</label>
                <input type="number" step="0.01" min="0.01" max="{{ $refundableBalance }}" name="amount" placeholder="0.00" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
            </div>

            <div>
                <label class="block font-medium text-stone-700 mb-1">Mode</label>
                <select name="mode" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800">
                    <option value="manual">Manual / Bank Transfer / Store Credit</option>
                    <option value="online">Original Payment Gateway (Online)</option>
                </select>
            </div>

            <div>
                <label class="block font-medium text-stone-700 mb-1">Reason for refund</label>
                <textarea name="reason" rows="2" required placeholder="e.g. Item out of stock / Customer returned item" class="w-full rounded-xl border border-stone-200 p-2.5 text-xs text-stone-800"></textarea>
            </div>

            <div class="rounded-xl border border-stone-200 bg-stone-50/70 p-3">
                <label class="flex items-start gap-2.5 cursor-pointer select-none">
                    <input type="checkbox" name="restock" value="1" checked class="h-4 w-4 rounded border-stone-300 text-purple-700 focus:ring-purple-600 mt-0.5">
                    <div>
                        <span class="font-semibold text-stone-800 text-xs">Restock items to inventory</span>
                        <p class="text-[10px] text-stone-500 leading-tight mt-0.5">Automatically releases reserved quantities and restores items back to active stock.</p>
                    </div>
                </label>
            </div>

            <div class="flex justify-end gap-2.5 pt-3 border-t border-stone-100">
                <button type="button" onclick="document.getElementById('refundModal').close()" class="rounded-xl border border-stone-200 bg-white px-4 py-2 text-xs font-semibold text-stone-700 hover:bg-stone-50">
                    Cancel
                </button>
                <button type="submit" class="rounded-xl bg-purple-700 px-4 py-2 text-xs font-semibold text-white hover:bg-purple-800">
                    Process Refund
                </button>
            </div>
        </form>
    </div>
</dialog>
@endsection
