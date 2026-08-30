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

<section class="border-t border-stone-200 bg-[var(--cream)]/70">
    <div class="section">
        <h2 class="display-serif text-center text-4xl leading-tight md:text-5xl">What arrives with your 30-day experience.</h2>
        <div class="mt-10 grid gap-5 md:grid-cols-3 md:grid-rows-2">
            <div class="overflow-hidden rounded-xl bg-white md:row-span-2">
                @if($galleryImages->get(1))
                    <img src="{{ $galleryImages->get(1)->media->url }}" alt="{{ $product->name }} course kit" class="h-full min-h-64 w-full object-cover">
                @elseif($primaryImage)
                    <img src="{{ $primaryImage->media->url }}" alt="{{ $product->name }}" class="h-full min-h-64 w-full object-cover">
                @endif
            </div>
            <div class="rounded-lg bg-white p-5"><h3 class="display-serif text-xl">The Product</h3><p class="mt-2 text-[13px] leading-6 text-stone-600">A tightly sized 30-day supply, designed for one simple daily moment.</p></div>
            <div class="rounded-lg bg-white p-5"><h3 class="display-serif text-xl">Course Companion</h3><p class="mt-2 text-[13px] leading-6 text-stone-600">A concise guide covering activation, missed days, safety, and support.</p></div>
            <div class="rounded-lg bg-white p-5"><h3 class="display-serif text-xl">Permanent Course Access</h3><p class="mt-2 text-[13px] leading-6 text-stone-600">Start, resume, or return to your guided course whenever you need.</p></div>
            <div class="rounded-lg bg-white p-5"><h3 class="display-serif text-xl">Welcome Guide</h3><p class="mt-2 text-[13px] leading-6 text-stone-600">How to begin, what to expect, and how to read your response.</p></div>
        </div>
    </div>
</section>

<section class="bg-[var(--ink)] text-white">
    <div class="section">
        <h2 class="display-serif max-w-3xl text-4xl leading-[1.12] md:text-5xl">The product creates the response.<br>The guided course makes it readable.</h2>
        <div class="mt-10 grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border border-white/15 bg-white/5 p-6"><div class="text-[11px] font-medium uppercase tracking-[.16em] text-white/70">The Daily Product</div><ul class="mt-4 space-y-1.5 text-[13px] leading-6 text-white/85"><li>· A consistent daily routine</li><li>· Designed for 30 days of support</li><li>· Clear use and storage guidance</li></ul></div>
            <div class="rounded-lg border border-white/15 bg-white/5 p-6"><div class="text-[11px] font-medium uppercase tracking-[.16em] text-white/70">The Guided Experience</div><ul class="mt-4 space-y-1.5 text-[13px] leading-6 text-white/85"><li>· A few short course moments</li><li>· Around 15 minutes in total</li><li>· A personal response brief at Day 30</li></ul></div>
        </div>
        <a href="{{ url('/#how-it-works') }}" class="btn-secondary mt-8 !border-white/30 !text-white hover:!bg-white/10">See how it works</a>
    </div>
</section>

<section class="section">
    <h2 class="display-serif text-4xl leading-tight md:text-5xl">A simple routine designed for real life.</h2>
    <div class="mt-10 grid gap-4 md:grid-cols-4">
        <div class="rounded-lg bg-[var(--cream)] p-6"><div class="display-serif text-lg">01</div><h3 class="display-serif mt-4 text-2xl leading-tight">Use it daily</h3><p class="mt-2 text-[13px] leading-6 text-stone-600">Follow the instructions on your product with water.</p></div>
        <div class="rounded-lg bg-[var(--cream)] p-6"><div class="display-serif text-lg">02</div><h3 class="display-serif mt-4 text-2xl leading-tight">A few course moments</h3><p class="mt-2 text-[13px] leading-6 text-stone-600">Short check-ins keep the experience light and useful.</p></div>
        <div class="rounded-lg bg-[var(--cream)] p-6"><div class="display-serif text-lg">03</div><h3 class="display-serif mt-4 text-2xl leading-tight">Complete 30 days</h3><p class="mt-2 text-[13px] leading-6 text-stone-600">Real-life disruptions are useful context, not failure.</p></div>
        <div class="rounded-lg bg-[var(--cream)] p-6"><div class="display-serif text-lg">04</div><h3 class="display-serif mt-4 text-2xl leading-tight">Receive your brief</h3><p class="mt-2 text-[13px] leading-6 text-stone-600">Get an honest read and a relevant next step at Day 30.</p></div>
    </div>
</section>

<section class="bg-[var(--cream)]/70">
    <div class="section grid items-center gap-10 lg:grid-cols-2 lg:gap-16">
        <div class="overflow-hidden rounded-xl bg-white">
            @if($galleryImages->get(2))
                <img src="{{ $galleryImages->get(2)->media->url }}" alt="{{ $product->name }} response brief" class="aspect-[4/3] h-full w-full object-cover">
            @elseif($primaryImage)
                <img src="{{ $primaryImage->media->url }}" alt="{{ $product->name }}" class="aspect-[4/3] h-full w-full object-cover">
            @endif
        </div>
        <div><h2 class="display-serif text-4xl leading-tight md:text-5xl">What you receive at Day 30</h2><p class="mt-5 max-w-md text-[14px] leading-7 text-stone-600">The Gut Response Brief brings together your product routine, course moments, and real-life context into one useful, honest read.</p><ul class="mt-6 space-y-1.5 text-[13px] leading-6 text-stone-600"><li>· Observations, not conclusions</li><li>· A recommended next step</li></ul><a href="{{ url('/#how-it-works') }}" class="btn-secondary mt-7">See a sample</a></div>
    </div>
