@props(['section'])
@php
    $cms = \App\Services\CmsSectionHelper::parse($section);
    $content = $cms['content'];
    
    $badge = $content['badge'] ?? ($content['eyebrow'] ?? 'Transparency');
    $cards = $content['cards'] ?? [
        ['num' => '01', 'title' => 'Formulation', 'text' => 'Every ingredient is disclosed with its intended role and rationale.'],
        ['num' => '02', 'title' => 'Quality', 'text' => 'How quality is tested and where the limits of testing are.'],
        ['num' => '03', 'title' => 'Guidance', 'text' => 'Safety information is presented up-front so the product is used well.']
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
                {!! $section->title ?? 'Standards you should be able to inspect.' !!}
            </h2>
            @if(!empty($section->subtitle))
                <p class="mt-3 sm:mt-4 text-stone-600 leading-relaxed text-sm sm:text-base break-words" style="{{ $cms['bodyStyle'] }}">{{ $section->subtitle }}</p>
            @endif
        </div>
        
        @if(!empty($cards) && is_array($cards))
            <div class="mt-8 sm:mt-10 grid gap-5 sm:gap-6 {{ $gridCols }}">
                @foreach($cards as $item)
                    <div class="border-t border-stone-300 pt-5 space-y-2">
                        <div class="text-xs font-mono font-bold text-stone-400">{{ $item['num'] ?? str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                        <h3 class="text-xl font-bold text-stone-900">{{ $item['title'] ?? '' }}</h3>
                        <p class="text-sm leading-6 text-stone-600">{{ $item['text'] ?? ($item['description'] ?? '') }}</p>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Dynamic Action Buttons -->
        <x-sections.partials.buttons :buttons="$content['buttons'] ?? []" :cms="$cms" :fallback="[['text' => 'Explore Standards', 'url' => route('our-standards'), 'style' => 'primary']]" />
    </div>
</section>
