@props(['section'])
@php
    $content = is_string($section->content) ? json_decode($section->content, true) : ($section->content ?? []);
    if (!is_array($content)) $content = [];
    $settings = $content['settings'] ?? [];
    
    $isMyClosq = !empty($isMyClosq) || in_array(request()->getHost(), ['myclosq.com', 'www.myclosq.com']);
    $heroBadge = $content['badge'] ?? ($isMyClosq ? '30-Day Guided My CLOSQ' : '30-Day Guided Gut Reset');
    if ($isMyClosq && $heroBadge === '30-Day Guided Gut Reset') {
        $heroBadge = '30-Day Guided My CLOSQ';
    }
    $button1 = $content['button_1'] ?? ($isMyClosq ? 'Explore My CLOSQ' : 'Explore the Reset');
    $button2 = $content['button_2'] ?? 'Check Your Fit';
    
    // Spacing
    $topStyle = isset($settings['top_spacing']) && $settings['top_spacing'] ? "padding-top: {$settings['top_spacing']};" : "padding-top: 64px;";
    $bottomStyle = isset($settings['bottom_spacing']) && $settings['bottom_spacing'] ? "padding-bottom: {$settings['bottom_spacing']};" : "padding-bottom: 64px;";
    $bgColor = isset($settings['bg_color']) && $settings['bg_color'] ? $settings['bg_color'] : "bg-[var(--cream)]";
    
    if (!empty($settings['hidden'])) return;
@endphp
<section class="relative overflow-hidden {{ !str_starts_with($bgColor??'', '#') ? $bgColor : '' }}" style="{{ $topStyle }} {{ $bottomStyle }} {{ str_starts_with($bgColor??'', '#') ? 'background-color: '.$bgColor.';' : '' }} {{ !empty($settings['button_primary_color']) ? '--btn-primary-color: '.$settings['button_primary_color'].';' : '' }} {{ !empty($settings['button_secondary_color']) ? '--btn-secondary-color: '.$settings['button_secondary_color'].';' : '' }}">
    <div class="section !py-0 grid items-center gap-10 lg:grid-cols-[1.05fr_1fr] lg:gap-14">
        <div class="max-w-xl animate-[fadeIn_.7s_ease-out_both]">
            <span class="badge">{{ $heroBadge }}</span>
            <h1 class="mt-5 {{ $settings['title_font_family'] ?? 'display-serif' }} {{ $settings['title_font_size'] ?? 'text-5xl leading-[1.02] md:text-6xl lg:text-7xl' }}" @if(!empty($settings['title_font_color'])) style="color: {{ $settings['title_font_color'] }} {{ !empty($settings['button_primary_color']) ? '--btn-primary-color: '.$settings['button_primary_color'].';' : '' }} {{ !empty($settings['button_secondary_color']) ? '--btn-secondary-color: '.$settings['button_secondary_color'].';' : '' }}" @endif>{!! $section->title ?? 'Take it daily.<br>Check in lightly.<br>Know what changed.' !!}</h1>
            <p class="mt-6 max-w-md leading-7 text-stone-600 {{ $settings['body_font_family'] ?? '' }} {{ $settings['body_font_size'] ?? 'text-[15px]' }}" @if(!empty($settings['body_font_color'])) style="color: {{ $settings['body_font_color'] }} {{ !empty($settings['button_primary_color']) ? '--btn-primary-color: '.$settings['button_primary_color'].';' : '' }} {{ !empty($settings['button_secondary_color']) ? '--btn-secondary-color: '.$settings['button_secondary_color'].';' : '' }}" @endif>
                {{ $section->subtitle ?? 'A 30-day guided gut-support capsule course. Take one capsule daily, complete a few short course moments, and receive a personal Gut Response Brief showing what changed and what to do next.' }}
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a class="btn-primary" href="{{ !empty($isMyClosq) ? '#how-it-works' : route('shop') }}">{{ $button1 }}</a>
                <a class="btn-secondary" href="#fit">{{ $button2 }}</a>
            </div>
            <div class="mt-9 flex flex-wrap gap-x-6 gap-y-2 text-[11px] font-medium uppercase tracking-[0.14em] text-stone-500">
                <span>· 30 capsules</span>
                <span>· 15 minutes total</span>
                <span>· Gut Response Brief</span>
            </div>
        </div>
        <div class="animate-[fadeIn_.8s_ease-out_.1s_both] overflow-hidden rounded-xl bg-[var(--sand)] shadow-[0_20px_60px_-30px_rgba(35,55,40,.35)]">
            @if($section->media)
                <img src="{{ $section->media->storage_path ? asset('storage/'.$section->media->storage_path) : $section->media->url }}" alt="{{ strip_tags($section->title ?? '30-Day Gut Reset') }}" class="aspect-[4/3] h-full w-full object-cover lg:aspect-auto lg:min-h-[520px]">
            @else
                <img src="https://guided-gut-reset-lovable-app.lovable.app/assets/hero-product-CftTmpl1.jpg" alt="30-Day Gut Reset" class="aspect-[4/3] h-full w-full object-cover lg:aspect-auto lg:min-h-[520px]">
            @endif
        </div>
    </div>
</section>
