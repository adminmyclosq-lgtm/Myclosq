@props(['section'])
@php
    $content = is_string($section->content) ? json_decode($section->content, true) : ($section->content ?? []);
    if (!is_array($content)) $content = [];
    $settings = $content['settings'] ?? [];
    
    $badge = $content['badge'] ?? 'Standout experience';
    $cards = $content['cards'] ?? [
        ['title' => 'The daily capsule', 'text' => 'A gut-support supplement designed to support baseline gut health, taken once daily.'],
        ['title' => 'The guided experience', 'text' => 'Eight components that guide you through a 30-day process, observing signals and coming to an honest read.'],
        ['title' => 'Take it daily.', 'text' => 'Check in lightly. Know what changed.']
    ];
    
    $topStyle = isset($settings['top_spacing']) && $settings['top_spacing'] ? "padding-top: {$settings['top_spacing']};" : "padding-top: 80px;";
    $bottomStyle = isset($settings['bottom_spacing']) && $settings['bottom_spacing'] ? "padding-bottom: {$settings['bottom_spacing']};" : "padding-bottom: 80px;";
    $bgColor = isset($settings['bg_color']) && $settings['bg_color'] ? $settings['bg_color'] : "bg-[var(--cream)]";
    
    if (!empty($settings['hidden'])) return;
@endphp
<section class="{{ $bgColor }}" id="how-it-works" style="{{ $topStyle }} {{ $bottomStyle }}">
    <div class="section">
        <div class="max-w-3xl">
            <span class="badge">{{ $badge }}</span>
            <h2 class="display-serif mt-4 text-4xl leading-tight">{!! $section->title ?? 'The capsule creates the response.<br>The guided course helps make it readable.' !!}</h2>
        </div>
        <div class="mt-12 grid gap-6 md:grid-cols-3">
            @foreach($cards as $card)
                <div class="card">
                    <div class="text-sm font-semibold">{{ $card['title'] ?? '' }}</div>
                    <p class="mt-3 text-sm leading-6 text-stone-600">{{ $card['text'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
