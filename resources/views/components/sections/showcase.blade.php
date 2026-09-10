@props(['section' => null])
@php
    $contentData = $section ? json_decode($section->content, true) : [];
    $settings = $contentData['settings'] ?? [];
    $topSpace = $settings['top_spacing'] ?? '80px';
    $bottomSpace = $settings['bottom_spacing'] ?? '80px';
    $bgColor = $settings['bg_color'] ?? 'bg-[var(--cream)]';
    $eyebrow = $contentData['eyebrow'] ?? 'What you receive';
    $title = $section->title ?? 'Everything needed to start correctly and return easily.';
    $subtitle = $section->subtitle ?? "The Capsule Bottle — discreetly labelled, tightly designed for one capsule daily.\nCourse Companion — a concise activation card and course guide.\nGuided Course Access — permanent bottle QR to start or return to your guided course.";
    $listItems = explode("\n", $subtitle);
@endphp
<section class="{{ $bgColor }}" style="padding-top: {{ $topSpace }}; padding-bottom: {{ $bottomSpace }};" id="section-{{ $section->id ?? 'new' }}">
    <div class="section grid gap-10 md:grid-cols-2 md:items-center">
        <div>
            @if($eyebrow)<span class="badge">{{ $eyebrow }}</span>@endif
            <h2 class="mt-4 {{ $settings['title_font_family'] ?? 'display-serif' }} {{ $settings['title_font_size'] ?? 'text-4xl leading-tight' }}" @if(!empty($settings['title_font_color'])) style="color: {{ $settings['title_font_color'] }}" @endif>{{ $title }}</h2>
            @if(count($listItems) > 0)
            <ul class="mt-7 space-y-4 text-stone-700 {{ $settings['body_font_family'] ?? '' }} {{ $settings['body_font_size'] ?? 'text-base' }}" @if(!empty($settings['body_font_color'])) style="color: {{ $settings['body_font_color'] }}" @endif>
                @foreach($listItems as $item)
                    @if(trim($item)) <li>{{ $item }}</li> @endif
                @endforeach
            </ul>
            @endif
            @if(isset($contentData['primary_button_text']) && $contentData['primary_button_text'])
                <div class="mt-6 text-sm uppercase tracking-[.22em] text-stone-500">{{ $contentData['primary_button_text'] }}</div>
            @else
                <div class="mt-6 text-sm uppercase tracking-[.22em] text-stone-500">Take 30. Spend 15. Know your gut.</div>
            @endif
        </div>
        <div class="grid gap-4">
            @if($section && $section->media)
                <div class="overflow-hidden rounded-[2rem] border border-stone-200 bg-white shadow-sm">
                    <img src="{{ $section->media->storage_path ? asset('storage/'.$section->media->storage_path) : $section->media->url }}" alt="{{ $section->media->file_name }}" class="h-full w-full object-cover">
                </div>
            @else
                <div class="overflow-hidden rounded-[2rem] border border-stone-200 bg-white shadow-sm">
                    <img src="https://guided-gut-reset-lovable-app.lovable.app/assets/course-kit-DtVPc2tT.jpg" alt="Open 30-day course kit" class="h-full w-full object-cover">
                </div>
            @endif
        </div>
    </div>
</section>
