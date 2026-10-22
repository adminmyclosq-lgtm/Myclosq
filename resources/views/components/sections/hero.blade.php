@props(['section'])
@php
    $cms = \App\Services\CmsSectionHelper::parse($section);
    $content = $cms['content'];
    
    $heroBadge = $content['badge'] ?? ($content['eyebrow'] ?? '30-Day Guided Gut Reset');
    $button1 = $content['button_1'] ?? ($content['primary_button_text'] ?? 'Explore the Reset');
    $button2 = $content['button_2'] ?? ($content['secondary_button_text'] ?? 'Check Your Fit');
    $button1Url = $content['button_1_url'] ?? ($content['primary_button_url'] ?? route('shop'));
    $button2Url = $content['button_2_url'] ?? ($content['secondary_button_url'] ?? '#fit');
    
    $fallbackButtons = [
        ['text' => $button1, 'url' => $button1Url, 'style' => 'primary'],
        ['text' => $button2, 'url' => $button2Url, 'style' => 'secondary']
    ];
    
    $imagePosition = $cms['imagePosition'] ?? 'right';
    $pills = is_array($content['pills'] ?? null) ? $content['pills'] : ['30 capsules', '15 minutes total', 'Gut Response Brief'];

    $mediaUrl = null;
    if ($section->media) {
        $mediaUrl = $section->media->storage_path ? asset('storage/'.$section->media->storage_path) : $section->media->url;
    } else {
        $mediaUrl = 'https://guided-gut-reset-lovable-app.lovable.app/assets/hero-product-CftTmpl1.jpg';
    }

    $bgImageStyle = ($imagePosition === 'background' && $mediaUrl) 
        ? "background-image: linear-gradient(rgba(245, 241, 232, 0.88), rgba(245, 241, 232, 0.88)), url('{$mediaUrl}'); background-size: cover; background-position: center;"
        : '';
@endphp
<section id="{{ $cms['anchorId'] }}" class="relative overflow-hidden {{ $cms['visibility'] }}" style="{{ $cms['style'] }} {{ $bgImageStyle }}">
    <div class="section {{ $cms['containerWidth'] }} {{ $cms['alignment'] }} {{ $imagePosition === 'background' || $imagePosition === 'none' ? 'py-6' : 'grid items-center gap-8 lg:grid-cols-[1.05fr_1fr] lg:gap-14' }}">
        
        <!-- Text Column -->
        <div class="max-w-xl animate-[fadeIn_.7s_ease-out_both] order-1 {{ $imagePosition === 'left' ? 'lg:order-2' : 'lg:order-1' }} {{ $imagePosition === 'background' || $imagePosition === 'none' ? 'mx-auto text-center' : '' }}">
            @if(!empty($heroBadge))
                <span class="badge" style="{{ !empty($cms['textColor']) ? 'border-color: currentColor; color: inherit;' : '' }}">{{ $heroBadge }}</span>
            @endif
            
            <h1 class="display-serif mt-4 sm:mt-5 text-3xl sm:text-4xl md:text-6xl lg:text-7xl leading-[1.1] sm:leading-[1.02] break-words {{ $cms['fontFamily'] }}" style="{{ $cms['headingStyle'] }}">
                {!! $section->title ?? 'Take it daily.<br>Check in lightly.<br>Know what changed.' !!}
            </h1>
            
            @if(!empty($section->subtitle))
                <p class="mt-4 sm:mt-6 max-w-md text-sm sm:text-[15px] leading-relaxed sm:leading-7 text-stone-600 break-words {{ $imagePosition === 'background' || $imagePosition === 'none' ? 'mx-auto' : '' }}" style="{{ $cms['bodyStyle'] }}">
                    {{ $section->subtitle }}
                </p>
            @endif

            <!-- Dynamic Buttons Component -->
            <x-sections.partials.buttons :buttons="$content['buttons'] ?? []" :cms="$cms" :fallback="$fallbackButtons" />

            <!-- Highlight Pills -->
            @if(!empty($pills))
                <div class="mt-7 sm:mt-9 flex flex-wrap gap-x-4 sm:gap-x-6 gap-y-2 text-[11px] font-medium uppercase tracking-[0.14em] text-stone-500 {{ $imagePosition === 'background' || $imagePosition === 'none' ? 'justify-center' : '' }}" style="{{ $cms['bodyStyle'] }}">
                    @foreach($pills as $pill)
                        @if(!empty(trim($pill)))
                            <span>· {{ trim($pill) }}</span>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Image Column (Only if not background and not none) -->
        @if($imagePosition !== 'background' && $imagePosition !== 'none')
            <div class="animate-[fadeIn_.8s_ease-out_.1s_both] overflow-hidden rounded-xl bg-[var(--sand)] shadow-[0_20px_60px_-30px_rgba(35,55,40,.35)] order-2 {{ $imagePosition === 'left' ? 'lg:order-1' : 'lg:order-2' }}">
                <img src="{{ $mediaUrl }}" alt="{{ strip_tags($section->title ?? '30-Day Gut Reset') }}" class="aspect-[4/3] w-full max-h-[380px] sm:max-h-[460px] object-cover lg:max-h-none lg:aspect-auto lg:min-h-[520px]">
            </div>
        @endif

    </div>
</section>
