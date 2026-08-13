@extends('layouts.app')
@section('content')
<div class="section grid gap-12 md:grid-cols-2">
    <div class="aspect-square rounded-[2rem] bg-stone-100">
        @if($product->productImages->first()?->media)
            <img src="{{ $product->productImages->first()?->media->url }}" class="h-full w-full rounded-[2rem] object-cover" alt="{{ $product->name }}">
        @endif
    </div>
    <div class="py-5">
        <span class="badge">30-Day Guided Gut Reset</span>
        <h1 class="serif mt-5 text-5xl">{{ $product->name }}</h1>
        <p class="mt-5 text-lg leading-8 text-stone-600">{{ $product->description }}</p>
        <div class="mt-7 space-y-3">
            <div>✓ 30 days of support</div>
            <div>✓ Guided 30-day course</div>
            <div>✓ Gut Response Brief</div>
        </div>
        @if($product->variants->first())
        <button data-add-cart="{{ $product->variants->first()->id }}" class="btn-primary mt-8">Add to cart</button>
        @endif
    </div>
</div>
@endsection
