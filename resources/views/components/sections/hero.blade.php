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
<section class="relative overflow-hidden bg-[var(--cream)]" style="{{ $topStyle }} {{ $bottomStyle }}">
    <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top_left,_rgba(125,154,135,.18),_transparent_36%),radial-gradient(circle_at_top_right,_rgba(24,53,44,.08),_transparent_28%),linear-gradient(180deg,#faf7f0_0%,#f7f4ec_55%,#fff_100%)]"></div>
    <div class="section grid items-center gap-12 md:grid-cols-2">
        <div class="max-w-2xl animate-[fadeIn_.7s_ease-out_both]">
            <span class="badge">{{ $heroBadge }}</span>
            <h1 class="display-serif mt-5 text-5xl leading-[.95] tracking-tight md:text-7xl">{!! $section->title ?? 'Take it daily.<br>Check in lightly.<br>Know what changed.' !!}</h1>
            <p class="mt-6 max-w-xl text-lg leading-8 text-stone-600">
                {{ $section->subtitle ?? 'A 30-day guided gut-support capsule course. Take one capsule daily, complete a few short course moments, and receive a personal Gut Response Brief showing what changed and what to do next.' }}
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a class="btn-primary" href="{{ route('shop') }}">{{ $button1 }}</a>
                <a class="btn-secondary" href="#fit">{{ $button2 }}</a>
            </div>
            <div class="mt-8 flex flex-wrap gap-5 text-sm text-stone-600">
                <span>· 30 capsules</span>
                <span>· 15 minutes total</span>
                <span>· Gut Response Brief</span>
            </div>
        </div>
        <div class="animate-[fadeIn_.8s_ease-out_.1s_both] h-full flex">
            @if(isset($content['layout']) && $content['layout'] === 'image')
                @if($section->media)
                    <img src="{{ $section->media->url }}" alt="{{ $section->title }}" class="w-full object-cover rounded-[2rem] shadow-xl h-full min-h-[400px]">
                @else
                    <div class="flex aspect-square md:aspect-auto w-full md:h-full min-h-[400px] items-center justify-center rounded-[2rem] border-2 border-dashed border-stone-300 bg-[var(--panel)] text-stone-400 backdrop-blur">
                        Image pending uploading from CMS
                    </div>
                @endif
            @else
                <div class="overflow-hidden rounded-[2rem] border border-white/60 bg-white/80 shadow-[0_20px_80px_rgba(24,53,44,.12)] backdrop-blur w-full">
                    <div class="grid gap-0 md:grid-cols-[1.05fr_.95fr] h-full">
                        <div class="relative min-h-[26rem] bg-[linear-gradient(180deg,#f7f4ec_0%,#e9e1d2_100%)] p-8 md:p-10">
                            <div class="absolute left-8 top-8 h-28 w-28 rounded-full bg-[radial-gradient(circle,_rgba(24,53,44,.22),_transparent_72%)] blur-sm"></div>
                            <div class="absolute bottom-8 right-8 h-36 w-36 rounded-full bg-[radial-gradient(circle,_rgba(125,154,135,.28),_transparent_70%)] blur-sm"></div>
                            <div class="flex h-full items-center justify-center">
                                <div class="max-w-xs rounded-[1.8rem] border border-stone-200 bg-white/95 p-7 text-center shadow-xl">
                                    <div class="text-xs uppercase tracking-[.32em] text-stone-500">Guided Wellness</div>
                                    <div class="display-serif mt-4 text-5xl leading-none">30</div>
                                    <div class="mt-2 text-sm text-stone-500">Days of guided support</div>
                                    <div class="mt-6 rounded-2xl bg-[var(--cream)] px-4 py-3 text-sm text-stone-700">A fair trial, a lighter routine, a clearer read.</div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col justify-between bg-white p-8 md:p-10">
                            <div>
                                <div class="text-xs uppercase tracking-[.28em] text-stone-400">Standout experience</div>
                                <h2 class="display-serif mt-4 text-4xl leading-tight">30-Day Gut Reset</h2>
                                <div class="mt-3 text-2xl font-semibold text-[var(--ink)]">₹ X,XXX</div>
                                <p class="mt-5 text-sm leading-7 text-stone-600">
                                    A daily gut-support capsule paired with a lightly guided 30-day course, designed to give you a fair trial and an honest read.
                                </p>
                                <ul class="mt-6 space-y-3 text-sm text-stone-700">
                                    <li>· 30 days of support</li>
                                    <li>· Guided 30-day course</li>
                                    <li>· Gut Response Brief</li>
                                </ul>
                            </div>
                            <div class="mt-8 flex flex-wrap gap-3">
                                <a class="btn-primary" href="{{ route('shop') }}">Buy Now</a>
                                <a class="btn-secondary" href="{{ route('shop') }}">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
