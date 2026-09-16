@extends('layouts.app')

@section('content')
@if(isset($page) && $page->sections && $page->sections->count() > 0)
    @foreach($page->sections->sortBy('sort_order') as $section)
        @php
            $contentData = is_string($section->content) ? json_decode($section->content, true) : ($section->content ?? []);
            if (!is_array($contentData)) $contentData = [];
                        $settings = $contentData['settings'] ?? [];
            if (!empty($settings['hidden_desktop']) && !empty($settings['hidden_mobile'])) continue;
            
            $imageUrl = $section->media ? ($section->media->storage_path ? asset('storage/'.$section->media->storage_path) : $section->media->url) : '';

            $topSpacing = $settings['top_spacing'] ?? '0px';
            $bottomSpacing = $settings['bottom_spacing'] ?? '0px';
            $topStyle = $topSpacing !== '0px' ? "padding-top: $topSpacing;" : "";
            $bottomStyle = $bottomSpacing !== '0px' ? "padding-bottom: $bottomSpacing;" : "";
            
            $bgColor = $settings['bg_color'] ?? '';
            $bgClass = is_string($bgColor) && $bgColor !== '' && str_starts_with($bgColor, 'bg-') ? $bgColor : '';
            $bgStyle = is_string($bgColor) && $bgColor !== '' && !str_starts_with($bgColor, 'bg-') ? "background-color: $bgColor;" : "";

            $btnPrimaryVar = !empty($settings['button_primary_color']) ? "--btn-primary-color: {$settings['button_primary_color']};" : "";
            $btnPrimaryTextVar = !empty($settings['button_primary_text_color']) ? "--btn-primary-text-color: {$settings['button_primary_text_color']};" : "";
            $btnSecondaryVar = !empty($settings['button_secondary_color']) ? "--btn-secondary-color: {$settings['button_secondary_color']};" : "";
            $btnSecondaryTextVar = !empty($settings['button_secondary_text_color']) ? "--btn-secondary-text-color: {$settings['button_secondary_text_color']};" : "";
            
            $styleAttr = trim("$topStyle $bottomStyle $bgStyle $btnPrimaryVar $btnPrimaryTextVar $btnSecondaryVar $btnSecondaryTextVar");

            $titleFont = isset($settings['title_font_family']) ? $settings['title_font_family'] : '';
            $titleSize = isset($settings['title_font_size']) ? $settings['title_font_size'] : '';
            $titleColor = isset($settings['title_font_color']) && $settings['title_font_color'] ? "color: {$settings['title_font_color']};" : '';

            $bodyFont = isset($settings['body_font_family']) ? $settings['body_font_family'] : '';
            $bodySize = isset($settings['body_font_size']) ? $settings['body_font_size'] : '';
            $bodyColor = isset($settings['body_font_color']) && $settings['body_font_color'] ? "color: {$settings['body_font_color']};" : '';
        @endphp
        @if($section->section_type === 'hiw_hero')
            <section class="border-b border-stone-200 bg-white {{ $bgClass }}" style="{{ $styleAttr }}">
                <div class="section grid items-center gap-10 py-16 md:py-20 lg:grid-cols-[1.05fr_1fr] lg:gap-14">
                    <div>
                        <h1 class="display-serif text-5xl leading-[1.05] md:text-6xl">{!! nl2br(e($section->title)) !!}</h1>
                        <p class="mt-6 max-w-md text-[15px] leading-7 text-stone-600 {{ $bodyFont }} {{ $bodySize }}" style="{{ $bodyColor }}">{{ $section->subtitle }}</p>
                        <div class="mt-7 flex flex-wrap gap-3">
                            <a href="{{ $contentData['button_1_url'] ?? route('shop') }}" class="btn-primary">{{ $contentData['button_1'] ?? 'View Product' }}</a>
                            <a href="{{ $contentData['button_2_url'] ?? url('/#standards') }}" class="btn-secondary">{{ $contentData['button_2'] ?? 'Review Standards' }}</a>
                        </div>
                    </div>
                    <div class="overflow-hidden rounded-xl bg-[var(--cream)]">
                        <img src="{{ $imageUrl ?: 'https://guided-gut-reset-lovable-app.lovable.app/assets/hero-product-CftTmpl1.jpg' }}" alt="{{ strip_tags($section->title) }}" class="aspect-[4/3] h-full w-full object-cover">
                    </div>
                </div>
            </section>
        
        @elseif($section->section_type === 'hiw_product_layer')
            <section class="bg-[var(--ink)] text-white {{ $bgClass }}" style="{{ $styleAttr }}">
                <div class="section text-center">
                    <h2 class="display-serif mx-auto max-w-3xl text-4xl leading-[1.15] md:text-5xl">{!! nl2br(e($section->title)) !!}</h2>
                    <div class="mx-auto mt-9 grid max-w-3xl gap-4 text-left md:grid-cols-2">
                        <div class="rounded-lg border border-white/15 bg-white/5 p-6">
                            <div class="text-[11px] font-medium uppercase tracking-[.16em] text-white/70">{{ $contentData['col1_title'] ?? 'The Product' }}</div>
                            <ul class="mt-4 space-y-1.5 text-[13px] leading-6 text-white/85">
                                @foreach($contentData['col1_items'] ?? [] as $item) <li>· {{ $item }}</li> @endforeach
                            </ul>
                        </div>
                        <div class="rounded-lg border border-white/15 bg-white/5 p-6">
                            <div class="text-[11px] font-medium uppercase tracking-[.16em] text-white/70">{{ $contentData['col2_title'] ?? 'The Guided Layer' }}</div>
                            <ul class="mt-4 space-y-1.5 text-[13px] leading-6 text-white/85">
                                @foreach($contentData['col2_items'] ?? [] as $item) <li>· {{ $item }}</li> @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </section>
        
        @elseif($section->section_type === 'hiw_timeline')
            <section class="section {{ $bgClass }}" style="{{ $styleAttr }}">
                <h2 class="display-serif text-center text-4xl leading-tight md:text-5xl">{{ $section->title }}</h2>
                <div class="mt-12 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                    @foreach($contentData['cards'] ?? [] as $card)
                        <article class="rounded-lg border border-stone-200 bg-white p-4 text-center">
                            <div class="mx-auto grid h-8 w-8 place-items-center rounded-full bg-[var(--cream)] text-[var(--ink)]">•</div>
                            <div class="mt-3 text-[11px] font-medium uppercase tracking-[.14em] text-stone-500">{{ $card['day'] ?? '' }}</div>
                            <h3 class="display-serif mt-1 text-lg leading-snug">{{ $card['label'] ?? '' }}</h3>
                        </article>
                    @endforeach
                </div>
                <div class="mx-auto mt-10 max-w-md rounded-lg border border-stone-200 bg-[var(--cream)] p-4 text-center text-[13px] text-stone-600">{{ $section->subtitle }}</div>
            </section>
        
        @elseif($section->section_type === 'hiw_cards_grid')
            <section class="bg-[var(--cream)]/70 {{ $bgClass }}" style="{{ $styleAttr }}">
                <div class="section">
                    <h2 class="display-serif text-4xl leading-tight md:text-5xl">{{ $section->title }}</h2>
                    <p class="mt-4 max-w-2xl text-[14px] leading-7 text-stone-600 {{ $bodyFont }} {{ $bodySize }}" style="{{ $bodyColor }}">{{ $section->subtitle }}</p>
                    <div class="mt-8 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                        @foreach($contentData['cards'] ?? [] as $card)
                            <article class="rounded-lg bg-white p-5 border border-stone-200 shadow-[0_4px_10px_-5px_rgba(0,0,0,0.05)]">
                                <h3 class="display-serif text-xl">{{ $card['title'] ?? '' }}</h3>
                                <p class="mt-2 text-[12px] leading-6 text-stone-600">{{ $card['text'] ?? '' }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

        @elseif($section->section_type === 'hiw_cards_grid_flat')
            <section class="section {{ $bgClass }}" style="{{ $styleAttr }}">
                <h2 class="display-serif text-center text-4xl leading-tight md:text-5xl">{{ $section->title }}</h2>
                <div class="mx-auto mt-10 grid max-w-4xl gap-4 md:grid-cols-2">
                    @foreach($contentData['cards'] ?? [] as $card)
                        <article class="rounded-lg border border-stone-200 bg-white p-5 shadow-[0_4px_6px_-3px_rgba(0,0,0,0.03)]">
                            <h3 class="display-serif text-xl">{{ $card['title'] ?? '' }}</h3>
                            <p class="mt-2 text-[13px] text-stone-600">{{ $card['text'] ?? '' }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

        @elseif($section->section_type === 'hiw_honest_read')
            <section class="bg-[var(--ink)] text-white {{ $bgClass }}" style="{{ $styleAttr }}">
                <div class="section">
                    <h2 class="display-serif text-4xl leading-tight md:text-5xl">{{ $section->title }}</h2>
                    <ul class="mt-8 max-w-2xl space-y-4 text-[14px] leading-6 text-white/85">
                        @foreach($contentData['cards'] ?? [] as $item)
                            <li>· {{ is_array($item) ? $item['title'] ?? '' : $item }}</li>
                        @endforeach
                    </ul>
                </div>
            </section>
            
        @elseif($section->section_type === 'hiw_personal_brief')
            <section class="section grid items-center gap-10 lg:grid-cols-2 lg:gap-16 {{ $bgClass }}" style="{{ $styleAttr }}">
                <div class="overflow-hidden rounded-xl bg-[var(--cream)]">
                    <img src="{{ $imageUrl ?: 'https://guided-gut-reset-lovable-app.lovable.app/assets/bottle-capsules-COsDFlLa.jpg' }}" alt="Sample Brief" class="aspect-[4/3] h-full w-full object-cover">
                </div>
                <div>
                    <h2 class="display-serif text-4xl leading-tight md:text-5xl">{{ $section->title }}</h2>
                    <p class="mt-5 max-w-md text-[14px] leading-7 text-stone-600 {{ $bodyFont }} {{ $bodySize }}" style="{{ $bodyColor }}">{{ $section->subtitle }}</p>
                    <div class="mt-6 flex flex-wrap gap-2">
                        @foreach($contentData['tags'] ?? [] as $tag)
                            @if(trim(is_array($tag) ? ($tag['title'] ?? '') : $tag))
                            <span class="rounded-full border border-stone-200 px-3 py-1 text-[11px]">{{ is_array($tag) ? $tag['title'] ?? '' : $tag }}</span>
                            @endif
                        @endforeach
                    </div>
                </div>
            </section>

        @elseif($section->section_type === 'hiw_physical_pack')
            <section class="bg-[var(--cream)]/70 {{ $bgClass }}" style="{{ $styleAttr }}">
                <div class="section grid items-center gap-10 lg:grid-cols-2 lg:gap-16">
                    <div class="overflow-hidden rounded-xl bg-white border border-stone-200">
                        <img src="{{ $imageUrl ?: 'https://guided-gut-reset-lovable-app.lovable.app/assets/course-kit-DtVPc2tT.jpg' }}" alt="Physical pack" class="aspect-[4/3] h-full w-full object-cover">
                    </div>
                    <div>
                        <h2 class="display-serif text-4xl leading-tight md:text-5xl">{{ $section->title }}</h2>
                        <ul class="mt-6 space-y-2 text-[13px] leading-6 text-stone-600">
                            @foreach($contentData['cards'] ?? [] as $item)
                                <li>· {{ is_array($item) ? $item['title'] ?? '' : $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </section>

        @elseif($section->section_type === 'hiw_help')
            <section class="section {{ $bgClass }}" style="{{ $styleAttr }}">
                <div class="grid gap-10 lg:grid-cols-[1fr_1.5fr] lg:items-center">
                    <div>
                        <h2 class="display-serif text-4xl leading-tight md:text-5xl">{{ $section->title }}</h2>
                        <a href="{{ $contentData['button_url'] ?? url('/#faq') }}" class="btn-secondary mt-6">{{ $contentData['button'] ?? 'Contact Us' }}</a>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach($contentData['cards'] ?? [] as $card)
                            <article class="rounded-lg border border-stone-200 bg-white p-5 shadow-[0_4px_6px_-3px_rgba(0,0,0,0.03)]">
                                <h3 class="display-serif text-xl">{{ $card['title'] ?? '' }}</h3>
                                <p class="mt-2 text-[12px] leading-6 text-stone-600">{{ $card['text'] ?? '' }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

        @elseif($section->section_type === 'hiw_ready')
            <section class="border-t border-stone-200 bg-[var(--cream)]/45 {{ $bgClass }}" style="{{ $styleAttr }}">
                <div class="section grid gap-10 md:grid-cols-2">
                    <div>
                        <div class="badge">{{ $contentData['col1_badge'] ?? 'Ready if relevant' }}</div>
                        <ul class="mt-4 space-y-1.5 text-[13px] leading-6 text-stone-600">
                            @foreach($contentData['col1_items'] ?? [] as $item) <li>· {{ $item }}</li> @endforeach
                        </ul>
                    </div>
                    <div>
                        <div class="badge">{{ $contentData['col2_badge'] ?? 'Speak to a doctor first' }}</div>
                        <ul class="mt-4 space-y-1.5 text-[13px] leading-6 text-stone-600">
                            @foreach($contentData['col2_items'] ?? [] as $item) <li>· {{ $item }}</li> @endforeach
                        </ul>
                    </div>
                </div>
            </section>

        @elseif($section->section_type === 'hiw_cta')
            <section class="bg-[var(--ink)] text-center text-white {{ $bgClass }}" style="--btn-primary-color: {{ !empty($settings['button_primary_color']) ? $settings['button_primary_color'] : '#ffffff' }}; --btn-primary-text-color: {{ !empty($settings['button_primary_text_color']) ? $settings['button_primary_text_color'] : '#0b0f14' }}; --btn-secondary-color: {{ !empty($settings['button_secondary_color']) ? $settings['button_secondary_color'] : 'transparent' }}; --btn-secondary-text-color: {{ !empty($settings['button_secondary_text_color']) ? $settings['button_secondary_text_color'] : '#ffffff' }}; {{ $styleAttr }}">
                <div class="section py-16">
                    <h2 class="display-serif text-4xl leading-tight md:text-5xl">{{ $section->title }}</h2>
                    <div class="mt-7 flex flex-wrap justify-center gap-3">
                        <a href="{{ $contentData['button_1_url'] ?? route('shop') }}" class="btn-primary">{{ $contentData['button_1'] ?? 'Shop' }}</a>
                        <a href="{{ $contentData['button_2_url'] ?? url('/#fit') }}" class="btn-secondary hover:">{{ $contentData['button_2'] ?? 'Check Fit' }}</a>
                    </div>
                </div>
            </section>
        @endif
    @endforeach
@endif
@endsection
