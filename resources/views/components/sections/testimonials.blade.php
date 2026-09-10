@props(['section' => null])
@php
    $contentData = $section ? json_decode($section->content, true) : [];
    $settings = $contentData['settings'] ?? [];
    $topSpace = $settings['top_spacing'] ?? '80px';
    $bottomSpace = $settings['bottom_spacing'] ?? '80px';
    $bgColor = $settings['bg_color'] ?? 'bg-white';
    $items = isset($contentData['items']) && is_array($contentData['items']) ? $contentData['items'] : [];
@endphp
<section class="{{ $bgColor }}" style="padding-top: {{ $topSpace }}; padding-bottom: {{ $bottomSpace }};" id="section-{{ $section->id ?? 'new' }}">
    <div class="section grid gap-10 md:grid-cols-2 md:items-center">
        <div>
            @if($contentData['eyebrow'] ?? '')
                <span class="badge">{{ $contentData['eyebrow'] }}</span>
            @else
                <span class="badge">Day 30</span>
            @endif
            <h2 class="mt-4 {{ $settings['title_font_family'] ?? 'display-serif' }} {{ $settings['title_font_size'] ?? 'text-4xl leading-tight' }}" @if(!empty($settings['title_font_color'])) style="color: {{ $settings['title_font_color'] }}" @endif>{{ $section->title ?? 'Your Gut Response Brief at Day 30.' }}</h2>
            <p class="mt-4 leading-7 text-stone-600 {{ $settings['body_font_family'] ?? '' }} {{ $settings['body_font_size'] ?? 'text-[15px]' }}" @if(!empty($settings['body_font_color'])) style="color: {{ $settings['body_font_color'] }}" @endif>{{ $section->subtitle ?? 'At the end of the 30 days you receive a personal Gut Response Brief. It is the read after your capsule course and a few short course moments.' }}</p>
            @if($contentData['primary_button_text'] ?? '')
                <a class="mt-6 inline-flex text-sm font-semibold text-[var(--ink)] underline decoration-stone-400 underline-offset-4" href="{{ $contentData['primary_button_url'] ?? '#' }}">{{ $contentData['primary_button_text'] }}</a>
            @else
                <a class="mt-6 inline-flex text-sm font-semibold text-[var(--ink)] underline decoration-stone-400 underline-offset-4" href="{{ route('shop') }}">See what you receive</a>
            @endif
        </div>
        <div class="card">
            <div class="text-sm font-semibold">Example brief</div>
            <div class="mt-5 grid gap-4 sm:grid-cols-2">
                @if(count($items) > 0)
                    @foreach($items as $item)
                        <div>
                            <div class="text-xs text-stone-500">{{ $item['title'] ?? '' }}</div>
                            <div class="mt-1 font-semibold">{{ $item['description'] ?? '' }}</div>
                        </div>
                    @endforeach
                @else
                    <div>
                        <div class="text-xs text-stone-500">What changed</div>
                        <div class="mt-1 font-semibold">Steadier after meals</div>
                    </div>
                    <div>
                        <div class="text-xs text-stone-500">Next step</div>
                        <div class="mt-1 font-semibold">Move to maintenance</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
