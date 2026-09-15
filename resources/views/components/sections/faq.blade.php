@props(['section' => null])
@php
    $contentData = $section ? json_decode($section->content, true) : [];
    $settings = $contentData['settings'] ?? [];
    $topSpace = $settings['top_spacing'] ?? '80px';
    $bottomSpace = $settings['bottom_spacing'] ?? '80px';
    $bgColor = $settings['bg_color'] ?? 'bg-white';
    $items = isset($contentData['items']) && is_array($contentData['items']) ? $contentData['items'] : [];
@endphp
<section class="{{ !str_starts_with($bgColor??'', '#') ? $bgColor : '' }}" style="padding-top: {{ $topSpace }}; padding-bottom: {{ $bottomSpace }}; {{ str_starts_with($bgColor??'', '#') ? 'background-color: '.$bgColor.';' : '' }} {{ !empty($settings['button_primary_color']) ? '--btn-primary-color: '.$settings['button_primary_color'].';' : '' }} {{ !empty($settings['button_primary_text_color']) ? '--btn-primary-text-color: '.$settings['button_primary_text_color'].';' : '' }} {{ !empty($settings['button_secondary_color']) ? '--btn-secondary-color: '.$settings['button_secondary_color'].';' : '' }} {{ !empty($settings['button_secondary_text_color']) ? '--btn-secondary-text-color: '.$settings['button_secondary_text_color'].';' : '' }}" id="section-{{ $section->id ?? 'new' }}">
    <div class="section">
        <div class="max-w-3xl">
            @if($contentData['eyebrow'] ?? '')
                <span class="badge">{{ $contentData['eyebrow'] }}</span>
            @else
                <span class="badge">Questions</span>
            @endif
            <h2 class="mt-4 {{ $settings['title_font_family'] ?? 'display-serif' }} {{ $settings['title_font_size'] ?? 'text-4xl leading-tight' }}" @if(!empty($settings['title_font_color'])) style="color: {{ $settings['title_font_color'] }} {{ !empty($settings['button_primary_color']) ? '--btn-primary-color: '.$settings['button_primary_color'].';' : '' }} {{ !empty($settings['button_primary_text_color']) ? '--btn-primary-text-color: '.$settings['button_primary_text_color'].';' : '' }} {{ !empty($settings['button_secondary_color']) ? '--btn-secondary-color: '.$settings['button_secondary_color'].';' : '' }} {{ !empty($settings['button_secondary_text_color']) ? '--btn-secondary-text-color: '.$settings['button_secondary_text_color'].';' : '' }}" @endif>{{ $section->title ?? 'Questions you might have.' }}</h2>
            @if($section && $section->subtitle)<p class="mt-4 leading-7 text-stone-600 {{ $settings['body_font_family'] ?? '' }} {{ $settings['body_font_size'] ?? 'text-[15px]' }}" @if(!empty($settings['body_font_color'])) style="color: {{ $settings['body_font_color'] }} {{ !empty($settings['button_primary_color']) ? '--btn-primary-color: '.$settings['button_primary_color'].';' : '' }} {{ !empty($settings['button_primary_text_color']) ? '--btn-primary-text-color: '.$settings['button_primary_text_color'].';' : '' }} {{ !empty($settings['button_secondary_color']) ? '--btn-secondary-color: '.$settings['button_secondary_color'].';' : '' }} {{ !empty($settings['button_secondary_text_color']) ? '--btn-secondary-text-color: '.$settings['button_secondary_text_color'].';' : '' }}" @endif>{{ $section->subtitle }}</p>@endif
        </div>
        <div class="mt-10 grid gap-4 md:grid-cols-2">
            @if(count($items) > 0)
                @foreach($items as $item)
                    <div class="card">
                        <div class="font-semibold">{{ $item['title'] ?? '' }}</div>
                        <p class="mt-3 text-sm leading-6 text-stone-600">{{ $item['description'] ?? '' }}</p>
                    </div>
                @endforeach
            @else
                <div class="card">
                    <div class="font-semibold">Is this a probiotic?</div>
                    <p class="mt-3 text-sm leading-6 text-stone-600">The 30-Day Gut Reset is a gut-support capsule formulated with targeted botanicals and fibre-focused ingredients. We focus on strengthening the response, not naming a category.</p>
                </div>
                <div class="card">
                    <div class="font-semibold">How does the course last 30 days?</div>
                    <p class="mt-3 text-sm leading-6 text-stone-600">The bottle, course companion, and short guided moments are designed to keep the trial simple enough to repeat every day.</p>
                </div>
                <div class="card">
                    <div class="font-semibold">Can I start if I am already taking supplements?</div>
                    <p class="mt-3 text-sm leading-6 text-stone-600">You can compare your current routine with the fair-trial format, but the fit section should guide any medical caution first.</p>
                </div>
                <div class="card">
                    <div class="font-semibold">What happens at Day 30?</div>
                    <p class="mt-3 text-sm leading-6 text-stone-600">Your check-ins are summarised into a personal Gut Response Brief, which highlights what changed and what to consider next.</p>
                </div>
            @endif
        </div>
    </div>
</section>
