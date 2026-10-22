@props(['section'])
@php
    $cms = \App\Services\CmsSectionHelper::parse($section);
    $content = $cms['content'];
    
    $badge = $content['badge'] ?? ($content['eyebrow'] ?? 'Standout experience');
    $cards = $content['cards'] ?? [
        ['title' => 'The daily capsule', 'text' => 'A gut-support supplement designed to support baseline gut health, taken once daily.'],
        ['title' => 'The guided experience', 'text' => 'Eight components that guide you through a 30-day process, observing signals and coming to an honest read.'],
        ['title' => 'Take it daily.', 'text' => 'Check in lightly. Know what changed.']
    ];
    $gridCols = count($cards) === 2 ? 'md:grid-cols-2' : (count($cards) >= 4 ? 'md:grid-cols-2 lg:grid-cols-4' : 'md:grid-cols-3');
@endphp
<section id="{{ $cms['anchorId'] }}" class="{{ $cms['visibility'] }}" style="{{ $cms['style'] }}">
    <div class="section {{ $cms['containerWidth'] }} {{ $cms['alignment'] }}">
        <div class="max-w-3xl">
            @if(!empty($badge))
                <span class="badge" style="{{ !empty($cms['textColor']) ? 'border-color: currentColor; color: inherit;' : '' }}">{{ $badge }}</span>
            @endif
            <h2 class="display-serif mt-4 text-2xl sm:text-3xl md:text-4xl lg:text-5xl leading-tight break-words {{ $cms['fontFamily'] }}" style="{{ $cms['headingStyle'] }}">
                {!! $section->title ?? 'The capsule creates the response.<br>The guided course helps make it readable.' !!}
            </h2>
            @if(!empty($section->subtitle))
                <p class="mt-3 sm:mt-4 text-stone-600 leading-relaxed text-sm sm:text-base break-words" style="{{ $cms['bodyStyle'] }}">{{ $section->subtitle }}</p>
            @endif
        </div>
        
        @if(!empty($cards) && is_array($cards))
            <div class="mt-8 sm:mt-12 grid gap-4 sm:gap-6 {{ $gridCols }}">
                @foreach($cards as $card)
                    @php
                        $cTitle = is_array($card) ? ($card['title'] ?? '') : (string)$card;
                        $cText = is_array($card) ? ($card['text'] ?? ($card['description'] ?? '')) : '';
                    @endphp
                    <div class="card bg-white p-5 sm:p-6 rounded-xl sm:rounded-2xl border border-stone-200/90 shadow-sm transition hover:shadow-md">
                        <div class="text-base font-semibold text-stone-900">{{ $cTitle }}</div>
                        @if(!empty($cText))
                            <p class="mt-3 text-sm leading-6 text-stone-600">{{ $cText }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Dynamic Action Buttons -->
        <x-sections.partials.buttons :buttons="$content['buttons'] ?? []" :cms="$cms" />
    </div>
</section>
