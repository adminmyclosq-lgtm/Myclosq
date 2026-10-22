@props(['section' => null])
@php
    $cms = \App\Services\CmsSectionHelper::parse($section);
    $content = $cms['content'];
    
    $items = isset($content['items']) && is_array($content['items']) ? $content['items'] : [];
    if (empty($items)) {
        $items = [
            ['title' => 'Is this a probiotic?', 'description' => 'The 30-Day Gut Reset is a gut-support capsule formulated with targeted botanicals and fibre-focused ingredients. We focus on strengthening the response, not naming a category.'],
            ['title' => 'How does the course last 30 days?', 'description' => 'The bottle, course companion, and short guided moments are designed to keep the trial simple enough to repeat every day.'],
            ['title' => 'Can I start if I am already taking supplements?', 'description' => 'You can compare your current routine with the fair-trial format, but the fit section should guide any medical caution first.'],
            ['title' => 'What happens at Day 30?', 'description' => 'Your check-ins are summarised into a personal Gut Response Brief, which highlights what changed and what to consider next.']
        ];
    }
@endphp
<section id="{{ $cms['anchorId'] }}" class="{{ $cms['visibility'] }}" style="{{ $cms['style'] }}">
    <div class="section {{ $cms['containerWidth'] }} {{ $cms['alignment'] }}">
        <div class="max-w-3xl">
            <span class="badge" style="{{ !empty($cms['textColor']) ? 'border-color: currentColor; color: inherit;' : '' }}">{{ $content['badge'] ?? ($content['eyebrow'] ?? 'Questions') }}</span>
            <h2 class="display-serif mt-4 text-2xl sm:text-3xl md:text-4xl leading-tight break-words {{ $cms['fontFamily'] }}" style="{{ $cms['headingStyle'] }}">
                {{ $section->title ?? 'Questions you might have.' }}
            </h2>
            @if($section && $section->subtitle)
                <p class="mt-3 sm:mt-4 text-sm sm:text-base leading-relaxed sm:leading-7 text-stone-600 break-words" style="{{ $cms['bodyStyle'] }}">{{ $section->subtitle }}</p>
            @endif
        </div>
        
        <div class="mt-8 sm:mt-10 grid gap-3 sm:gap-4 md:grid-cols-2">
            @foreach($items as $item)
                @php
                    $qTitle = is_array($item) ? ($item['title'] ?? '') : (string)$item;
                    $qDesc = is_array($item) ? ($item['description'] ?? ($item['text'] ?? '')) : '';
                @endphp
                <div class="card bg-stone-50/60 border border-stone-200/80 rounded-xl sm:rounded-2xl p-5 sm:p-6 transition hover:border-stone-300">
                    <div class="font-semibold text-stone-900 text-base">{{ $qTitle }}</div>
                    @if(!empty($qDesc))
                        <p class="mt-3 text-sm leading-6 text-stone-600">{{ $qDesc }}</p>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Dynamic Action Buttons -->
        <x-sections.partials.buttons :buttons="$content['buttons'] ?? []" :cms="$cms" />
    </div>
</section>
