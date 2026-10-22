@props(['section' => null])
@php
    $cms = \App\Services\CmsSectionHelper::parse($section);
    $content = $cms['content'];
    $images = $content['images'] ?? [];
@endphp
<section id="{{ $cms['anchorId'] }}" class="{{ $cms['visibility'] }}" style="{{ $cms['style'] }}">
    <div class="section {{ $cms['containerWidth'] }} {{ $cms['alignment'] }}">
        @if($section && ($section->title || $section->subtitle))
            <div class="max-w-3xl mb-6 sm:mb-10">
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

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
            @if(count($images) > 0)
                @foreach($images as $img)
                    @php
                        $imgUrl = is_array($img) ? ($img['url'] ?? '') : (string)$img;
                    @endphp
                    @if(!empty($imgUrl))
                        <div class="group relative aspect-square overflow-hidden rounded-xl sm:rounded-2xl bg-stone-100 shadow-sm border border-stone-200/80">
                            <img src="{{ $imgUrl }}" alt="Gallery Image" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                        </div>
                    @endif
                @endforeach
            @else
                <div class="col-span-full py-12 text-center border-2 border-dashed border-stone-200 rounded-2xl text-stone-400 text-sm">
                    No gallery images added yet. Add image URLs in the Section Editor.
                </div>
            @endif
        </div>

        <!-- Dynamic Action Buttons -->
        <x-sections.partials.buttons :buttons="$content['buttons'] ?? []" :cms="$cms" />
    </div>
</section>