</section>

<section class="section">
    <h2 class="display-serif text-center text-3xl leading-tight md:text-4xl">Product information you should be able to inspect.</h2>
    <div class="mx-auto mt-8 grid max-w-4xl gap-6 text-center sm:grid-cols-3">
        <div><h3 class="text-[11px] font-medium uppercase tracking-[.14em] text-[var(--ink)]">Ingredients &amp; Form</h3><p class="mt-2 text-[13px] leading-6 text-stone-600">{{ $product->ingredients ?: 'Ingredients and format are disclosed clearly.' }}</p></div>
        <div><h3 class="text-[11px] font-medium uppercase tracking-[.14em] text-[var(--ink)]">Suitability Notes</h3><p class="mt-2 text-[13px] leading-6 text-stone-600">{{ $product->warnings ?: 'Review safety and fit before starting.' }}</p></div>
        <div><h3 class="text-[11px] font-medium uppercase tracking-[.14em] text-[var(--ink)]">Usage &amp; Storage</h3><p class="mt-2 text-[13px] leading-6 text-stone-600">{{ $product->usage_instructions ?: 'Follow the label and store in a cool, dry place.' }}</p></div>
    </div>
</section>

<section id="fit" class="border-t border-stone-200 bg-[var(--cream)]/70">
    <div class="section"><h2 class="display-serif text-center text-3xl leading-tight md:text-4xl">Check whether this product is likely to be right for you.</h2><div class="mx-auto mt-8 grid max-w-4xl gap-4 md:grid-cols-2"><div class="rounded-lg bg-white p-6"><div class="text-[11px] font-medium uppercase tracking-[.18em] text-stone-500">May fit</div><ul class="mt-4 space-y-1.5 text-[13px] leading-6 text-stone-600"><li>· Looking for a structured daily routine</li><li>· Want to observe your response over time</li><li>· Value a clear, lighter process</li></ul></div><div class="rounded-lg bg-white p-6"><div class="text-[11px] font-medium uppercase tracking-[.18em] text-stone-500">Doctor first</div><ul class="mt-4 space-y-1.5 text-[13px] leading-6 text-stone-600"><li>· Pregnant or breast-feeding</li><li>· Diagnosed condition or new severe symptoms</li><li>· Taking prescription medication</li><li>· Under 18</li></ul></div></div></div>
</section>

<section class="section"><div class="mx-auto max-w-3xl"><h2 class="display-serif text-center text-3xl leading-tight md:text-4xl">Frequently Asked Questions</h2><div class="mt-8 divide-y divide-stone-200 border-y border-stone-200"><details class="group py-5" open><summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-[15px]">What is included with my order?<span class="text-xl text-stone-500 group-open:rotate-45">+</span></summary><p class="pt-3 pr-8 text-[13px] leading-6 text-stone-600">Your product, a Course Companion, and access to the guided 30-day experience.</p></details><details class="group py-5"><summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-[15px]">How do I access the guided experience?<span class="text-xl text-stone-500 group-open:rotate-45">+</span></summary><p class="pt-3 pr-8 text-[13px] leading-6 text-stone-600">Use the course access details included with your order to begin, resume, or return.</p></details><details class="group py-5"><summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-[15px]">What if I miss a day?<span class="text-xl text-stone-500 group-open:rotate-45">+</span></summary><p class="pt-3 pr-8 text-[13px] leading-6 text-stone-600">Continue when you can. A missed day is part of the context considered in your experience.</p></details><details class="group py-5"><summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-[15px]">What happens at Day 30?<span class="text-xl text-stone-500 group-open:rotate-45">+</span></summary><p class="pt-3 pr-8 text-[13px] leading-6 text-stone-600">You receive a Gut Response Brief that summarises what you observed and suggests a next step.</p></details></div></div></section>

<section class="bg-[var(--ink)] text-center text-white">
    <div class="section">
        <h2 class="display-serif text-4xl leading-tight md:text-5xl">Ready to give it a fair 30-day trial?</h2>
        <p class="mx-auto mt-4 max-w-md text-[14px] leading-7 text-white/80">Start the course and receive a Gut Response Brief at Day 30.</p>
        <div class="mt-7 flex flex-wrap justify-center gap-3">
            @if($variant)
                @if($cartItem)
                    <div class="flex items-center rounded-full border border-white/30 bg-white text-[var(--ink)]" data-cart-item="{{ $cartItem->id }}">
                        <button type="button" class="inline-flex h-10 w-10 items-center justify-center transition hover:bg-stone-100 disabled:cursor-not-allowed disabled:opacity-40" data-cart-quantity="decrease" aria-label="Decrease quantity" @disabled($cartItem->quantity <= 1)><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14" /></svg></button>
                        <output class="w-8 text-center text-sm font-semibold" data-cart-quantity-value>{{ $cartItem->quantity }}</output>
                        <button type="button" class="inline-flex h-10 w-10 items-center justify-center transition hover:bg-stone-100" data-cart-quantity="increase" aria-label="Increase quantity"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14" /></svg></button>
                    </div>
                    <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/30 text-white transition hover:bg-white/10" data-cart-remove data-cart-item="{{ $cartItem->id }}" aria-label="Remove {{ $product->name }} from cart"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2m-9 0 1 14h8l1-14M10 10v6m4-6v6" /></svg></button>
                @else
                    <button data-add-cart="{{ $variant->id }}" data-product-add class="btn-primary !bg-white !text-[var(--ink)]">Buy Now</button>
                @endif
            @endif
            <a href="#fit" class="btn-secondary !border-white/30 !text-white hover:!bg-white/10">Check Fit</a>
        </div>
    </div>
</section>
@endsection
