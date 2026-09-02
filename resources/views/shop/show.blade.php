@extends('layouts.app')
@section('content')
@php
    $images = $product->productImages
        ->filter(fn ($image) => $image->media)
        ->sortByDesc('is_primary')
        ->values();
    $primaryImage = $images->first();
    $galleryImages = $images->isNotEmpty()
        ? collect(range(0, 2))->map(fn ($index) => $images[$index % $images->count()])
        : collect();
    $variant = $product->variants->first();
    $price = $variant?->prices
        ->where('is_active', true)
        ->filter(fn ($item) => !$item->effective_from || $item->effective_from->isPast())
        ->filter(fn ($item) => !$item->effective_to || $item->effective_to->isFuture())
        ->sortByDesc('effective_from')
        ->first();
@endphp
<div class="section grid gap-10 lg:grid-cols-[1fr_1fr] lg:gap-14">
    <div>
        <div class="overflow-hidden rounded-xl bg-[var(--cream)]">
            @if($primaryImage)
                <img id="product-main-image" src="{{ $primaryImage->media->url }}" class="aspect-square h-full w-full object-cover" alt="{{ $product->name }}">
            @else
                <div class="aspect-square bg-stone-100"></div>
            @endif
        </div>
        @if($galleryImages->isNotEmpty())
            <div class="mt-3 grid grid-cols-3 gap-3">
                @foreach($galleryImages as $image)
                    <button type="button" class="product-gallery-thumbnail overflow-hidden rounded-lg bg-[var(--cream)] ring-offset-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--ink)] {{ $loop->first ? 'ring-1 ring-[var(--ink)]' : '' }}" data-product-image-src="{{ $image->media->url }}" data-product-image-alt="{{ $product->name }} image {{ $loop->iteration }}">
                        <img src="{{ $image->media->url }}" class="aspect-square h-full w-full object-cover" alt="">
                    </button>
                @endforeach
            </div>
        @endif
    </div>
    <div class="lg:py-4">
        <span class="badge">{{ $product->category->name ?? 'Guided Wellness' }}</span>
        <h1 class="display-serif mt-3 text-5xl leading-[1.05] md:text-6xl">{{ $product->name }}</h1>
        <p class="mt-5 max-w-md text-[15px] leading-7 text-stone-600">{{ $product->description ?: $product->short_description }}</p>
        @if($price)
            <div class="display-serif mt-5 text-3xl">₹{{ number_format((float) $price->selling_price, 0) }}</div>
        @endif
        <ul class="mt-6 space-y-1.5 text-[13px] leading-6 text-stone-600">
            <li>· 30 days of support</li>
            <li>· One capsule daily</li>
            <li>· Guided 30-day course access</li>
            <li>· Gut Response Brief at Day 30</li>
        </ul>
        @if($variant)
            <div class="mt-7 flex flex-wrap gap-3">
                @if($cartItem)
                    <div class="flex items-center rounded-full border border-stone-200 bg-white" data-cart-item="{{ $cartItem->id }}">
                        <button type="button" class="inline-flex h-10 w-10 items-center justify-center text-[var(--ink)] transition hover:bg-stone-50 disabled:cursor-not-allowed disabled:opacity-40" data-cart-quantity="decrease" aria-label="Decrease quantity" @disabled($cartItem->quantity <= 1)><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14" /></svg></button>
                        <output class="w-8 text-center text-sm font-semibold" data-cart-quantity-value>{{ $cartItem->quantity }}</output>
                        <button type="button" class="inline-flex h-10 w-10 items-center justify-center text-[var(--ink)] transition hover:bg-stone-50" data-cart-quantity="increase" aria-label="Increase quantity"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14" /></svg></button>
                    </div>
                    <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-stone-200 text-stone-600 transition hover:border-red-200 hover:bg-red-50 hover:text-red-700" data-cart-remove data-cart-item="{{ $cartItem->id }}" aria-label="Remove {{ $product->name }} from cart"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2m-9 0 1 14h8l1-14M10 10v6m4-6v6" /></svg></button>
                @else
                    <button data-add-cart="{{ $variant->id }}" data-product-add class="btn-primary">Add to cart</button>
                @endif
                <a href="{{ url('/#fit') }}" class="btn-secondary">Check Your Fit First</a>
                <a href="{{ route('checkout') }}" class="btn-primary">Buy Now</a>
            </div>
        @endif
        <div class="mt-6 space-y-1 text-[12px] leading-5 text-stone-500">
            <div>Free shipping across India.</div>
            <div>Ships in 2–3 business days.</div>
            <div>Review our <a class="underline underline-offset-2" href="{{ url('/#standards') }}">standards &amp; safety</a>.</div>
        </div>
    </div>
</div>

@if(isset($page) && $page->sections && $page->sections->count() > 0)
    <!-- DYNAMIC CMS SECTION RENDERER -->
    @foreach($page->sections->sortBy('sort_order') as $section)
        @php
            $settings = is_string($section->content) ? (json_decode($section->content, true)['settings'] ?? []) : [];
            if (!empty($settings['hidden_desktop']) && !empty($settings['hidden_mobile'])) continue; // simple skip
        @endphp
        
        @if($section->section_type === 'hero')
            <x-sections.hero :section="$section" />
        @elseif($section->section_type === 'problem')
            <x-sections.problem :section="$section" />
        @elseif($section->section_type === 'features')
            <x-sections.features :section="$section" />
        @elseif($section->section_type === 'standards')
            <x-sections.standards :section="$section" />
        @elseif($section->section_type === 'showcase')
            <x-sections.showcase :section="$section" />
        @elseif($section->section_type === 'testimonials')
            <x-sections.testimonials :section="$section" />
        @elseif($section->section_type === 'feature_cards')
            <x-sections.feature_cards :section="$section" />
        @elseif($section->section_type === 'learn')
            <x-sections.learn :section="$section" />
        @elseif($section->section_type === 'faq')
            <x-sections.faq :section="$section" />
        @elseif($section->section_type === 'cta')
            <x-sections.cta :section="$section" />
        @endif
    @endforeach
@endif
@endsection
