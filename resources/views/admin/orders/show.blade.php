@extends('layouts.admin')
@section('content')
<div class="p-6 md:p-10 max-w-6xl"><a class="underline text-sm" href="{{ route('admin.orders.index') }}">← Orders</a><h1 class="serif mt-4 text-4xl">{{ $order->order_number }}</h1>
<div class="mt-8 grid gap-6 lg:grid-cols-3">
<div class="card lg:col-span-2"><h2 class="font-bold">Items</h2><div class="mt-5 space-y-4">@foreach($order->orderItems as $i)<div class="flex justify-between border-b pb-3"><span>{{ $i->product_name_snapshot }} × {{ $i->quantity }}</span><span>₹{{ number_format($i->line_total,2) }}</span></div>@endforeach</div><div class="mt-5 text-right text-xl font-bold">₹{{ number_format($order->grand_total,2) }}</div></div>
<div class="card"><h2 class="font-bold">Update lifecycle</h2><form method="POST" action="{{ route('admin.orders.update',$order) }}" class="mt-5 space-y-4">@csrf @method('PUT')
@foreach(['payment_status'=>'Payment','fulfilment_status'=>'Fulfilment','shipment_status'=>'Shipment'] as $field=>$label)<div><label class="text-sm">{{ $label }}</label><select name="{{ $field }}" class="mt-2 w-full rounded-xl border p-3">@foreach(['pending','paid','failed','processing','packed','dispatched','delivered','cancelled','refunded'] as $s)<option value="{{ $s }}" @selected($order->$field===$s)>{{ $s }}</option>@endforeach</select></div>@endforeach
<button class="btn-primary w-full">Save status</button></form></div></div>
<div class="card mt-6"><h2 class="font-bold">Shipping address</h2><pre class="mt-4 whitespace-pre-wrap text-sm">{{ json_encode($order->shipping_address_json,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre></div>
</div>
@endsection
