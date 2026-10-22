@props(['section' => null])
@php
    $cms = \App\Services\CmsSectionHelper::parse($section);
    $content = $cms['content'];
    $bgColor = $cms['bgColor'] ?: '';
    
    $fallbackButtons = [
        ['text' => $content['primary_button_text'] ?? ($content['button_1'] ?? 'Shop Gut Reset'), 'url' => $content['primary_button_url'] ?? ($content['button_1_url'] ?? route('shop')), 'style' => 'primary'],
        ['text' => $content['secondary_button_text'] ?? ($content['button_2'] ?? 'Check Your Fit'), 'url' => $content['secondary_button_url'] ?? ($content['button_2_url'] ?? '#fit'), 'style' => 'secondary']
    ];

    $imagePosition = $cms['imagePosition'] ?? 'right';
    $mediaUrl = null;
    if ($section && $section->media) {
        $mediaUrl = $section->media->storage_path ? asset('storage/'.$section->media->storage_path) : $section->media->url;
    }

    $cardBgStyle = ($imagePosition === 'background' && $mediaUrl)
        ? "background-image: linear-gradient(135deg, rgba(24,53,44,0.92) 0%, rgba(40,74,62,0.92) 100%), url('{$mediaUrl}'); background-size: cover; background-position: center;"
        : "background: linear-gradient(135deg, #18352c 0%, #284a3e 55%, #3c5c4e 100%);";
@endphp
<section id="{{ $cms['anchorId'] }}" class="{{ $bgColor }} {{ $cms['visibility'] }}" style="{{ $cms['style'] }}">
    <div class="section {{ $cms['containerWidth'] }} {{ $cms['alignment'] }}">
        <div class="rounded-2xl sm:rounded-[2rem] px-5 py-8 sm:px-8 sm:py-12 md:px-12 md:py-16 text-white shadow-lg overflow-hidden relative" style="{{ $cardBgStyle }}">
            <div class="grid gap-6 sm:gap-8 md:grid-cols-[1.25fr_.75fr] md:items-end relative z-10">
                <div>
                    <span class="badge bg-white/10 text-white border-0">{{ $content['badge'] ?? ($content['eyebrow'] ?? 'Start the course') }}</span>
                    <h2 class="display-serif mt-3 sm:mt-4 text-2xl sm:text-3xl md:text-5xl leading-tight break-words {{ $cms['fontFamily'] }}">
                        {{ $section->title ?? 'Give the capsule a fair 30-day trial.' }}
                    </h2>
                    <p class="mt-3 sm:mt-4 max-w-2xl text-white/80 text-sm sm:text-[15px] leading-relaxed break-words">
                        {{ $section->subtitle ?? 'Start the course and receive your Gut Response Brief at Day 30.' }}
                    </p>
                </div>
                <div class="w-full md:w-auto flex flex-col sm:flex-row gap-3 md:justify-end">
                    <!-- Dynamic Buttons -->
                    <x-sections.partials.buttons :buttons="$content['buttons'] ?? []" :cms="$cms" :fallback="$fallbackButtons" />
                </div>
            </div>
        </div>
    </div>
</section>
