@props(['section' => null])
@php
    $cms = \App\Services\CmsSectionHelper::parse($section);
    $content = $cms['content'];
    
    $items = isset($content['items']) && is_array($content['items']) ? $content['items'] : [];
    if (empty($items)) {
        $items = [
            ['title' => 'What changed', 'description' => 'Steadier after meals, improved baseline gut predictability.'],
            ['title' => 'Next step', 'description' => 'Move smoothly to the maintenance phase.']
        ];
    }
    $imagePosition = $cms['imagePosition'] ?? 'right';
    $mediaUrl = null;
    if ($section && $section->media) {
        $mediaUrl = $section->media->storage_path ? asset('storage/'.$section->media->storage_path) : $section->media->url;
    }

    $bgImageStyle = ($imagePosition === 'background' && $mediaUrl) 
        ? "background-image: linear-gradient(rgba(255, 255, 255, 0.92), rgba(255, 255, 255, 0.92)), url('{$mediaUrl}'); background-size: cover; background-position: center;"
        : '';
@endphp
<section id="{{ $cms['anchorId'] }}" class="{{ $cms['visibility'] }}" style="{{ $cms['style'] }} {{ $bgImageStyle }}">
    <div class="section {{ $cms['containerWidth'] }} {{ $cms['alignment'] }} {{ $imagePosition === 'background' || $imagePosition === 'none' ? 'py-4 max-w-3xl mx-auto' : 'grid gap-8 md:grid-cols-2 md:items-center' }}">
        
        <!-- Text Column -->
        <div class="order-1 {{ $imagePosition === 'left' ? 'md:order-2' : 'md:order-1' }}">
            <span class="badge" style="{{ !empty($cms['textColor']) ? 'border-color: currentColor; color: inherit;' : '' }}">{{ $content['badge'] ?? ($content['eyebrow'] ?? 'Day 30') }}</span>
            <h2 class="display-serif mt-4 text-2xl sm:text-3xl md:text-4xl leading-tight break-words {{ $cms['fontFamily'] }}" style="{{ $cms['headingStyle'] }}">
                {{ $section->title ?? 'Your Gut Response Brief at Day 30.' }}
            </h2>
            <p class="mt-3 sm:mt-4 text-sm sm:text-base leading-relaxed sm:leading-7 text-stone-600 break-words" style="{{ $cms['bodyStyle'] }}">
                {{ $section->subtitle ?? 'At the end of the 30 days you receive a personal Gut Response Brief. It is the read after your capsule course and a few short course moments.' }}
            </p>

            <!-- Dynamic Buttons -->
            <x-sections.partials.buttons :buttons="$content['buttons'] ?? []" :cms="$cms" :fallback="[['text' => $content['primary_button_text'] ?? 'See what you receive', 'url' => $content['primary_button_url'] ?? route('shop'), 'style' => 'primary']]" />
        </div>

        <!-- Extract Cards Column -->
        <div class="order-2 {{ $imagePosition === 'left' ? 'md:order-1' : 'md:order-2' }} card rounded-2xl border border-stone-200 bg-stone-50/70 p-4 sm:p-6 shadow-sm">
            <div class="text-xs uppercase font-bold tracking-wider text-stone-400 mb-4">Sample Brief Extract</div>
            <div class="grid gap-3 sm:gap-4 sm:grid-cols-2">
                @foreach($items as $item)
                    <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-stone-200/80">
                        <div class="text-xs text-stone-500 font-medium">{{ $item['title'] ?? '' }}</div>
                        <div class="mt-1 font-semibold text-stone-900 text-sm break-words">{{ $item['description'] ?? ($item['text'] ?? '') }}</div>
                    </div>
                @endforeach
            </div>
            @if($mediaUrl && $imagePosition !== 'background')
                <div class="mt-4 overflow-hidden rounded-xl border border-stone-200">
                    <img src="{{ $mediaUrl }}" alt="Sample Brief Preview" class="w-full h-36 object-cover">
                </div>
            @endif
        </div>

    </div>
</section>
