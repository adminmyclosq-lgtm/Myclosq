@props(['section' => null])
@php
    $cms = \App\Services\CmsSectionHelper::parse($section);
    $content = $cms['content'];
    
    $eyebrow = $content['badge'] ?? ($content['eyebrow'] ?? 'What you receive');
    $title = $section->title ?? 'Everything needed to start correctly and return easily.';
    $subtitle = $section->subtitle ?? "The Capsule Bottle — discreetly labelled, tightly designed for one capsule daily.\nCourse Companion — a concise activation card and course guide.\nGuided Course Access — permanent bottle QR to start or return to your guided course.";
    
    $listItems = is_array($content['items'] ?? null) 
        ? $content['items'] 
        : (is_array($subtitle) ? $subtitle : explode("\n", (string) $subtitle));
        
    $imagePosition = $cms['imagePosition'] ?? 'right';

    $mediaUrl = null;
    if ($section && $section->media) {
        $mediaUrl = $section->media->storage_path ? asset('storage/'.$section->media->storage_path) : $section->media->url;
    }

    $bgImageStyle = ($imagePosition === 'background' && $mediaUrl) 
        ? "background-image: linear-gradient(rgba(245, 241, 232, 0.90), rgba(245, 241, 232, 0.90)), url('{$mediaUrl}'); background-size: cover; background-position: center;"
        : '';
@endphp
<section id="{{ $cms['anchorId'] }}" class="{{ $cms['visibility'] }}" style="{{ $cms['style'] }} {{ $bgImageStyle }}">
    <div class="section {{ $cms['containerWidth'] }} {{ $cms['alignment'] }} {{ $imagePosition === 'background' || $imagePosition === 'none' ? 'py-4 max-w-3xl mx-auto' : 'grid gap-8 md:grid-cols-2 md:items-center' }}">
        
        <!-- Text Column -->
        <div class="order-1 {{ $imagePosition === 'left' ? 'md:order-2' : 'md:order-1' }}">
            @if(!empty($eyebrow))
                <span class="badge" style="{{ !empty($cms['textColor']) ? 'border-color: currentColor; color: inherit;' : '' }}">{{ $eyebrow }}</span>
            @endif
            
            <h2 class="display-serif mt-4 text-2xl sm:text-3xl md:text-4xl lg:text-5xl leading-tight break-words {{ $cms['fontFamily'] }}" style="{{ $cms['headingStyle'] }}">
                {{ $title }}
            </h2>
            
            @if(!empty($listItems))
                <ul class="mt-6 sm:mt-7 space-y-3 sm:space-y-4 text-sm sm:text-base text-stone-700" style="{{ $cms['bodyStyle'] }}">
                    @foreach($listItems as $item)
                        @php
                            $line = is_array($item) ? ($item['title'] ?? ($item['text'] ?? '')) : (string)$item;
                        @endphp
                        @if(!empty(trim($line))) 
                            <li class="flex items-start gap-3">
                                <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-[var(--ink)]"></span>
                                <span>{{ trim($line) }}</span>
                            </li> 
                        @endif
                    @endforeach
                </ul>
            @endif

            @if(!empty($content['primary_button_text']))
                <div class="mt-6 text-sm uppercase tracking-[.22em] text-stone-500 font-semibold" style="{{ $cms['bodyStyle'] }}">
                    {{ $content['primary_button_text'] }}
                </div>
            @endif

            <!-- Dynamic Action Buttons -->
            <x-sections.partials.buttons :buttons="$content['buttons'] ?? []" :cms="$cms" />
        </div>

        <!-- Image Column -->
        @if($imagePosition !== 'background' && $imagePosition !== 'none')
            <div class="order-2 {{ $imagePosition === 'left' ? 'md:order-1' : 'md:order-2' }} overflow-hidden rounded-2xl bg-white p-3 sm:p-4 shadow-sm border border-stone-200">
                @if($mediaUrl)
                    <img src="{{ $mediaUrl }}" alt="{{ $title }}" class="h-56 sm:h-72 md:h-full w-full object-cover rounded-xl">
                @else
                    <div class="aspect-[4/3] h-56 sm:h-72 md:h-full rounded-xl bg-[var(--sand)]/50 flex items-center justify-center text-stone-400 text-sm">
                        Course Companion & Bottle Presentation
                    </div>
                @endif
            </div>
        @endif

    </div>
</section>
