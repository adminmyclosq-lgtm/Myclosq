@extends('layouts.app', ['title' => 'Our Standards - Guided Wellness'])

@section('content')
@php
    $page = \App\Models\CmsPage::where('slug', 'our-standards')->with('sections.media')->first();
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

        @if($section->section_type === 'standards_hero')
            <section class="border-b border-border/60 {{ $bgClass }}" style="{{ $styleAttr }}">
                <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8 grid gap-10 py-20 md:py-32 lg:grid-cols-[1fr_1.4fr] lg:gap-14">
                    <div>
                        <div class="text-[11px] font-medium uppercase tracking-[0.18em] text-muted-foreground">Our Standards</div>
                        <h1 class="mt-4 font-serif text-5xl leading-[1.05] md:text-6xl {{ $titleFont }} {{ $titleSize }}" style="{{ $titleColor }}">{{ $section->title }}</h1>
                        <p class="mt-6 max-w-md text-[14px] leading-relaxed text-foreground/70 {{ $bodyFont }} {{ $bodySize }}"14 style="{{ $bodyColor }}">{{ $section->subtitle }}</p>
                        <div class="mt-8 flex gap-3">
                            <a href="{{ $contentData['button_1_url'] ?? '#' }}" class="btn-primary">{{ $contentData['button_1'] ?? '' }}</a>
                            <a href="{{ $contentData['button_2_url'] ?? '#' }}" class="btn-secondary">{{ $contentData['button_2'] ?? '' }}</a>
                        </div>
                    </div>
                    <div class="grid gap-4 mt-8 lg:mt-0">
                        @foreach($contentData['cards'] ?? [] as $card)
                            <div class="rounded-xl border border-border/60 bg-card p-8">
                                <div class="text-[13px] font-semibold text-foreground/60">{{ $card['num'] ?? '00' }}</div>
                                <div class="mt-2 font-serif text-2xl">{{ $card['title'] ?? '' }}</div>
                                <div class="mt-2 text-[14px] text-foreground/60">{{ $card['text'] ?? '' }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        
        @elseif($section->section_type === 'standards_formulation')
            <section class="py-24 lg:py-32 {{ $bgClass }}" style="{{ $styleAttr }}">
                <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8">
                    <h2 class="font-serif text-3xl leading-tight md:text-4xl text-center {{ $titleFont }} {{ $titleSize }}" style="{{ $titleColor }}">{{ $section->title }}</h2>
                    <p class="mt-3 text-[14px] text-muted-foreground text-center {{ $bodyFont }} {{ $bodySize }}"14 style="{{ $bodyColor }}">{{ $section->subtitle }}</p>
                    
                    <div class="mt-10 overflow-hidden rounded-lg border border-border/60">
                        <div class="bg-primary px-6 py-4 text-[12px] uppercase tracking-[0.14em] text-primary-foreground grid grid-cols-5 gap-4">
                            <div>Ingredient</div><div>Declared Quantity</div><div>Intended Role</div><div>Source / Form</div><div>Supporting Information</div>
                        </div>
                        <div class="bg-cream/50 px-6 py-12 text-center text-[14px] text-foreground/60">
                            No data available. Select a product to view formulation details.
                        </div>
                    </div>
                    <p class="mt-4 text-[13px] text-foreground/60 text-center">Select an ingredient to review its intended role, selection rationale, supporting information and evidence limitations.</p>
                </div>
            </section>
        
        @elseif($section->section_type === 'standards_quality')
            <section class="bg-primary text-primary-foreground py-24 lg:py-32 {{ $bgClass }}" style="{{ $styleAttr }}">
                <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8 grid gap-10 lg:grid-cols-[1.2fr_1.4fr] lg:gap-16 items-center">
                    <div>
                        <h2 class="font-serif text-3xl leading-tight md:text-4xl {{ $titleFont }} {{ $titleSize }}" style="{{ $titleColor }}">{{ $section->title }}</h2>
                        <p class="mt-6 max-w-md text-[15px] leading-relaxed text-primary-foreground/80 {{ $bodyFont }} {{ $bodySize }}"15 style="{{ $bodyColor }}">{{ $section->subtitle }}</p>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach($contentData['cards'] ?? [] as $card)
                            <div class="rounded-xl border border-primary-foreground/15 bg-primary-foreground/[0.05] p-6">
                                <div class="font-serif text-lg">{{ $card['title'] ?? '' }}</div>
                                <div class="mt-4 space-y-2 text-[13px] text-primary-foreground/80">
                                    <div>What's checked</div><div>Why it matters</div><div>Standard</div><div>Document</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        
        @elseif($section->section_type === 'standards_observation')
            <section class="py-24 lg:py-32 {{ $bgClass }}" style="{{ $styleAttr }}">
                <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8 mx-auto max-w-3xl text-center">
                    <h2 class="font-serif text-3xl leading-tight md:text-4xl text-center {{ $titleFont }} {{ $titleSize }}" style="{{ $titleColor }}">{{ $section->title }}</h2>
                    <p class="mx-auto mt-6 text-[15px] max-w-2xl leading-relaxed text-foreground/70 {{ $bodyFont }} {{ $bodySize }}"15 style="{{ $bodyColor }}">{{ $section->subtitle }}</p>
                    
                    <div class="mt-10 flex flex-wrap justify-center gap-3">
                        @foreach($contentData['tags'] ?? [] as $tag)
                            <span class="rounded-full border border-border/70 bg-cream px-4 py-2 text-[12px] uppercase">{{ is_array($tag) ? $tag['title'] ?? '' : $tag }}</span>
                        @endforeach
                    </div>
                    
                    <div class="mx-auto mt-14 grid gap-6 md:grid-cols-2 text-left">
                        <div class="rounded-xl border border-border/60 bg-card p-8">
                            <div class="text-[12px] font-medium uppercase tracking-[0.18em] text-muted-foreground">{{ $contentData['col1_title'] ?? '' }}</div>
                            <ul class="mt-5 space-y-3 text-[14px] text-foreground/70 leading-relaxed">
                                @foreach($contentData['col1_items'] ?? [] as $item) <li>· {{ $item }}</li> @endforeach
                            </ul>
                        </div>
                        <div class="rounded-xl border border-destructive/30 bg-destructive/[0.04] p-8">
                            <div class="text-[12px] font-medium uppercase tracking-[0.18em] text-muted-foreground">{{ $contentData['col2_title'] ?? '' }}</div>
                            <ul class="mt-5 space-y-3 text-[14px] text-foreground/70 leading-relaxed">
                                @foreach($contentData['col2_items'] ?? [] as $item) <li>· {{ $item }}</li> @endforeach
                            </ul>
                        </div>
                    </div>
                    <p class="mt-10 text-[12px] max-w-2xl mx-auto uppercase tracking-wide text-foreground/50">DISCLAIMER: The information above is educational. Always speak with a qualified healthcare professional regarding specific health concerns.</p>
                </div>
            </section>
        
        @elseif($section->section_type === 'standards_safety')
            <section class="bg-cream/60 py-24 lg:py-32 {{ $bgClass }}" style="{{ $styleAttr }}">
                <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8">
                    <h2 class="text-center font-serif text-3xl leading-tight md:text-4xl {{ $titleFont }} {{ $titleSize }}" style="{{ $titleColor }}">{{ $section->title }}</h2>
                    <p class="mx-auto mt-4 max-w-xl text-center text-[15px] leading-relaxed text-foreground/70 {{ $bodyFont }} {{ $bodySize }}"15 style="{{ $bodyColor }}">{{ $section->subtitle }}</p>
                    
                    <div class="mx-auto mt-12 grid max-w-4xl gap-6 md:grid-cols-2">
                        <div class="rounded-xl border border-border/60 bg-background p-8">
                            <div class="text-[12px] font-medium uppercase tracking-[0.18em] text-muted-foreground">{{ $contentData['col1_title'] ?? '' }}</div>
                            <ul class="mt-5 space-y-3 text-[14px] text-foreground/75 leading-relaxed">
                                @foreach($contentData['col1_items'] ?? [] as $item) <li>· {{ $item }}</li> @endforeach
                            </ul>
                        </div>
                        <div class="rounded-xl border border-destructive/30 bg-destructive/[0.04] p-8">
                            <div class="text-[12px] font-medium uppercase tracking-[0.18em] text-muted-foreground">{{ $contentData['col2_title'] ?? '' }}</div>
                            <ul class="mt-5 space-y-3 text-[14px] text-foreground/75 leading-relaxed">
                                @foreach($contentData['col2_items'] ?? [] as $item) <li>· {{ $item }}</li> @endforeach
                            </ul>
                        </div>
                    </div>
                    
                    <div class="mt-12 text-center">
                        <div class="flex flex-wrap justify-center gap-6 text-[12px] uppercase tracking-[0.14em] text-foreground/60">
                            <span>Usage Instructions</span><span>Full Warnings</span><span>Storage Guidance</span>
                        </div>
                        <div class="mt-10">
                            <a href="{{ $contentData['button_url'] ?? '#' }}" class="btn-primary">{{ $contentData['button'] ?? '' }}</a>
                        </div>
                    </div>
                </div>
            </section>
        
        @elseif($section->section_type === 'standards_review')
            <section class="py-24 lg:py-32 {{ $bgClass }}" style="{{ $styleAttr }}">
                <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8 mx-auto max-w-3xl text-center">
                    <h2 class="font-serif text-3xl leading-tight md:text-4xl {{ $titleFont }} {{ $titleSize }}" style="{{ $titleColor }}">{{ $section->title }}</h2>
                    <div class="mt-8 text-[15px] text-foreground/60 bg-cream/30 py-4 px-6 inline-block rounded-xl">{{ $section->subtitle }}</div>
                </div>
            </section>
        
        @elseif($section->section_type === 'standards_cta')
            <section class="bg-primary text-primary-foreground {{ $bgClass }}" style="--btn-primary-color: {{ !empty($settings['button_primary_color']) ? $settings['button_primary_color'] : '#ffffff' }}; --btn-primary-text-color: {{ !empty($settings['button_primary_text_color']) ? $settings['button_primary_text_color'] : '#0b0f14' }}; --btn-secondary-color: {{ !empty($settings['button_secondary_color']) ? $settings['button_secondary_color'] : 'transparent' }}; --btn-secondary-text-color: {{ !empty($settings['button_secondary_text_color']) ? $settings['button_secondary_text_color'] : '#ffffff' }}; {{ $styleAttr }}">
                <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8 py-24 lg:py-32 text-center">
                    <h2 class="font-serif text-3xl md:text-4xl {{ $titleFont }} {{ $titleSize }}" style="{{ $titleColor }}">{{ $section->title }}</h2>
                    <div class="mt-8 flex justify-center gap-4">
                        <a href="{{ $contentData['button_1_url'] ?? '#' }}" class="btn-primary hover:">{{ $contentData['button_1'] ?? '' }}</a>
                        <a href="{{ $contentData['button_2_url'] ?? '#' }}" class="btn-secondary hover:">{{ $contentData['button_2'] ?? '' }}</a>
                    </div>
                </div>
            </section>
        @endif
        
    @endforeach
@endif
@endsection
