@extends('layouts.app')
@section('content')
<div class="section">
    <div class="flex flex-wrap items-end justify-between gap-4"><div><span class="badge">Order tracking</span><h1 class="serif mt-4 text-5xl">{{ $order->order_number }}</h1></div><span class="rounded-full bg-stone-100 px-4 py-2 text-sm">{{ ucfirst(str_replace('_',' ',$order->shipment_status)) }}</span></div>
    <div class="mt-10 grid gap-6 lg:grid-cols-3">
        <div class="card"><div class="text-sm text-stone-500">Payment</div><div class="mt-2 text-xl font-bold">{{ ucfirst($order->payment_status) }}</div><div class="mt-2">₹{{ number_format($order->grand_total,2) }}</div></div>
        <div class="card"><div class="text-sm text-stone-500">Fulfilment</div><div class="mt-2 text-xl font-bold">{{ ucfirst($order->fulfilment_status) }}</div></div>
        <div class="card"><div class="text-sm text-stone-500">Shipment</div><div class="mt-2 text-xl font-bold">{{ $order->shipments->first()?->tracking_number ?: 'Preparing' }}</div></div>
    </div>
    <div class="card mt-6">
        <h2 class="text-xl font-bold">Tracking timeline</h2>
        <div class="mt-6 space-y-5">
        @forelse($order->shipments->flatMap->trackingEvents->sortByDesc('event_time') as $event)
            <div class="flex gap-4"><div class="mt-2 h-3 w-3 rounded-full bg-stone-800"></div><div><div class="font-semibold">{{ ucfirst(str_replace('_',' ',$event->status)) }}</div><div class="text-sm text-stone-500">{{ $event->event_time?->format('d M Y, h:i A') }} @if($event->location) · {{ $event->location }} @endif</div><div class="mt-1 text-sm">{{ $event->description }}</div></div></div>
        @empty <p class="text-stone-500">Tracking updates will appear here after dispatch.</p>@endforelse
        </div>
    </div>
</div>
@endsection
