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
    <div class="section">
        <div class="max-w-3xl">
            @if($contentData['eyebrow'] ?? '')
                <span class="badge">{{ $contentData['eyebrow'] }}</span>
            @else
                <span class="badge">Questions</span>
            @endif
            <h2 class="display-serif mt-4 text-4xl leading-tight">{{ $section->title ?? 'Questions you might have.' }}</h2>
            @if($section && $section->subtitle)<p class="mt-4 leading-7 text-stone-600">{{ $section->subtitle }}</p>@endif
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
