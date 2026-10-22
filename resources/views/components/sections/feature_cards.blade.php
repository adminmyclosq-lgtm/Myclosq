@props(['section' => null])
@php
    $cms = \App\Services\CmsSectionHelper::parse($section);
    $content = $cms['content'];
    
    $items = isset($content['items']) && is_array($content['items']) ? $content['items'] : [];
    if (empty($items)) {
        $items = [
            [
                'title' => 'May be relevant',
                'description' => "Seeking structured gut-health support\nStruggle with supplement consistency\nWant a more informed read\nValue data-driven personal insight"
            ],
            [
                'title' => 'Speak to a doctor first',
                'description' => "Pregnancy or breast-feeding\nDiagnosed gastrointestinal condition\nTaking prescription medications\nUnder medical supervision for chronic issues"
            ]
        ];
    }
@endphp
<section id="{{ $cms['anchorId'] }}" class="{{ $cms['visibility'] }}" style="{{ $cms['style'] }}">
    <div class="section {{ $cms['containerWidth'] }} {{ $cms['alignment'] }}">
        <span class="badge" style="{{ !empty($cms['textColor']) ? 'border-color: currentColor; color: inherit;' : '' }}">{{ $content['badge'] ?? ($content['eyebrow'] ?? 'Check your fit') }}</span>
        <h2 class="display-serif mt-4 text-2xl sm:text-3xl md:text-4xl leading-tight break-words {{ $cms['fontFamily'] }}" style="{{ $cms['headingStyle'] }}">
            {{ $section->title ?? 'Is this likely to be right for you?' }}
        </h2>
        @if($section && $section->subtitle)
            <p class="mt-3 sm:mt-4 text-stone-600 max-w-2xl leading-relaxed text-sm sm:text-base break-words" style="{{ $cms['bodyStyle'] }}">{{ $section->subtitle }}</p>
        @endif
        
        <div class="mt-8 sm:mt-10 grid gap-5 sm:gap-6 md:grid-cols-2">
            @foreach($items as $item)
                <div class="card rounded-2xl bg-white p-5 sm:p-7 border border-stone-200/90 shadow-sm">
                    <h3 class="font-bold text-lg text-stone-900">{{ $item['title'] ?? '' }}</h3>
                    @php 
                        $lines = is_array($item['description'] ?? '') 
                            ? $item['description'] 
                            : explode("\n", (string) ($item['description'] ?? '')); 
                    @endphp
                    <ul class="mt-4 sm:mt-5 space-y-2.5 sm:space-y-3 text-stone-600 text-sm">
                        @foreach($lines as $line)
                            @if(!empty(trim($line))) 
                                <li class="flex items-start gap-2.5">
                                    <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-[var(--ink)]"></span>
                                    <span>{{ ltrim(trim($line), '·- ') }}</span>
                                </li> 
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>

        <!-- Dynamic Action Buttons -->
        <x-sections.partials.buttons :buttons="$content['buttons'] ?? []" :cms="$cms" />
    </div>
</section>
