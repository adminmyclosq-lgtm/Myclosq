@props(['section'])
@php
    $cms = \App\Services\CmsSectionHelper::parse($section);
    $content = $cms['content'];
    
    $badge = $content['badge'] ?? ($content['eyebrow'] ?? 'Learn');
    $cards = $content['cards'] ?? [
        ['title' => 'Methodology', 'text' => 'Why supplements are easy to start and difficult to judge.', 'url' => route('learn')],
        ['title' => 'Medical Context', 'text' => 'What digestive symptoms need medical attention.', 'url' => route('learn')],
        ['title' => 'Trial Design', 'text' => 'What a fair 30-day product trial looks like.', 'url' => route('learn')]
    ];
    $gridCols = count($cards) === 2 ? 'md:grid-cols-2' : (count($cards) >= 4 ? 'md:grid-cols-2 lg:grid-cols-4' : 'md:grid-cols-3');

    $defaultMediaUrl = 'https://guided-gut-reset-lovable-app.lovable.app/assets/bottle-capsules-COsDFlLa.jpg';
    if ($section->media) {
        $defaultMediaUrl = $section->media->storage_path ? asset('storage/'.$section->media->storage_path) : $section->media->url;
    }
@endphp
<section id="{{ $cms['anchorId'] }}" class="{{ $cms['visibility'] }}" style="{{ $cms['style'] }}">
    <div class="section {{ $cms['containerWidth'] }} {{ $cms['alignment'] }}">
        <div class="max-w-3xl">
            @if(!empty($badge))
                <span class="badge" style="{{ !empty($cms['textColor']) ? 'border-color: currentColor; color: inherit;' : '' }}">{{ $badge }}</span>
            @endif
            <h2 class="display-serif mt-4 text-2xl sm:text-3xl md:text-4xl leading-tight break-words {{ $cms['fontFamily'] }}" style="{{ $cms['headingStyle'] }}">
                {!! $section->title ?? 'Understand your gut — and how to judge a trial.' !!}
            </h2>
            @if(!empty($section->subtitle))
                <p class="mt-3 sm:mt-4 text-stone-600 leading-relaxed text-sm sm:text-base break-words" style="{{ $cms['bodyStyle'] }}">{{ $section->subtitle }}</p>
            @endif
        </div>
        
        @if(!empty($cards) && is_array($cards))
            <div class="mt-8 sm:mt-10 grid gap-5 sm:gap-6 {{ $gridCols }}">
                @foreach ($cards as $item)
                    @php
                        $cTitle = is_array($item) ? ($item['title'] ?? '') : (string)$item;
                        $cText = is_array($item) ? ($item['text'] ?? ($item['description'] ?? '')) : '';
                        $cUrl = is_array($item) ? ($item['url'] ?? route('learn')) : route('learn');
                    @endphp
                    <a href="{{ $cUrl }}" class="group overflow-hidden rounded-2xl sm:rounded-[1.75rem] border border-stone-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="aspect-[4/3] overflow-hidden bg-stone-100">
                            <img src="{{ $defaultMediaUrl }}" alt="{{ $cTitle }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                        </div>
                        <div class="p-4 sm:p-5">
                            <div class="text-xs uppercase tracking-[.26em] text-stone-400 font-semibold">{{ $cTitle }}</div>
                            <div class="mt-2 text-base font-semibold text-stone-900 leading-snug">{{ $cText }}</div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

        <!-- Dynamic Action Buttons -->
        <x-sections.partials.buttons :buttons="$content['buttons'] ?? []" :cms="$cms" />
    </div>
</section>
