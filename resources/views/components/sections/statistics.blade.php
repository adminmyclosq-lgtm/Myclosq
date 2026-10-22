@props(['section' => null])
@php
    $cms = \App\Services\CmsSectionHelper::parse($section);
    $content = $cms['content'];
    $stats = $content['stats'] ?? [
        ['value' => '30', 'unit' => 'Days', 'label' => 'Structured capsule course'],
        ['value' => '15', 'unit' => 'Minutes', 'label' => 'Total course check-in time'],
        ['value' => '100%', 'unit' => 'Traceable', 'label' => 'Clinical ingredient disclosure'],
        ['value' => '1:1', 'unit' => 'Read', 'label' => 'Personal Gut Response Brief']
    ];
    $colsClass = count($stats) <= 2 ? 'grid-cols-2 max-w-xl mx-auto' : (count($stats) === 3 ? 'grid-cols-1 sm:grid-cols-3' : 'grid-cols-2 md:grid-cols-4');
@endphp
<section id="{{ $cms['anchorId'] }}" class="{{ $cms['visibility'] }}" style="{{ $cms['style'] }}">
    <div class="section {{ $cms['containerWidth'] }} {{ $cms['alignment'] }}">
        @if($section && ($section->title || $section->subtitle))
            <div class="max-w-3xl mb-8 sm:mb-12 text-center mx-auto">
                @if(!empty($content['badge'] ?? ($content['eyebrow'] ?? '')))
                    <span class="badge" style="{{ !empty($cms['textColor']) ? 'border-color: currentColor; color: inherit;' : '' }}">{{ $content['badge'] ?? $content['eyebrow'] }}</span>
                @endif
                <h2 class="display-serif mt-3 text-2xl sm:text-3xl md:text-4xl leading-tight break-words {{ $cms['fontFamily'] }}" style="{{ $cms['headingStyle'] }}">
                    {{ $section->title }}
                </h2>
                @if($section->subtitle)
                    <p class="mt-2.5 sm:mt-3 text-stone-600 leading-relaxed text-sm sm:text-base break-words" style="{{ $cms['bodyStyle'] }}">{{ $section->subtitle }}</p>
                @endif
            </div>
        @endif

        @if(!empty($stats) && is_array($stats))
            <div class="grid {{ $colsClass }} gap-3 sm:gap-6">
                @foreach($stats as $stat)
                    <div class="rounded-xl sm:rounded-2xl bg-white p-4 sm:p-6 border border-stone-200/90 shadow-sm text-center">
                        <div class="display-serif text-3xl sm:text-4xl md:text-5xl font-bold text-[var(--ink)]">
                            {{ $stat['value'] ?? '' }}
                            @if(!empty($stat['unit']))
                                <span class="text-xs uppercase font-sans tracking-widest text-stone-400 block mt-1 font-semibold">{{ $stat['unit'] }}</span>
                            @endif
                        </div>
                        <p class="mt-2 sm:mt-3 text-xs md:text-sm text-stone-600 font-medium">{{ $stat['label'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Dynamic Action Buttons -->
        <x-sections.partials.buttons :buttons="$content['buttons'] ?? []" :cms="$cms" />
    </div>
</section>
