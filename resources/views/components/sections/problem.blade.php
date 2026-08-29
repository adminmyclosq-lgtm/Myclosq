@props(['section'])
@php
    $content = is_string($section->content) ? json_decode($section->content, true) : ($section->content ?? []);
    if (!is_array($content)) $content = [];
    $settings = $content['settings'] ?? [];
    
    $badge = $content['badge'] ?? 'The problem';
    $cards = $content['cards'] ?? ['No clear starting point', 'Real-life disruptions get read as failure', 'The most recent moment becomes the impression'];
    
    $topStyle = isset($settings['top_spacing']) && $settings['top_spacing'] ? "padding-top: {$settings['top_spacing']};" : "padding-top: 80px;";
    $bottomStyle = isset($settings['bottom_spacing']) && $settings['bottom_spacing'] ? "padding-bottom: {$settings['bottom_spacing']};" : "padding-bottom: 80px;";
    
    if (!empty($settings['hidden'])) return;
@endphp
<section class="section" style="{{ $topStyle }} {{ $bottomStyle }}">
    <div class="grid gap-12 md:grid-cols-2 md:items-start">
        <div>
            <span class="badge">{{ $badge }}</span>
            <h2 class="display-serif mt-4 text-4xl leading-tight">{!! $section->title ?? 'Most gut resets leave you guessing.' !!}</h2>
        </div>
        <div class="space-y-4 leading-7 text-stone-600">
            <p>{{ $section->subtitle ?? 'A good day feels encouraging. A bad day feels like the capsule is not working. Without a fair 30-day trial, most decisions are made on impression.' }}</p>
            <div class="grid gap-3 sm:grid-cols-3">
                @foreach($cards as $card)
                    <div class="card text-sm">{{ is_array($card) ? ($card['title'] ?? '') : $card }}</div>
                @endforeach
            </div>
        </div>
    </div>
</section>
