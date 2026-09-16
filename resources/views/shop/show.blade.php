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

@php
    $cmsPage = \App\Models\CmsPage::where('slug', 'product-page-content')->with('sections.media')->first();
@endphp

@if($cmsPage && $cmsPage->sections && $cmsPage->sections->count() > 0)
    @foreach($cmsPage->sections->sortBy('sort_order') as $section)
        @php
            $contentData = is_string($section->content) ? json_decode($section->content, true) : ($section->content ?? []);
            if (!is_array($contentData)) $contentData = [];
            $settings = $contentData['settings'] ?? [];
            if (!empty($settings['hidden_desktop']) && !empty($settings['hidden_mobile'])) continue;
        @endphp

        @if($section->section_type === 'product_arrives')
            <section class="border-t border-stone-200 bg-[var(--cream)]/70">
                <div class="section">
                    <h2 class="display-serif text-center text-4xl leading-tight md:text-5xl">{!! $section->title !!}</h2>
                    <div class="mt-10 grid gap-5 md:grid-cols-3 md:grid-rows-2">
                        <div class="overflow-hidden rounded-xl bg-white md:row-span-2">
                            @if($section->media)
                                <img src="{{ $section->media->storage_path ? asset('storage/'.$section->media->storage_path) : $section->media->url }}" class="h-full min-h-64 w-full object-cover" alt="{{ $section->title }}">
                            @elseif($galleryImages->get(1))
                                <img src="{{ $galleryImages->get(1)->media->url }}" alt="{{ $product->name }} course kit" class="h-full min-h-64 w-full object-cover">
                            @elseif($primaryImage)
                                <img src="{{ $primaryImage->media->url }}" alt="{{ $product->name }}" class="h-full min-h-64 w-full object-cover">
                            @endif
                        </div>
                        @foreach($contentData['cards'] ?? [] as $card)
                            <div class="rounded-lg bg-white p-5"><h3 class="display-serif text-xl">{{ $card['title'] ?? '' }}</h3><p class="mt-2 text-[13px] leading-6 text-stone-600">{{ $card['text'] ?? '' }}</p></div>
                        @endforeach
                    </div>
                </div>
            </section>
        
        @elseif($section->section_type === 'product_creates_response')
            <section class="bg-[var(--ink)] text-white">
                <div class="section">
                    <h2 class="display-serif max-w-3xl text-4xl leading-[1.12] md:text-5xl">{!! $section->title !!}</h2>
                    <div class="mt-10 grid gap-4 md:grid-cols-2">
                        @foreach($contentData['cards'] ?? [] as $card)
                            <div class="rounded-lg border border-white/15 bg-white/5 p-6"><div class="text-[11px] font-medium uppercase tracking-[.16em] text-white/70">{{ $card['badge'] ?? '' }}</div><ul class="mt-4 space-y-1.5 text-[13px] leading-6 text-white/85">
                                @foreach($card['items'] ?? [] as $item) <li>· {{ $item }}</li> @endforeach
                            </ul></div>
                        @endforeach
                    </div>
                    <a href="{{ $contentData['button_url'] ?? '#' }}" class="btn-secondary mt-8 hover:">{{ $contentData['button'] ?? 'See how it works' }}</a>
                </div>
            </section>

        @elseif($section->section_type === 'product_routine')
            <section class="section">
                <h2 class="display-serif text-4xl leading-tight md:text-5xl">{!! $section->title !!}</h2>
                <div class="mt-10 grid gap-4 md:grid-cols-4">
                    @foreach($contentData['cards'] ?? [] as $card)
                        <div class="rounded-lg bg-[var(--cream)] p-6"><div class="display-serif text-lg">{{ $card['num'] ?? '' }}</div><h3 class="display-serif mt-4 text-2xl leading-tight">{{ $card['title'] ?? '' }}</h3><p class="mt-2 text-[13px] leading-6 text-stone-600">{{ $card['text'] ?? '' }}</p></div>
                    @endforeach
                </div>
            </section>

        @elseif($section->section_type === 'product_receive_day30')
            <section class="bg-[var(--cream)]/70">
                <div class="section grid items-center gap-10 lg:grid-cols-2 lg:gap-16">
                    <div class="overflow-hidden rounded-xl bg-white">
                        @if($section->media)
                            <img src="{{ $section->media->storage_path ? asset('storage/'.$section->media->storage_path) : $section->media->url }}" class="aspect-[4/3] h-full w-full object-cover" alt="{{ $section->title }}">
                        @elseif($galleryImages->get(2))
                            <img src="{{ $galleryImages->get(2)->media->url }}" alt="{{ $product->name }} response brief" class="aspect-[4/3] h-full w-full object-cover">
                        @elseif($primaryImage)
                            <img src="{{ $primaryImage->media->url }}" alt="{{ $product->name }}" class="aspect-[4/3] h-full w-full object-cover">
                        @endif
                    </div>
                    <div><h2 class="display-serif text-4xl leading-tight md:text-5xl">{!! $section->title !!}</h2><p class="mt-5 max-w-md text-[14px] leading-7 text-stone-600">{{ $section->subtitle }}</p><ul class="mt-6 space-y-1.5 text-[13px] leading-6 text-stone-600">
                        @foreach($contentData['items'] ?? [] as $item) <li>· {{ $item }}</li> @endforeach
                    </ul><a href="{{ $contentData['button_url'] ?? '#' }}" class="btn-secondary mt-7">{{ $contentData['button'] ?? 'See a sample' }}</a></div>
                </div>
            </section>
        
        @elseif($section->section_type === 'product_inspect')
            <section class="section">
                <h2 class="display-serif text-center text-3xl leading-tight md:text-4xl">{!! $section->title !!}</h2>
                <div class="mx-auto mt-8 grid max-w-4xl gap-6 text-center sm:grid-cols-3">
                    @foreach($contentData['cards'] ?? [] as $card)
                        <div><h3 class="text-[11px] font-medium uppercase tracking-[.14em] text-[var(--ink)]">{{ $card['title'] ?? '' }}</h3><p class="mt-2 text-[13px] leading-6 text-stone-600">{{ $card['text'] ?? '' }}</p></div>
                    @endforeach
                </div>
            </section>
        
        @elseif($section->section_type === 'product_check_fit')
            <section id="fit" class="border-t border-stone-200 bg-[var(--cream)]/70">
                <div class="section"><h2 class="display-serif text-center text-3xl leading-tight md:text-4xl">{!! $section->title !!}</h2><div class="mx-auto mt-8 grid max-w-4xl gap-4 md:grid-cols-2"><div class="rounded-lg bg-white p-6"><div class="text-[11px] font-medium uppercase tracking-[.18em] text-stone-500">{{ $contentData['col1_badge'] ?? 'May fit' }}</div><ul class="mt-4 space-y-1.5 text-[13px] leading-6 text-stone-600">
                    @foreach($contentData['col1_items'] ?? [] as $item) <li>· {{ $item }}</li> @endforeach
                </ul></div><div class="rounded-lg bg-white p-6"><div class="text-[11px] font-medium uppercase tracking-[.18em] text-stone-500">{{ $contentData['col2_badge'] ?? 'Doctor first' }}</div><ul class="mt-4 space-y-1.5 text-[13px] leading-6 text-stone-600">
                    @foreach($contentData['col2_items'] ?? [] as $item) <li>· {{ $item }}</li> @endforeach
                </ul></div></div></div>
            </section>
        
        @elseif($section->section_type === 'product_faq')
            <section class="section"><div class="mx-auto max-w-3xl"><h2 class="display-serif text-center text-3xl leading-tight md:text-4xl">{!! $section->title !!}</h2><div class="mt-8 divide-y divide-stone-200 border-y border-stone-200">
                @foreach($contentData['items'] ?? [] as $item)
                <details class="group py-5" {{ $loop->first ? 'open' : '' }}><summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-[15px]">{{ $item['q'] ?? '' }}<span class="text-xl text-stone-500 group-open:rotate-45">+</span></summary><p class="pt-3 pr-8 text-[13px] leading-6 text-stone-600">{{ $item['a'] ?? '' }}</p></details>
                @endforeach
            </div></div></section>
        
        @elseif($section->section_type === 'product_cta')
            <section class="bg-[var(--ink)] text-center text-white" style="--btn-primary-color: {{ !empty($settings['button_primary_color']) ? $settings['button_primary_color'] : '#ffffff' }}; --btn-primary-text-color: {{ !empty($settings['button_primary_text_color']) ? $settings['button_primary_text_color'] : '#0b0f14' }}; --btn-secondary-color: {{ !empty($settings['button_secondary_color']) ? $settings['button_secondary_color'] : 'transparent' }}; --btn-secondary-text-color: {{ !empty($settings['button_secondary_text_color']) ? $settings['button_secondary_text_color'] : '#ffffff' }}; {{ $styleAttr ?? '' }}">
                <div class="section">
                    <h2 class="display-serif text-4xl leading-tight md:text-5xl">{!! $section->title !!}</h2>
                    <p class="mx-auto mt-4 max-w-md text-[14px] leading-7 text-white/80">{{ $section->subtitle }}</p>
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
                                <button data-add-cart="{{ $variant->id }}" data-product-add class="btn-primary">Buy Now</button>
                            @endif
                        @endif
                        <a href="{{ $contentData['button_2_url'] ?? '#fit' }}" class="btn-secondary hover:">{{ $contentData['button_2'] ?? 'Check Fit' }}</a>
                    </div>
                </div>
            </section>
        
        @endif
        
    @endforeach
@endif
@endsection
