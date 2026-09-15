@extends('layouts.app', ['title' => 'Learn - Guided Wellness'])

@section('content')
@php
    $page = \App\Models\CmsPage::where('slug', 'learn')->with('sections.media')->first();
    if (!function_exists('resolveImg')) {
        function resolveImg($path, $fallback) {
            $path = $path ?: $fallback;
            if (str_starts_with($path, 'assets/')) return 'https://guided-gut-reset-lovable-app.lovable.app/' . $path;
            return asset($path);
        }
    }
@endphp
@if(isset($page) && $page->sections && $page->sections->count() > 0)
    @foreach($page->sections->sortBy('sort_order') as $section)
        @php
            $contentData = is_string($section->content) ? json_decode($section->content, true) : ($section->content ?? []);
            if (!is_array($contentData)) $contentData = [];
            $settings = $contentData['settings'] ?? [];
            if (!empty($settings['hidden_desktop']) && !empty($settings['hidden_mobile'])) continue;
            
            $topStyle = isset($settings['top_spacing']) && $settings['top_spacing'] ? "padding-top: {$settings['top_spacing']};" : "";
            $bottomStyle = isset($settings['bottom_spacing']) && $settings['bottom_spacing'] ? "padding-bottom: {$settings['bottom_spacing']};" : "";
            $bgColor = $settings['bg_color'] ?? '';
            $bgClass = !str_starts_with($bgColor, '#') ? $bgColor : '';
            $bgStyle = str_starts_with($bgColor, '#') ? "background-color: $bgColor;" : '';
            
            $btnPrimaryVar = !empty($settings['button_primary_color']) ? "--btn-primary-color: {$settings['button_primary_color']};" : "";
            $btnPrimaryTextVar = !empty($settings['button_primary_text_color']) ? "--btn-primary-text-color: {$settings['button_primary_text_color']};" : "";
            $btnSecondaryVar = !empty($settings['button_secondary_color']) ? "--btn-secondary-color: {$settings['button_secondary_color']};" : "";
            $btnSecondaryTextVar = !empty($settings['button_secondary_text_color']) ? "--btn-secondary-text-color: {$settings['button_secondary_text_color']};" : "";
            
            $styleAttr = trim("$topStyle $bottomStyle $bgStyle $btnPrimaryVar $btnPrimaryTextVar $btnSecondaryVar $btnSecondaryTextVar");
            
            $titleFont = $settings['title_font_family'] ?? 'font-serif';
            $titleSize = $settings['title_font_size'] ?? 'text-3xl md:text-4xl';
            $titleColor = !empty($settings['title_font_color']) ? "color: {$settings['title_font_color']};" : '';
            
            $bodyFont = $settings['body_font_family'] ?? '';
            $bodySize = $settings['body_font_size'] ?? 'text-[15px]';
            $bodyColor = !empty($settings['body_font_color']) ? "color: {$settings['body_font_color']};" : '';
        @endphp

        @if($section->section_type === 'learn_hero')
            <section class="border-b border-border/60 {{ $bgClass }}" style="{{ $styleAttr }}">
                <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8 grid gap-10 py-20 md:py-32 lg:grid-cols-[1.05fr_1fr] lg:gap-14">
                    <div>
                        <div class="text-[11px] font-medium uppercase tracking-[0.18em] text-muted-foreground">Learn</div>
                        <h1 class="mt-4 font-serif text-5xl leading-[1.05] md:text-6xl {{ $titleFont }} {{ $titleSize }} {{ $titleFont }} {{ $titleSize }}" style="{{ $titleColor }}" style="{{ $titleColor }}">{{ $section->title }}</h1>
                        <p class="mt-6 max-w-md text-[15px] leading-relaxed text-foreground/70 {{ $bodyFont }} {{ $bodySize }} {{ $bodyFont }} {{ $bodySize }}"15 style="{{ $bodyColor }}">{{ $section->subtitle }}</p>
                        <div class="mt-8 flex gap-3">
                            <a href="{{ $contentData['button_1_url'] ?? '#' }}" class="btn-primary">{{ $contentData['button_1'] ?? '' }}</a>
                            <a href="{{ $contentData['button_2_url'] ?? '#' }}" class="btn-secondary">{{ $contentData['button_2'] ?? '' }}</a>
                        </div>
                    </div>
                    <div class="overflow-hidden rounded-2xl bg-cream min-h-[400px]">
                        @if($section->media)
                            <img src="{{ $section->media->storage_path ? asset('storage/'.$section->media->storage_path) : $section->media->url }}" alt="{{ $section->title }}" class="h-full w-full object-cover"/>
                        @else
                            <img src="{{ resolveImg($contentData['image'] ?? '', 'assets/hero-product-CftTmpl1.jpg') }}" alt="Learn" class="h-full w-full object-cover"/>
                        @endif
                    </div>
                </div>
            </section>
        
        @elseif($section->section_type === 'learn_start_understanding')
            <section class="py-24 lg:py-32 {{ $bgClass }}" style="{{ $styleAttr }}">
                <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8">
                    <h2 class="text-center font-serif text-3xl leading-tight md:text-4xl {{ $titleFont }} {{ $titleSize }} {{ $titleFont }} {{ $titleSize }}" style="{{ $titleColor }}" style="{{ $titleColor }}">{{ $section->title }}</h2>
                    <div class="mt-12 grid gap-6 md:grid-cols-3">
                        @foreach($contentData['cards'] ?? [] as $card)
                            <a class="block rounded-2xl border border-border/60 bg-card p-8 transition hover:bg-cream/60" href="{{ $card['url'] ?? '#' }}">
                                <div class="font-serif text-lg uppercase tracking-[0.14em]">{{ $card['title'] ?? '' }}</div>
                                <p class="mt-4 text-[14px] leading-relaxed text-foreground/60 {{ $bodyFont }} {{ $bodySize }}"14 style="{{ $bodyColor }}">{{ $card['text'] ?? '' }}</p>
                                <div class="mt-6 text-[12px] uppercase tracking-[0.14em] text-primary font-medium">{{ $card['btn'] ?? 'Read the guide →' }}</div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        
        @elseif($section->section_type === 'learn_product_trials')
            <section class="bg-cream/60 py-24 lg:py-32 {{ $bgClass }}" style="{{ $styleAttr }}">
                <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8 grid items-center gap-14 lg:grid-cols-[1fr_1fr] lg:gap-20">
                    <div class="overflow-hidden rounded-2xl bg-background min-h-[400px]">
                        @if($section->media)
                            <img src="{{ $section->media->storage_path ? asset('storage/'.$section->media->storage_path) : $section->media->url }}" alt="{{ $section->title }}" class="h-full w-full object-cover"/>
                        @else
                            <img src="{{ resolveImg($contentData['image'] ?? '', 'assets/bottle-capsules-COsDFlLa.jpg') }}" alt="Trial" class="h-full w-full object-cover"/>
                        @endif
                    </div>
                    <div>
                        <div class="text-[11px] font-medium uppercase tracking-[0.18em] text-muted-foreground">{{ $contentData['badge'] ?? 'Product Trials' }}</div>
                        <h3 class="mt-4 font-serif text-3xl leading-tight md:text-4xl {{ $titleFont }} {{ $titleSize }} {{ $titleFont }} {{ $titleSize }}" style="{{ $titleColor }}" style="{{ $titleColor }}">{{ $section->title }}</h3>
                        <p class="mt-6 max-w-md text-[15px] leading-relaxed text-foreground/70 {{ $bodyFont }} {{ $bodySize }} {{ $bodyFont }} {{ $bodySize }}"15 style="{{ $bodyColor }}">{{ $section->subtitle }}</p>
                        <div class="mt-8">
                            <a class="btn-primary" href="{{ $contentData['button_1_url'] ?? '#' }}">{{ $contentData['button_1'] ?? 'Read the guide →' }}</a>
                        </div>
                    </div>
                </div>
            </section>
        
        @elseif($section->section_type === 'learn_guides_grid')
            <section class="py-24 lg:py-32 {{ $bgClass }}" style="{{ $styleAttr }}">
                <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8">
                    @if($section->title)
                    <h2 class="text-center font-serif text-3xl leading-tight md:text-4xl mb-12 {{ $titleFont }} {{ $titleSize }} {{ $titleFont }} {{ $titleSize }}" style="{{ $titleColor }}" style="{{ $titleColor }}">{{ $section->title }}</h2>
                    @endif
                    <div class="grid gap-8 md:grid-cols-3">
                        @foreach($contentData['cards'] ?? [] as $card)
                            <a class="group block" href="{{ $card['url'] ?? '#' }}">
                                <div class="overflow-hidden rounded-2xl bg-cream">
                                    <img src="{{ resolveImg($card['image'] ?? '', 'assets/hero-product-CftTmpl1.jpg') }}" alt="" class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-[1.03]"/>
                                </div>
                                <div class="mt-5">
                                    <div class="text-[11px] font-medium uppercase tracking-[0.14em] text-primary">{{ $card['tag'] ?? '' }}</div>
                                    <div class="mt-3 font-serif text-xl leading-snug">{{ $card['title'] ?? '' }}</div>
                                    <div class="mt-3 text-[12px] font-medium uppercase tracking-[0.14em] text-foreground/50 group-hover:text-primary transition-colors">{{ $card['btn'] ?? 'Read the guide →' }}</div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        
        @elseif($section->section_type === 'learn_language_clarity')
            <section class="bg-primary text-primary-foreground py-24 lg:py-32 {{ $bgClass }}" style="{{ $styleAttr }}">
                <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8 grid gap-12 lg:grid-cols-[1fr_1.5fr] lg:gap-20">
                    <div>
                        <h2 class="font-serif text-3xl leading-tight md:text-4xl {{ $titleFont }} {{ $titleSize }} {{ $titleFont }} {{ $titleSize }}" style="{{ $titleColor }}" style="{{ $titleColor }}">{{ $section->title }}</h2>
                        <p class="mt-6 max-w-sm text-[15px] leading-relaxed text-primary-foreground/80 {{ $bodyFont }} {{ $bodySize }} {{ $bodyFont }} {{ $bodySize }}"15 style="{{ $bodyColor }}">{{ $section->subtitle }}</p>
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2">
                        @foreach($contentData['cards'] ?? [] as $card)
                            <div class="rounded-xl border border-primary-foreground/15 bg-primary-foreground/[0.05] p-6">
                                <div class="text-[11px] font-medium uppercase tracking-[0.18em] text-muted-foreground"><span class="text-primary-foreground/70">{{ $card['badge'] ?? '' }}</span></div>
                                <p class="mt-3 text-[14px] leading-relaxed text-primary-foreground/85 {{ $bodyFont }} {{ $bodySize }}"14 style="{{ $bodyColor }}">{{ $card['text'] ?? '' }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div class="lg:col-span-2 mt-4">
                        <a href="{{ $contentData['button_1_url'] ?? '#' }}" class="btn-secondary !border-primary-foreground/30 !text-primary-foreground hover:!bg-primary-foreground/10">{{ $contentData['button_1'] ?? 'Review Our Standards' }}</a>
                    </div>
                </div>
            </section>
        
        @elseif($section->section_type === 'learn_cta')
            <section class="bg-cream/60 py-24 lg:py-32 text-center {{ $bgClass }}" style="{{ $styleAttr }}">
                <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8">
                    <div class="text-[11px] font-medium uppercase tracking-[0.18em] text-muted-foreground">{{ $contentData['badge'] ?? 'Ready when you are' }}</div>
                    <h2 class="mx-auto mt-5 max-w-2xl font-serif text-3xl leading-tight md:text-4xl {{ $titleFont }} {{ $titleSize }} {{ $titleFont }} {{ $titleSize }}" style="{{ $titleColor }}" style="{{ $titleColor }}">{{ $section->title }}</h2>
                    <p class="mx-auto mt-6 max-w-md text-[15px] leading-relaxed text-foreground/70 {{ $bodyFont }} {{ $bodySize }} {{ $bodyFont }} {{ $bodySize }}"15 style="{{ $bodyColor }}">{{ $section->subtitle }}</p>
                    <div class="mt-8 flex flex-wrap justify-center gap-3">
                        <a href="{{ $contentData['button_1_url'] ?? '#' }}" class="btn-primary">{{ $contentData['button_1'] ?? '' }}</a>
                        <a href="{{ $contentData['button_2_url'] ?? '#' }}" class="btn-secondary">{{ $contentData['button_2'] ?? '' }}</a>
                    </div>
                </div>
            </section>
        
        @elseif($section->section_type === 'learn_footer_note')
            <section class="border-t border-border/60 py-10 {{ $bgClass }}" style="{{ $styleAttr }}">
                <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8 flex flex-col items-center justify-between gap-4 text-[13px] text-foreground/60 sm:flex-row">
                    <div>{{ $contentData['text'] ?? 'Some symptoms should not wait for a product trial.' }}</div>
                    <div class="flex flex-wrap items-center gap-6">
                        @foreach($contentData['links'] ?? [] as $link)
                            <a href="{{ $link['url'] ?? '#' }}" class="underline hover:text-foreground transition-colors">{{ $link['title'] ?? '' }}</a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
        
    @endforeach
@endif
@endsection
