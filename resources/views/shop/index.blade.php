@extends('layouts.app')
@section('content')
<div class="section">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div><span class="badge">Shop</span><h1 class="serif mt-4 text-5xl">{{ $isMyClosq ? '30-Day My CLOSQ' : '30-Day Gut Reset' }}</h1></div>
        <form><input name="search" value="{{ request('search') }}" class="rounded-full border px-5 py-3" placeholder="Search products"><button class="btn-primary ml-2">Search</button></form>
    </div>
    <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($products as $product)
        <article class="card overflow-hidden p-0">
            <a href="{{ route('product.show',$product) }}" class="block aspect-square bg-stone-100">
                <img src="{{ $product->productImages->first()?->media?->url ?: 'https://guided-gut-reset-lovable-app.lovable.app/assets/hero-product-CftTmpl1.jpg' }}" class="h-full w-full object-cover" alt="{{ $product->name }}" onerror="this.onerror=null;this.src='https://guided-gut-reset-lovable-app.lovable.app/assets/hero-product-CftTmpl1.jpg';">
            </a>
            <div class="p-6">
                <div class="text-xs uppercase tracking-widest text-stone-500">{{ ($isMyClosq && ($product->category->name ?? '') === 'Gut Reset') ? 'My CLOSQ' : ($product->category->name ?? ($isMyClosq ? 'My CLOSQ' : 'Gut Reset')) }}</div>
                <h2 class="mt-2 text-xl font-bold">{{ $product->name }}</h2>
                <p class="mt-2 text-sm text-stone-600">{{ $product->short_description }}</p>
            </div>
        </article>
        @endforeach
    </div>
    <div class="mt-8">{{ $products->links() }}</div>
</div>
@endsection
