@props(['section'])
@php
    $content = is_string($section->content) ? json_decode($section->content, true) : ($section->content ?? []);
    if (!is_array($content)) $content = [];
    $settings = $content['settings'] ?? [];
    
    $badge = $content['badge'] ?? 'Learn';
    $cards = $content['cards'] ?? [
        ['title' => 'Methodology', 'text' => 'Why supplements are easy to start and difficult to judge.'],
        ['title' => 'Medical Context', 'text' => 'What digestive symptoms need medical attention.'],
        ['title' => 'Trial Design', 'text' => 'What a fair 30-day product trial looks like.']
    ];
    
    $topStyle = isset($settings['top_spacing']) && $settings['top_spacing'] ? "padding-top: {$settings['top_spacing']};" : "";
    $bottomStyle = isset($settings['bottom_spacing']) && $settings['bottom_spacing'] ? "padding-bottom: {$settings['bottom_spacing']};" : "";
    
    if (!empty($settings['hidden'])) return;
@endphp
<section class="section {{ $settings['bg_color'] ?? '' }}" id="learn" style="{{ $topStyle }} {{ $bottomStyle }} {{ !empty($settings['button_primary_color']) ? '--btn-primary-color: '.$settings['button_primary_color'].';' : '' }} {{ !empty($settings['button_secondary_color']) ? '--btn-secondary-color: '.$settings['button_secondary_color'].';' : '' }}">
    <div class="max-w-3xl">
        <span class="badge">{{ $badge }}</span>
        <h2 class="mt-4 {{ $settings['title_font_family'] ?? 'display-serif' }} {{ $settings['title_font_size'] ?? 'text-4xl leading-tight' }}" @if(!empty($settings['title_font_color'])) style="color: {{ $settings['title_font_color'] }} {{ !empty($settings['button_primary_color']) ? '--btn-primary-color: '.$settings['button_primary_color'].';' : '' }} {{ !empty($settings['button_secondary_color']) ? '--btn-secondary-color: '.$settings['button_secondary_color'].';' : '' }}" @endif>{!! $section->title ?? 'Understand your gut — and how to judge a trial.' !!}</h2>
    </div>
    <div class="mt-10 grid gap-5 md:grid-cols-3">
        @foreach ($cards as $item)
            <a href="{{ route('shop') }}" class="group overflow-hidden rounded-[1.75rem] border border-stone-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                <div class="aspect-[4/3] overflow-hidden bg-stone-100">
                    <img src="https://guided-gut-reset-lovable-app.lovable.app/assets/bottle-capsules-COsDFlLa.jpg" alt="Guided wellness article image" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                </div>
                <div class="p-5">
                    <div class="text-xs uppercase tracking-[.26em] text-stone-400">{{ is_array($item) ? ($item['title'] ?? '') : $item }}</div>
                    <div class="mt-2 text-lg font-semibold">{{ is_array($item) ? ($item['text'] ?? '') : '' }}</div>
                </div>
            </a>
        @endforeach
    </div>
</section>
