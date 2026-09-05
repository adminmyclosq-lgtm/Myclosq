@props(['section'])
@php
    $content = is_string($section->content) ? json_decode($section->content, true) : ($section->content ?? []);
    if (!is_array($content)) $content = [];
    $settings = $content['settings'] ?? [];
    
    $heroBadge = $content['badge'] ?? '30-Day Guided Gut Reset';
    $button1 = $content['button_1'] ?? 'Explore the Reset';
    $button2 = $content['button_2'] ?? 'Check Your Fit';
    
    // Spacing
    $topStyle = isset($settings['top_spacing']) && $settings['top_spacing'] ? "padding-top: {$settings['top_spacing']};" : "padding-top: 64px;";
    $bottomStyle = isset($settings['bottom_spacing']) && $settings['bottom_spacing'] ? "padding-bottom: {$settings['bottom_spacing']};" : "padding-bottom: 64px;";
    $bgColor = isset($settings['bg_color']) && $settings['bg_color'] ? $settings['bg_color'] : "bg-[var(--cream)]";
    
    if (!empty($settings['hidden'])) return;
@endphp
<section class="relative overflow-hidden {{ $bgColor }}" style="{{ $topStyle }} {{ $bottomStyle }}">
    <div class="section !py-0 grid items-center gap-10 lg:grid-cols-[1.05fr_1fr] lg:gap-14">
        <div class="max-w-xl animate-[fadeIn_.7s_ease-out_both]">
            <span class="badge">{{ $heroBadge }}</span>
            <h1 class="display-serif mt-5 text-5xl leading-[1.02] md:text-6xl lg:text-7xl">{!! $section->title ?? 'Take it daily.<br>Check in lightly.<br>Know what changed.' !!}</h1>
            <p class="mt-6 max-w-md text-[15px] leading-7 text-stone-600">
                {{ $section->subtitle ?? 'A 30-day guided gut-support capsule course. Take one capsule daily, complete a few short course moments, and receive a personal Gut Response Brief showing what changed and what to do next.' }}
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a class="btn-primary" href="{{ route('shop') }}">{{ $button1 }}</a>
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
