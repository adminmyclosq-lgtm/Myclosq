@props(['section'])
@php
    $content = is_string($section->content) ? json_decode($section->content, true) : ($section->content ?? []);
    if (!is_array($content)) $content = [];
    $settings = $content['settings'] ?? [];
    
    $badge = $content['badge'] ?? 'Transparency';
    $cards = $content['cards'] ?? [
        ['num' => '01', 'title' => 'Formulation', 'text' => 'Every ingredient is disclosed with its intended role and rationale.'],
        ['num' => '02', 'title' => 'Quality', 'text' => 'How quality is tested and where the limits of testing are.'],
        ['num' => '03', 'title' => 'Guidance', 'text' => 'Safety information is presented up-front so the product is used well.']
    ];
    
    $topStyle = isset($settings['top_spacing']) && $settings['top_spacing'] ? "padding-top: {$settings['top_spacing']};" : "padding-top: 80px;";
    $bottomStyle = isset($settings['bottom_spacing']) && $settings['bottom_spacing'] ? "padding-bottom: {$settings['bottom_spacing']};" : "padding-bottom: 80px;";
    $bgColor = isset($settings['bg_color']) && $settings['bg_color'] ? $settings['bg_color'] : "";
    
    if (!empty($settings['hidden'])) return;
@endphp
<section class="section {{ !str_starts_with($bgColor??'', '#') ? $bgColor : '' }}" id="standards" style="{{ $topStyle }} {{ $bottomStyle }} {{ str_starts_with($bgColor??'', '#') ? 'background-color: '.$bgColor.';' : '' }} {{ !empty($settings['button_primary_color']) ? '--btn-primary-color: '.$settings['button_primary_color'].';' : '' }} {{ !empty($settings['button_secondary_color']) ? '--btn-secondary-color: '.$settings['button_secondary_color'].';' : '' }}">
    <div class="max-w-3xl">
        <span class="badge">{{ $badge }}</span>
        <h2 class="mt-4 {{ $settings['title_font_family'] ?? 'display-serif' }} {{ $settings['title_font_size'] ?? 'text-4xl leading-tight' }}" @if(!empty($settings['title_font_color'])) style="color: {{ $settings['title_font_color'] }} {{ !empty($settings['button_primary_color']) ? '--btn-primary-color: '.$settings['button_primary_color'].';' : '' }} {{ !empty($settings['button_secondary_color']) ? '--btn-secondary-color: '.$settings['button_secondary_color'].';' : '' }}" @endif>{!! $section->title ?? 'Standards you should be able to inspect.' !!}</h2>
        <p class="mt-4 text-stone-600 {{ $settings['body_font_family'] ?? '' }} {{ $settings['body_font_size'] ?? 'text-[15px]' }}" @if(!empty($settings['body_font_color'])) style="color: {{ $settings['body_font_color'] }} {{ !empty($settings['button_primary_color']) ? '--btn-primary-color: '.$settings['button_primary_color'].';' : '' }} {{ !empty($settings['button_secondary_color']) ? '--btn-secondary-color: '.$settings['button_secondary_color'].';' : '' }}" @endif>{{ $section->subtitle ?? 'Transparency is not a feature; it is the foundation of trust.' }}</p>
        <a class="mt-6 inline-flex text-sm font-semibold text-[var(--ink)] underline decoration-stone-400 underline-offset-4" href="#learn">Explore standards</a>
    </div>
    <div class="mt-10 grid gap-5 md:grid-cols-3">
        @foreach($cards as $item)
            <div class="border-t border-stone-300 pt-5">
                <div class="text-sm text-stone-500">{{ $item['num'] ?? '01' }}</div>
                <h3 class="mt-2 text-xl font-bold">{{ $item['title'] ?? '' }}</h3>
                <p class="mt-3 leading-6 text-stone-600 {{ $settings['body_font_family'] ?? '' }} {{ $settings['body_font_size'] ?? 'text-sm' }}" @if(!empty($settings['body_font_color'])) style="color: {{ $settings['body_font_color'] }} {{ !empty($settings['button_primary_color']) ? '--btn-primary-color: '.$settings['button_primary_color'].';' : '' }} {{ !empty($settings['button_secondary_color']) ? '--btn-secondary-color: '.$settings['button_secondary_color'].';' : '' }}" @endif>{{ $item['text'] ?? '' }}</p>
                <div class="mt-4 text-sm font-semibold text-[var(--ink)]">Learn More →</div>
            </div>
        @endforeach
    </div>
</section>
