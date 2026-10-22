@props(['section' => null])
@php
    $cms = \App\Services\CmsSectionHelper::parse($section);
    $content = $cms['content'];
    $videoUrl = $content['video_url'] ?? '';
    
    // Convert YouTube / Vimeo URL to embed
    $embedUrl = $videoUrl;
    if (str_contains($videoUrl, 'youtube.com/watch?v=')) {
        $embedUrl = str_replace('watch?v=', 'embed/', $videoUrl);
    } elseif (str_contains($videoUrl, 'youtu.be/')) {
        $embedUrl = str_replace('youtu.be/', 'www.youtube.com/embed/', $videoUrl);
    } elseif (str_contains($videoUrl, 'vimeo.com/')) {
        $embedUrl = str_replace('vimeo.com/', 'player.vimeo.com/video/', $videoUrl);
    }
@endphp
<section id="{{ $cms['anchorId'] }}" class="{{ $cms['visibility'] }}" style="{{ $cms['style'] }}">
    <div class="section {{ $cms['containerWidth'] }} {{ $cms['alignment'] }}">
        @if($section && ($section->title || $section->subtitle))
            <div class="max-w-3xl mb-6 sm:mb-8">
                @if(!empty($content['badge'] ?? ($content['eyebrow'] ?? '')))
                    <span class="badge" style="{{ !empty($cms['textColor']) ? 'border-color: currentColor; color: inherit;' : '' }}">{{ $content['badge'] ?? $content['eyebrow'] }}</span>
                @endif
                <h2 class="display-serif mt-3 text-2xl sm:text-3xl md:text-4xl leading-tight break-words {{ $cms['fontFamily'] }}" style="{{ $cms['headingStyle'] }}">
                    {{ $section->title }}
                </h2>
                @if($section->subtitle)
                    <p class="mt-2.5 sm:mt-3 text-stone-400 leading-relaxed text-sm sm:text-base break-words" style="{{ $cms['bodyStyle'] }}">{{ $section->subtitle }}</p>
                @endif
            </div>
        @endif
        
        <div class="relative aspect-video w-full overflow-hidden rounded-xl sm:rounded-2xl bg-black/40 shadow-2xl border border-white/10">
            @if($embedUrl)
                <iframe src="{{ $embedUrl }}" class="absolute inset-0 h-full w-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            @elseif($section && $section->media)
                <video src="{{ $section->media->storage_path ? asset('storage/'.$section->media->storage_path) : $section->media->url }}" controls class="h-full w-full object-cover"></video>
            @else
                <div class="flex h-full w-full items-center justify-center text-stone-400 text-sm">
                    Enter YouTube, Vimeo, or video URL in CMS Section Editor
                </div>
            @endif
        </div>

        <!-- Dynamic Action Buttons -->
        <x-sections.partials.buttons :buttons="$content['buttons'] ?? []" :cms="$cms" />
    </div>
</section>
