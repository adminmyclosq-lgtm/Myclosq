@props(['section'])
@php
    $content = is_string($section->content) ? json_decode($section->content, true) : ($section->content ?? []);
    if (!is_array($content)) $content = [];
    $settings = $content['settings'] ?? [];
    
    $badge = $content['badge'] ?? 'The problem';
    $cards = $content['cards'] ?? ['No clear starting point', 'Real-life disruptions get read as failure', 'The most recent moment becomes the impression'];
    
    $topStyle = isset($settings['top_spacing']) && $settings['top_spacing'] ? "padding-top: {$settings['top_spacing']};" : "padding-top: 80px;";
    $bottomStyle = isset($settings['bottom_spacing']) && $settings['bottom_spacing'] ? "padding-bottom: {$settings['bottom_spacing']};" : "padding-bottom: 80px;";
    $bgColor = isset($settings['bg_color']) && $settings['bg_color'] ? $settings['bg_color'] : "bg-background";
    
    if (!empty($settings['hidden'])) return;
@endphp
<section class="border-t border-border/60 {{ !str_starts_with($bgColor??'', '#') ? $bgColor : '' }}" style="{{ $topStyle }} {{ $bottomStyle }} {{ str_starts_with($bgColor??'', '#') ? 'background-color: '.$bgColor.';' : '' }} {{ !empty($settings['button_primary_color']) ? '--btn-primary-color: '.$settings['button_primary_color'].';' : '' }} {{ !empty($settings['button_secondary_color']) ? '--btn-secondary-color: '.$settings['button_secondary_color'].';' : '' }}">
    <div class="section grid gap-10 lg:grid-cols-[1fr_1fr] lg:gap-16 lg:items-center">
        <div class="overflow-hidden rounded-2xl bg-cream">
            @if($section->media)
                <img src="{{ $section->media->storage_path ? asset('storage/'.$section->media->storage_path) : $section->media->url }}" alt="{{ $section->title }}" class="h-full w-full object-cover">
            @else
                <div class="flex aspect-square md:aspect-auto w-full md:h-full min-h-[400px] items-center justify-center border-2 border-dashed border-stone-300 bg-[var(--panel)] text-stone-400 backdrop-blur text-sm">
                    Image pending uploading from CMS
                </div>
            @endif
        </div>
        <div>
            <div class="badge mb-4">{{ $badge }}</div>
            <h2 class="{{ $settings['title_font_family'] ?? 'font-serif' }} {{ $settings['title_font_size'] ?? 'text-4xl leading-[1.1] md:text-5xl' }} text-foreground" @if(!empty($settings['title_font_color'])) style="color: {{ $settings['title_font_color'] }} {{ !empty($settings['button_primary_color']) ? '--btn-primary-color: '.$settings['button_primary_color'].';' : '' }} {{ !empty($settings['button_secondary_color']) ? '--btn-secondary-color: '.$settings['button_secondary_color'].';' : '' }}" @endif>{!! $section->title ?? 'Most gut resets leave you guessing.' !!}</h2>
            <p class="mt-6 max-w-md leading-relaxed text-foreground/70 {{ $settings['body_font_family'] ?? '' }} {{ $settings['body_font_size'] ?? 'text-[15px]' }}" @if(!empty($settings['body_font_color'])) style="color: {{ $settings['body_font_color'] }} {{ !empty($settings['button_primary_color']) ? '--btn-primary-color: '.$settings['button_primary_color'].';' : '' }} {{ !empty($settings['button_secondary_color']) ? '--btn-secondary-color: '.$settings['button_secondary_color'].';' : '' }}" @endif>{{ $section->subtitle }}</p>
            
            @if(!empty($cards))
            <ul class="mt-8 space-y-4 text-[14px] text-foreground/75">
                @foreach($cards as $card)
                    <li class="flex gap-3">
                        <span class="mt-2 h-1 w-1 shrink-0 rounded-full bg-primary"></span> 
                        <span>{{ is_array($card) ? ($card['title'] ?? '') : $card }}</span>
                    </li>
                @endforeach
            </ul>
            @endif
        </div>
    </div>
</section>
