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
    
    if (!empty($settings['hidden'])) return;
@endphp
<section class="relative overflow-hidden bg-background">
    <div class="section grid gap-10 lg:grid-cols-[1.05fr_1fr] lg:gap-14">
        <div class="max-w-xl animate-[fadeIn_.7s_ease-out_both]">
            <div class="badge">{{ $heroBadge }}</div>
            <h1 class="mt-5 font-serif text-[44px] leading-[1.05] text-foreground sm:text-[56px] lg:text-[68px]">{!! $section->title ?? 'Take it daily.<br/>Check in lightly.<br/>Know what changed.' !!}</h1>
            <p class="mt-6 max-w-md text-[15px] leading-relaxed text-foreground/70">{{ $section->subtitle ?? 'A 30-day guided gut-support capsule course. Take one capsule daily, complete a few short course moments, and receive a personal Gut Response Brief showing what changed and what to do next.' }}</p>
            <div class="mt-8 flex flex-wrap items-center gap-3">
                <a href="{{ route('shop') }}" class="btn-primary">{{ $button1 }}</a>
                <a href="#fit" class="btn-secondary">{{ $button2 }}</a>
            </div>
            <ul class="mt-10 flex flex-wrap gap-x-6 gap-y-2 text-[12px] uppercase tracking-[0.14em] text-foreground/60">
                <li>· 30 capsules</li>
                <li>· 15 minutes total</li>
                <li>· Gut Response Brief</li>
            </ul>
        </div>
        <div class="relative animate-[fadeIn_.8s_ease-out_.1s_both]">
            <div class="overflow-hidden rounded-2xl bg-cream shadow-[0_20px_60px_-30px_rgba(35,55,40,0.35)] h-full min-h-[400px]">
                @if($section->media)
                    <img src="{{ $section->media->storage_path ? asset('storage/'.$section->media->storage_path) : $section->media->url }}" alt="{{ $section->title }}" class="h-full w-full object-cover">
                @else
                    <div class="flex aspect-square md:aspect-auto w-full md:h-full min-h-[400px] items-center justify-center border-2 border-dashed border-stone-300 bg-[var(--panel)] text-stone-400 backdrop-blur text-sm">
                        Image pending uploading from CMS
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
