@extends('layouts.app', ['title' => 'Support - Guided Wellness'])

@section('content')
@php
    $page = \App\Models\CmsPage::where('slug', 'support')->with('sections.media')->first();
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

        @if($section->section_type === 'support_hero')
            <section class="py-16 md:py-20 text-center {{ $bgClass }}" style="{{ $styleAttr }}">
                <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8">
                    <div class="text-[11px] font-medium uppercase tracking-[0.18em] text-muted-foreground">Support</div>
                    <h1 class="mt-4 font-serif text-5xl leading-[1.05] md:text-6xl {{ $titleFont }} {{ $titleSize }}" style="{{ $titleColor }}">{{ $section->title }}</h1>
                    <p class="mx-auto mt-4 max-w-xl text-[15px] text-foreground/70 {{ $bodyFont }} {{ $bodySize }}"15 style="{{ $bodyColor }}">{{ $section->subtitle }}</p>
                    
                    <div class="mx-auto mt-10 grid max-w-4xl gap-4 md:grid-cols-4">
                        @foreach($contentData['cards'] ?? [] as $card)
                            <div class="rounded-xl border border-border/60 bg-card p-5 text-[14px] text-foreground/70 hover:bg-cream transition cursor-pointer">{{ is_array($card) ? $card['title'] ?? '' : $card }}</div>
                        @endforeach
                    </div>
                </div>
            </section>
        
        @elseif($section->section_type === 'support_purchase_origin')
            <section class="bg-cream/60 py-24 lg:py-32 {{ $bgClass }}" style="{{ $styleAttr }}">
                <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8">
                    <h2 class="text-center font-serif text-3xl leading-tight md:text-4xl {{ $titleFont }} {{ $titleSize }}" style="{{ $titleColor }}">{{ $section->title }}</h2>
                    <div class="mx-auto mt-12 grid max-w-4xl gap-6 md:grid-cols-2">
                        <div class="rounded-2xl bg-background p-8">
                            <div class="font-serif text-xl border-l-[3px] border-primary pl-4">{{ $contentData['col1_title'] ?? '' }}</div>
                            <p class="mt-4 text-[14px] text-foreground/60 pl-4 {{ $bodyFont }} {{ $bodySize }}"14 style="{{ $bodyColor }}">{{ $contentData['col1_text'] ?? '' }}</p>
                            <div class="mt-8 pl-4">
                                <a class="btn-primary" href="{{ $contentData['col1_btn_url'] ?? '#' }}">{{ $contentData['col1_btn'] ?? 'Get help with your order' }}</a>
                            </div>
                        </div>
                        <div class="rounded-2xl bg-background p-8">
                            <div class="font-serif text-xl border-l-[3px] border-border pl-4">{{ $contentData['col2_title'] ?? '' }}</div>
                            <p class="mt-4 text-[14px] text-foreground/60 pl-4 {{ $bodyFont }} {{ $bodySize }}"14 style="{{ $bodyColor }}">{{ $contentData['col2_text'] ?? '' }}</p>
                            <div class="mt-8 pl-4">
                                <a class="btn-secondary" href="{{ $contentData['col2_btn_url'] ?? '#' }}">{{ $contentData['col2_btn'] ?? 'Get Amazon Help' }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        
        @elseif($section->section_type === 'support_topics')
            <section class="py-24 lg:py-32 {{ $bgClass }}" style="{{ $styleAttr }}">
                <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8">
                    <h2 class="text-center font-serif text-3xl leading-tight md:text-4xl {{ $titleFont }} {{ $titleSize }}" style="{{ $titleColor }}">{{ $section->title }}</h2>
                    <div class="mx-auto mt-14 grid max-w-5xl gap-10 md:grid-cols-4">
                        @foreach($contentData['topics'] ?? [] as $topic)
                            <div>
                                <div class="text-[12px] font-medium uppercase tracking-[0.14em] text-primary">{{ $topic['title'] ?? '' }}</div>
                                <ul class="mt-5 space-y-3 text-[14px] text-foreground/70">
                                    @foreach($topic['items'] ?? [] as $item)
                                        <li>&middot; {{ is_array($item) ? $item['title'] ?? '' : $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        
        @elseif($section->section_type === 'support_course_start')
            <section class="bg-cream/60 py-24 lg:py-32 {{ $bgClass }}" style="{{ $styleAttr }}">
                <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8">
                    <h2 class="text-center font-serif text-3xl leading-tight md:text-4xl {{ $titleFont }} {{ $titleSize }}" style="{{ $titleColor }}">{{ $section->title }}</h2>
                    <div class="mx-auto mt-12 grid max-w-4xl gap-6 md:grid-cols-3">
                        @foreach($contentData['cards'] ?? [] as $index => $card)
                            <div class="rounded-xl bg-background p-8 text-center flex flex-col items-center">
                                <div class="grid h-10 w-10 place-items-center rounded-full bg-primary/10 text-primary font-semibold text-lg">{{ $index + 1 }}</div>
                                <div class="mt-5 font-serif text-lg">{{ $card['title'] ?? '' }}</div>
                                <div class="mt-3 text-[14px] text-foreground/60 leading-relaxed max-w-[200px]">{{ $card['text'] ?? '' }}</div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-12 flex justify-center gap-4">
                        <a href="{{ $contentData['button_1_url'] ?? '#' }}" class="btn-primary">{{ $contentData['button_1'] ?? 'Activate Course' }}</a>
                        <a href="{{ $contentData['button_2_url'] ?? '#' }}" class="btn-secondary">{{ $contentData['button_2'] ?? 'Contact Us' }}</a>
                    </div>
                </div>
            </section>
        
        @elseif($section->section_type === 'support_concerns')
            <section class="py-24 lg:py-32 {{ $bgClass }}" style="{{ $styleAttr }}">
                <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8">
                    <h2 class="text-center font-serif text-3xl leading-tight md:text-4xl {{ $titleFont }} {{ $titleSize }}" style="{{ $titleColor }}">{!! nl2br(e($section->title)) !!}</h2>
                    <div class="mx-auto mt-12 grid max-w-4xl gap-6 md:grid-cols-2">
                        <div class="rounded-2xl border border-border/60 bg-card p-10 flex flex-col justify-between">
                            <div>
                                <div class="text-[12px] font-medium uppercase tracking-[0.18em] text-muted-foreground">{{ $contentData['col1_badge'] ?? 'Report a product concern' }}</div>
                                <p class="mt-4 text-[15px] leading-relaxed text-foreground/70 {{ $bodyFont }} {{ $bodySize }}"15 style="{{ $bodyColor }}">{{ $contentData['col1_text'] ?? '' }}</p>
                            </div>
                            <div class="mt-8">
                                <a class="btn-primary" href="{{ $contentData['col1_btn_url'] ?? '#' }}">{{ $contentData['col1_btn'] ?? 'Report Now' }}</a>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-destructive/30 bg-destructive/[0.04] p-10 flex flex-col justify-between">
                            <div>
                                <div class="text-[12px] font-medium uppercase tracking-[0.18em] text-muted-foreground"><span class="text-destructive">{{ $contentData['col2_badge'] ?? 'Seek professional guidance first' }}</span></div>
                                <p class="mt-4 text-[15px] leading-relaxed text-foreground/70 {{ $bodyFont }} {{ $bodySize }}"15 style="{{ $bodyColor }}">{{ $contentData['col2_text'] ?? '' }}</p>
                            </div>
                            <div class="mt-8">
                                <a href="{{ $contentData['col2_btn_url'] ?? '/our-standards' }}" class="btn-secondary">{{ $contentData['col2_btn'] ?? 'Read Safety Standards' }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        
        @elseif($section->section_type === 'support_contact_methods')
            <section class="bg-cream/60 py-20 lg:py-24 {{ $bgClass }}" style="{{ $styleAttr }}">
                <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8">
                    <div class="mx-auto grid max-w-5xl gap-6 md:grid-cols-3">
                        @foreach($contentData['cards'] ?? [] as $index => $card)
                            <div class="rounded-2xl bg-background p-8 flex flex-col h-full">
                                <div class="font-serif text-xl border-l-[3px] {{ $index === 0 ? 'border-[#25d366]' : 'border-primary' }} pl-4">{{ $card['title'] ?? '' }}</div>
                                <div class="mt-4 text-[14px] text-foreground/60 pl-4 flex-grow">{{ $card['text'] ?? '' }}</div>
                                <div class="mt-8 pl-4">
                                    <a class="btn-secondary" href="{{ $card['url'] ?? '#' }}">{{ $card['btn'] ?? 'Open' }}</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        
        @elseif($section->section_type === 'support_details_ready')
            <section class="py-20 lg:py-24 {{ $bgClass }}" style="{{ $styleAttr }}">
                <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8">
                    <div class="mx-auto max-w-4xl rounded-2xl border border-primary/20 bg-primary/5 p-8 md:p-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-8">
                        <div>
                            <div class="font-serif text-2xl">{{ $section->title }}</div>
                            <div class="mt-2 text-[14px] text-foreground/70">{{ $section->subtitle }}</div>
                        </div>
                        <ul class="shrink-0 space-y-3 text-[14px] text-foreground/70 font-medium">
                            @foreach($contentData['items'] ?? [] as $item)
                                <li>&bull; {{ $item['label'] ?? '' }} <span class="font-normal opacity-70">{{ $item['hint'] ?? '' }}</span></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </section>
        
        @elseif($section->section_type === 'support_faq')
            <section class="py-24 lg:py-32 {{ $bgClass }}" style="{{ $styleAttr }}">
                <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8 mx-auto max-w-3xl">
                    <h2 class="text-center font-serif text-3xl leading-tight md:text-4xl {{ $titleFont }} {{ $titleSize }}" style="{{ $titleColor }}">{{ $section->title }}</h2>
                    <div class="mt-12 divide-y divide-border/70 border-t border-b border-border/70">
                        @foreach($contentData['items'] ?? [] as $item)
                        <details class="group">
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 py-5 text-left font-medium [&::-webkit-details-marker]:hidden">
                                <span class="text-[15px] text-foreground group-hover:text-primary transition-colors">{{ $item['q'] ?? '' }}</span>
                                <span class="text-foreground/40 transition-transform duration-300 group-open:rotate-45 text-2xl shrink-0">+</span>
                            </summary>
                            <div class="pb-6 pr-8 text-[14px] leading-relaxed text-foreground/70">{{ $item['a'] ?? '' }}</div>
                        </details>
                        @endforeach
                    </div>
                </div>
            </section>
        
        @elseif($section->section_type === 'support_auth_cta')
            <section class="bg-primary text-primary-foreground {{ $bgClass }}" style="{{ $styleAttr }}">
                <div class="mx-auto w-full max-w-[1200px] px-5 sm:px-8 py-24 text-center">
                    <h2 class="font-serif text-3xl md:text-4xl {{ $titleFont }} {{ $titleSize }}" style="{{ $titleColor }}">{{ $section->title }}</h2>
                    <p class="mx-auto mt-5 max-w-md text-[15px] text-primary-foreground/80 leading-relaxed {{ $bodyFont }} {{ $bodySize }}"15 style="{{ $bodyColor }}">{{ $section->subtitle }}</p>
                    <div class="mt-8">
                        <a href="{{ $contentData['button_url'] ?? '/login' }}" class="btn-primary hover:">{{ $contentData['button'] ?? 'Log in' }}</a>
                    </div>
                </div>
            </section>
        
        @endif
    @endforeach
@endif
@endsection
