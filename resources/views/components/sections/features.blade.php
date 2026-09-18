@props(['section'])
@php
    $content = is_string($section->content) ? json_decode($section->content, true) : ($section->content ?? []);
    if (!is_array($content)) $content = [];
    $settings = $content['settings'] ?? [];
    $isMyClosq = !empty($isMyClosq) || in_array(request()->getHost(), ['myclosq.com', 'www.myclosq.com']);
    
    $badge = $content['badge'] ?? 'Standout experience';
    $cards = $content['cards'] ?? [];
    // Default fallback cards
    if (empty($cards)) {
        $cards = [
            ['title' => 'The daily capsule', 'text' => 'A gut-support supplement designed to support baseline gut health, taken once daily.'],
            ['title' => 'The guided experience', 'text' => 'Eight components that guide you through a 30-day process, observing signals and coming to an honest read.'],
        ];
    }
    // On myclosq, always ensure the third card "Your response brief" is present
    if ($isMyClosq && count($cards) < 3) {
        $cards[] = ['title' => 'Your response brief', 'text' => 'At Day 30, receive a personal Gut Response Brief — a clear read of what changed and what to do next.'];
    }
    
    $topStyle = isset($settings['top_spacing']) && $settings['top_spacing'] ? "padding-top: {$settings['top_spacing']};" : "padding-top: 80px;";
    $bottomStyle = isset($settings['bottom_spacing']) && $settings['bottom_spacing'] ? "padding-bottom: {$settings['bottom_spacing']};" : "padding-bottom: 80px;";
    $bgColor = isset($settings['bg_color']) && $settings['bg_color'] ? $settings['bg_color'] : "bg-[var(--cream)]";
    
    if (!empty($settings['hidden'])) return;
@endphp
<section class="{{ !str_starts_with($bgColor??'', '#') ? $bgColor : '' }}" id="how-it-works" style="{{ $topStyle }} {{ $bottomStyle }} {{ str_starts_with($bgColor??'', '#') ? 'background-color: '.$bgColor.';' : '' }} {{ !empty($settings['button_primary_color']) ? '--btn-primary-color: '.$settings['button_primary_color'].';' : '' }} {{ !empty($settings['button_secondary_color']) ? '--btn-secondary-color: '.$settings['button_secondary_color'].';' : '' }}">
    <div class="section">
        <div class="max-w-3xl">
            <span class="badge">{{ $badge }}</span>
            <h2 class="mt-4 {{ $settings['title_font_family'] ?? 'display-serif' }} {{ $settings['title_font_size'] ?? 'text-4xl leading-tight' }}" @if(!empty($settings['title_font_color'])) style="color: {{ $settings['title_font_color'] }} {{ !empty($settings['button_primary_color']) ? '--btn-primary-color: '.$settings['button_primary_color'].';' : '' }} {{ !empty($settings['button_secondary_color']) ? '--btn-secondary-color: '.$settings['button_secondary_color'].';' : '' }}" @endif>{!! $section->title ?? 'The capsule creates the response.<br>The guided course helps make it readable.' !!}</h2>
        </div>
        <div class="mt-12 grid gap-6 md:grid-cols-3">
            @foreach($cards as $card)
                <div class="card">
                    <div class="text-sm font-semibold">{{ $card['title'] ?? '' }}</div>
                    <p class="mt-3 leading-6 text-stone-600 {{ $settings['body_font_family'] ?? '' }} {{ $settings['body_font_size'] ?? 'text-sm' }}" @if(!empty($settings['body_font_color'])) style="color: {{ $settings['body_font_color'] }} {{ !empty($settings['button_primary_color']) ? '--btn-primary-color: '.$settings['button_primary_color'].';' : '' }} {{ !empty($settings['button_secondary_color']) ? '--btn-secondary-color: '.$settings['button_secondary_color'].';' : '' }}" @endif>{{ $card['text'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
