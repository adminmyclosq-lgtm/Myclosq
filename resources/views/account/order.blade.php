@extends('layouts.customer')

@section('content')
<div class="mx-auto max-w-[1400px] px-5 py-8 md:px-10 md:py-10 space-y-6">

    <!-- Back to Orders Navigation -->
    <div>
        <a href="{{ route('account') }}#orders" class="inline-flex items-center gap-1.5 text-xs font-semibold text-stone-500 hover:text-stone-900 transition">
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            <span>Back to Order History</span>
        </a>
    </div>

    <!-- Order Header -->
    <div class="flex flex-wrap items-end justify-between gap-4 border-b border-stone-200 pb-5">
        <div>
            <div class="inline-flex items-center gap-2">
                <span class="rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-xs font-semibold text-emerald-800">Order #{{ $order->order_number }}</span>
                <span class="text-xs text-stone-400">Placed on {{ $order->created_at?->format('d M Y, h:i A') }}</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-stone-900 mt-2">Order Tracking &amp; Invoice</h1>
        </div>
        <span class="rounded-full bg-stone-100 px-3.5 py-1 text-xs font-semibold text-stone-700">
            {{ ucfirst(str_replace('_',' ',$order->shipment_status ?? 'processing')) }}
        </span>
    </div>

    <!-- Order Summary Cards -->
    <div class="grid gap-5 sm:grid-cols-3">
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-2xs">
            <div class="text-[10px] font-bold uppercase tracking-wider text-stone-400">Payment Status</div>
            <div class="mt-2 text-xl font-bold text-stone-900">{{ ucfirst($order->payment_status) }}</div>
            <div class="mt-2 text-xs font-semibold text-emerald-800">Total: ₹{{ number_format($order->grand_total, 2) }}</div>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-2xs">
            <div class="text-[10px] font-bold uppercase tracking-wider text-stone-400">Fulfilment</div>
            <div class="mt-2 text-xl font-bold text-stone-900">{{ ucfirst($order->fulfilment_status) }}</div>
            <div class="mt-2 text-xs text-stone-500">{{ $order->orderItems->count() }} item(s) included</div>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-2xs">
            <div class="text-[10px] font-bold uppercase tracking-wider text-stone-400">Shipment Tracking</div>
            <div class="mt-2 text-xl font-bold text-stone-900">{{ $order->shipments->first()?->tracking_number ?: 'Preparing Dispatch' }}</div>
            <div class="mt-2 text-xs text-stone-500">{{ $order->shipments->first()?->carrier ?: 'Standard Courier' }}</div>
        </div>
    </div>

    <!-- Items in Order -->
    <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-2xs">
        <h2 class="text-base font-bold text-stone-900 border-b border-stone-100 pb-3 mb-4">Items in this Order</h2>
        <div class="divide-y divide-stone-100 text-xs">
            @foreach($order->orderItems as $item)
                <div class="py-3 flex items-center justify-between">
                    <div>
                        <div class="font-semibold text-stone-900">{{ $item->product_name ?? 'Product Item' }}</div>
                        <div class="text-[11px] text-stone-400">Qty: {{ $item->quantity }} × ₹{{ number_format($item->unit_price, 2) }}</div>
                    </div>
                    <div class="font-semibold text-stone-900">
                        ₹{{ number_format($item->subtotal ?? ($item->unit_price * $item->quantity), 2) }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Tracking Timeline -->
    <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-2xs">
        <h2 class="text-base font-bold text-stone-900 border-b border-stone-100 pb-3 mb-4">Tracking Timeline</h2>
        <div class="space-y-5">
            @forelse($order->shipments->flatMap->trackingEvents->sortByDesc('event_time') as $event)
                <div class="flex gap-4">
                    <div class="mt-1 h-2.5 w-2.5 rounded-full bg-emerald-600 shrink-0"></div>
                    <div>
                        <div class="font-semibold text-xs text-stone-900">{{ ucfirst(str_replace('_',' ',$event->status)) }}</div>
                        <div class="text-[11px] text-stone-400">{{ $event->event_time?->format('d M Y, h:i A') }} @if($event->location) · {{ $event->location }} @endif</div>
                        <div class="mt-0.5 text-xs text-stone-600">{{ $event->description }}</div>
                    </div>
                </div>
            @empty
                <p class="text-xs text-stone-500">Tracking updates will appear here once your package is dispatched by our warehouse.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection
