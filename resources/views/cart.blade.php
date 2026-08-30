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
                <div class="card flex flex-wrap items-center justify-between gap-4" data-cart-item="{{ $item->id }}">
                    <div>
                        <div class="font-bold">{{ $item->productVariant->product->name }}</div>
                        <div class="text-sm text-stone-500">{{ $item->productVariant->sku }}</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex items-center rounded-full border border-stone-200 bg-white">
                            <button type="button" class="inline-flex h-10 w-10 items-center justify-center text-lg text-[var(--ink)] transition hover:bg-stone-50 disabled:cursor-not-allowed disabled:opacity-40" data-cart-quantity="decrease" aria-label="Decrease quantity" @disabled($item->quantity <= 1)>&minus;</button>
                            <output class="w-8 text-center text-sm font-semibold" data-cart-quantity-value>{{ $item->quantity }}</output>
                            <button type="button" class="inline-flex h-10 w-10 items-center justify-center text-lg text-[var(--ink)] transition hover:bg-stone-50" data-cart-quantity="increase" aria-label="Increase quantity">+</button>
                        </div>
                        <button type="button" class="text-sm font-semibold text-red-700 underline-offset-4 transition hover:underline" data-cart-remove aria-label="Remove {{ $item->productVariant->product->name }} from cart">Remove</button>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-8 flex flex-wrap justify-end gap-3 border-t border-stone-200 pt-6">
                <a href="{{ route('checkout') }}" class="btn-primary">Pay online</a>
                <a href="{{ route('checkout', ['payment_method' => 'cod']) }}" class="btn-secondary">Cash on delivery</a>
            </div>
        @endif
    @endguest
</div>
@endsection
