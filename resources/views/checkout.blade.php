@extends('layouts.app')
@section('content')
<div class="section">
    <span class="badge">Checkout</span><h1 class="serif mt-4 text-5xl">Complete your order.</h1>
    @if($errors->any()) <div class="mt-5 rounded-xl bg-red-50 p-4 text-sm text-red-700">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div> @endif
    <form method="POST" action="{{ route('checkout.store') }}" class="mt-10 grid gap-8 lg:grid-cols-3">
        @csrf
        <div class="lg:col-span-2 card space-y-5">
            <h2 class="text-xl font-bold">Delivery address</h2>
            <input name="shipping_address[recipient_name]" required placeholder="Recipient name" class="w-full rounded-xl border p-3">
            <input name="shipping_address[phone]" required placeholder="Phone" class="w-full rounded-xl border p-3">
            <input name="shipping_address[address_line1]" required placeholder="Address line 1" class="w-full rounded-xl border p-3">
            <input name="shipping_address[address_line2]" placeholder="Address line 2" class="w-full rounded-xl border p-3">
            <div class="grid gap-4 md:grid-cols-3"><input name="shipping_address[city]" required placeholder="City" class="rounded-xl border p-3"><input name="shipping_address[state]" required placeholder="State" class="rounded-xl border p-3"><input name="shipping_address[postal_code]" required placeholder="PIN code" class="rounded-xl border p-3"></div>
            <input name="shipping_address[country]" value="India" placeholder="Country" class="w-full rounded-xl border p-3">

            <h2 class="pt-5 text-xl font-bold">Shipping method</h2>
            @foreach($methods as $method)
                <label class="flex cursor-pointer items-center justify-between rounded-2xl border p-4">
                    <span><input type="radio" name="shipping_method_id" value="{{ $method->id }}" class="mr-3" {{ $loop->first?'checked':'' }}>{{ $method->name }}</span>
                    <span class="text-sm text-stone-500">{{ $method->estimated_min_days }}–{{ $method->estimated_max_days }} days</span>
                </label>
            @endforeach

            <h2 class="pt-5 text-xl font-bold">Coupon</h2>
            <input name="coupon_code" placeholder="Coupon code (optional)" class="w-full rounded-xl border p-3">

            <h2 class="pt-5 text-xl font-bold">Payment method</h2>
            <label class="flex cursor-pointer items-center justify-between rounded-2xl border p-4">
                <span><input type="radio" name="payment_method" value="online" class="mr-3" @checked(request('payment_method', 'online') !== 'cod')>Pay online</span>
                <span class="text-sm text-stone-500">Secure Razorpay checkout</span>
            </label>
            <label class="flex cursor-pointer items-center justify-between rounded-2xl border p-4">
                <span><input type="radio" name="payment_method" value="cod" class="mr-3" @checked(request('payment_method') === 'cod')>Cash on delivery</span>
                <span class="text-sm text-stone-500">Pay when your order arrives</span>
            </label>

            <button class="btn-primary mt-5">Place order</button>
        </div>
        <aside class="card h-fit">
            <h2 class="text-xl font-bold">Order summary</h2>
            <div class="mt-5 space-y-4">
                @foreach($cart->cartItems as $item)
                <div class="flex justify-between text-sm"><span>{{ $item->productVariant->product->name }} × {{ $item->quantity }}</span><span>₹{{ number_format($item->unit_price_snapshot*$item->quantity,2) }}</span></div>
                @endforeach
            </div>
            <div class="mt-6 border-t pt-5 flex justify-between font-bold"><span>Subtotal</span><span>₹{{ number_format($cart->cartItems->sum(fn($i)=>$i->unit_price_snapshot*$i->quantity),2) }}</span></div>
        </aside>
    </form>
</div>
@endsection
