@props(['section' => null])
@php
    $isMyClosq = !empty($isMyClosq) || in_array(request()->getHost(), ['myclosq.com', 'www.myclosq.com']);
    $contentData = $section ? json_decode($section->content, true) : [];
    $settings = $contentData['settings'] ?? [];
    $topSpace = $settings['top_spacing'] ?? '80px';
    $bottomSpace = $settings['bottom_spacing'] ?? '80px';
    
    // Outer Wrapper background color from global settings
    $outerBgColor = !empty($settings['bg_color']) ? $settings['bg_color'] : 'transparent';
    $outerBgClass = !str_starts_with($outerBgColor, '#') && $outerBgColor !== 'transparent' ? $outerBgColor : '';
    $outerBgStyle = str_starts_with($outerBgColor, '#') ? "background-color: $outerBgColor;" : '';

    // Inner Card background color
    $innerBgColor = !empty($settings['inner_bg_color']) ? $settings['inner_bg_color'] : 'bg-[linear-gradient(135deg,#18352c_0%,#284a3e_55%,#3c5c4e_100%)]';
    // If they cleared it, default to gradient
    if(empty($settings['inner_bg_color'])) $innerBgColor = 'bg-[linear-gradient(135deg,#18352c_0%,#284a3e_55%,#3c5c4e_100%)]';
    $innerBgClass = !str_starts_with($innerBgColor, '#') ? $innerBgColor : '';
    $innerBgStyle = str_starts_with($innerBgColor, '#') ? "background-color: $innerBgColor;" : '';

    // Eyebrow background
    $eyebrowBg = !empty($settings['eyebrow_bg_color']) ? $settings['eyebrow_bg_color'] : 'rgba(255,255,255,0.1)';

    $btnPriBg = !empty($settings['button_primary_color']) ? $settings['button_primary_color'] : '#ffffff';
    $btnPriText = !empty($settings['button_primary_text_color']) ? $settings['button_primary_text_color'] : '#0b0f14';
    $btnSecBg = !empty($settings['button_secondary_color']) ? $settings['button_secondary_color'] : 'transparent';
    $btnSecText = !empty($settings['button_secondary_text_color']) ? $settings['button_secondary_text_color'] : '#ffffff';

    $styleAttr = trim("padding-top: {$topSpace}; padding-bottom: {$bottomSpace}; $outerBgStyle --btn-primary-color: {$btnPriBg}; --btn-primary-text-color: {$btnPriText}; --btn-secondary-color: {$btnSecBg}; --btn-secondary-text-color: {$btnSecText};");
@endphp
<section class="section {{ $outerBgClass }}" style="{{ $styleAttr }}" id="section-{{ $section->id ?? 'new' }}">
    <div class="rounded-[2rem] {{ $innerBgClass }} px-6 py-12 text-white md:px-12 md:py-16" style="{{ $innerBgStyle }}">
        <div class="grid gap-8 md:grid-cols-[1.25fr_.75fr] md:items-end">
            <div>
                <span class="badge text-white border-0" style="background-color: {{ $eyebrowBg }}">{{ $contentData['eyebrow'] ?? 'Start the course' }}</span>
                <h2 class="mt-4 {{ $settings['title_font_family'] ?? 'display-serif' }} {{ $settings['title_font_size'] ?? 'text-5xl leading-tight' }}" @if(!empty($settings['title_font_color'])) style="color: {{ $settings['title_font_color'] }}" @endif>{{ $section->title ?? 'Give the capsule a fair 30-day trial.' }}</h2>
                <p class="mt-4 max-w-2xl text-white/80 {{ $settings['body_font_family'] ?? '' }} {{ $settings['body_font_size'] ?? 'text-[15px]' }}" @if(!empty($settings['body_font_color'])) style="color: {{ $settings['body_font_color'] }}" @endif>{{ $section->subtitle ?? 'Start the course and receive your Gut Response Brief at Day 30.' }}</p>
            </div>
            <div class="flex flex-wrap gap-3 md:justify-end">
                <a class="btn-primary" href="{{ $contentData['primary_button_url'] ?? route('shop') }}">{{ ($isMyClosq && ($contentData['primary_button_text'] ?? 'Shop Gut Reset') === 'Shop Gut Reset') ? 'Shop My CLOSQ' : ($contentData['primary_button_text'] ?? ($isMyClosq ? 'Shop My CLOSQ' : 'Shop Gut Reset')) }}</a>
                <a class="btn-secondary" href="{{ $contentData['secondary_button_url'] ?? '#fit' }}">{{ $contentData['secondary_button_text'] ?? 'Check Your Fit' }}</a>
            </div>
        </div>
    </div>
</section>
