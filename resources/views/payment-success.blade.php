@extends('layouts.app')
@section('content')
<div class="section max-w-3xl">
    @php($isCod = $order->payments->contains('gateway', 'cash_on_delivery'))
    <span class="badge">{{ $isCod ? 'Order confirmed' : 'Payment confirmed' }}</span>
    <h1 class="serif mt-4 text-5xl">Your order is confirmed.</h1>
    <div class="card mt-8">
        <div class="flex justify-between border-b pb-4"><span>Order</span><b>{{ $order->order_number }}</b></div>
        <div class="flex justify-between py-4"><span>Payment</span><span class="font-semibold {{ $isCod ? 'text-amber-700' : 'text-green-700' }}">{{ $isCod ? 'Cash on delivery' : 'Paid' }}</span></div>
        <div class="flex justify-between border-t pt-4"><span>Total</span><b>₹{{ number_format($order->grand_total,2) }}</b></div>
        <a class="btn-primary mt-7 inline-block" href="{{ route('account.order',$order) }}">Track my order</a>
    </div>
</div>
@endsection
