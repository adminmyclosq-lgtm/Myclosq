@extends('layouts.app')
@section('content')
<div class="section">
    <span class="badge">Cart</span><h1 class="serif mt-4 text-5xl">Your cart</h1>
    @guest
        <div class="mt-8 card"><p>Please sign in to manage your cart.</p><a class="btn-primary mt-5" href="{{ route('login') }}">Sign in</a></div>
    @else
        @if(!$cart || $cart->cartItems->isEmpty())
            <div class="mt-8 text-stone-600">Your cart is empty. <a class="underline" href="{{ route('shop') }}">Explore the reset.</a></div>
        @else
            <div class="mt-10 space-y-4">
                @foreach($cart->cartItems as $item)
                <div class="card flex items-center justify-between">
                    <div><div class="font-bold">{{ $item->productVariant->product->name }}</div><div class="text-sm text-stone-500">{{ $item->productVariant->sku }}</div></div>
                    <div>Qty {{ $item->quantity }}</div>
                </div>
                @endforeach
            </div>
        @endif
    @endguest
</div>
@endsection
