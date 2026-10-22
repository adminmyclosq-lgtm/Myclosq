@props(['section'])
@php
    $cms = \App\Services\CmsSectionHelper::parse($section);
    $content = $cms['content'];
    
    $badge = $content['badge'] ?? ($content['eyebrow'] ?? 'The problem');
    $cards = !empty($content['bullets']) ? $content['bullets'] : ($content['cards'] ?? ['No clear starting point', 'Real-life disruptions get read as failure', 'The most recent moment becomes the impression']);
    $imagePosition = $cms['imagePosition'] ?? 'left';

    $mediaUrl = null;
    if ($section->media) {
        $mediaUrl = $section->media->storage_path ? asset('storage/'.$section->media->storage_path) : $section->media->url;
    }

    $bgImageStyle = ($imagePosition === 'background' && $mediaUrl) 
        ? "background-image: linear-gradient(rgba(255, 255, 255, 0.90), rgba(255, 255, 255, 0.90)), url('{$mediaUrl}'); background-size: cover; background-position: center;"
        : '';
@endphp
<section id="{{ $cms['anchorId'] }}" class="border-t border-stone-200/60 {{ $cms['visibility'] }}" style="{{ $cms['style'] }} {{ $bgImageStyle }}">
    <div class="section {{ $cms['containerWidth'] }} {{ $cms['alignment'] }} {{ $imagePosition === 'background' || $imagePosition === 'none' ? 'py-4' : 'grid gap-8 lg:grid-cols-[1fr_1fr] lg:gap-16 lg:items-center' }}">
        
        <!-- Image Container -->
        @if($imagePosition !== 'background' && $imagePosition !== 'none')
            <div class="order-2 {{ $imagePosition === 'right' ? 'lg:order-2' : 'lg:order-1' }} overflow-hidden rounded-2xl bg-cream">
                @if($mediaUrl)
                    <img src="{{ $mediaUrl }}" alt="{{ $section->title }}" class="h-64 sm:h-80 lg:h-full w-full object-cover">
                @else
                    <div class="flex aspect-square md:aspect-auto w-full h-64 sm:h-80 lg:h-full items-center justify-center border-2 border-dashed border-stone-300 bg-[var(--panel)] text-stone-400 backdrop-blur text-sm">
                        Image pending from CMS
                    </div>
                @endif
            </div>
        @endif

        <!-- Text Content -->
        <div class="order-1 {{ $imagePosition === 'right' ? 'lg:order-1' : 'lg:order-2' }} {{ $imagePosition === 'background' || $imagePosition === 'none' ? 'max-w-2xl mx-auto' : '' }}">
            @if(!empty($badge))
                <div class="badge mb-4" style="{{ !empty($cms['textColor']) ? 'border-color: currentColor; color: inherit;' : '' }}">{{ $badge }}</div>
            @endif
            
            <h2 class="font-serif text-2xl sm:text-3xl md:text-5xl leading-tight break-words {{ $cms['fontFamily'] }}" style="{{ $cms['headingStyle'] }}">
                {!! $section->title ?? 'Most gut resets leave you guessing.' !!}
            </h2>
            
            @if(!empty($section->subtitle))
                <p class="mt-4 sm:mt-6 max-w-md text-sm sm:text-[15px] leading-relaxed break-words" style="{{ $cms['bodyStyle'] }}">{{ $section->subtitle }}</p>
            @endif
            
            @if(!empty($cards) && is_array($cards))
                <ul class="mt-6 sm:mt-8 space-y-3 sm:space-y-4 text-sm" style="{{ $cms['bodyStyle'] }}">
                    @foreach($cards as $card)
                        @php
                            $cardText = is_array($card) ? ($card['title'] ?? ($card['text'] ?? '')) : $card;
                        @endphp
                        @if(!empty(trim($cardText)))
                            <li class="flex items-start gap-3">
                                <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-emerald-700"></span> 
                                <span>{{ $cardText }}</span>
                            </li>
                        @endif
                    @endforeach
                </ul>
            @endif

            <!-- Dynamic Buttons Component -->
            <x-sections.partials.buttons :buttons="$content['buttons'] ?? []" :cms="$cms" />
        </div>

    </div>
</section>
