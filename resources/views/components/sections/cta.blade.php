@props(['section' => null])
@php
    $contentData = $section ? json_decode($section->content, true) : [];
    $settings = $contentData['settings'] ?? [];
    $topSpace = $settings['top_spacing'] ?? '80px';
    $bottomSpace = $settings['bottom_spacing'] ?? '80px';
    $bgColor = $settings['bg_color'] ?? '';
@endphp
<section class="section {{ $bgColor }}" style="padding-top: {{ $topSpace }}; padding-bottom: {{ $bottomSpace }};" id="section-{{ $section->id ?? 'new' }}">
    <div class="rounded-[2rem] bg-[linear-gradient(135deg,#18352c_0%,#284a3e_55%,#3c5c4e_100%)] px-6 py-12 text-white md:px-12 md:py-16">
        <div class="grid gap-8 md:grid-cols-[1.25fr_.75fr] md:items-end">
            <div>
                <span class="badge bg-white/10 text-white">{{ $contentData['eyebrow'] ?? 'Start the course' }}</span>
                <h2 class="display-serif mt-4 text-5xl leading-tight">{{ $section->title ?? 'Give the capsule a fair 30-day trial.' }}</h2>
                <p class="mt-4 max-w-2xl text-white/80">{{ $section->subtitle ?? 'Start the course and receive your Gut Response Brief at Day 30.' }}</p>
            </div>
            <div class="flex flex-wrap gap-3 md:justify-end">
                <a class="btn-primary !bg-white !text-[var(--ink)]" href="{{ $contentData['primary_button_url'] ?? route('shop') }}">{{ $contentData['primary_button_text'] ?? 'Shop Myclosq' }}</a>
                <a class="btn-secondary !border-white !text-white hover:bg-white/10" href="{{ $contentData['secondary_button_url'] ?? '#fit' }}">{{ $contentData['secondary_button_text'] ?? 'Check Your Fit' }}</a>
            </div>
        </div>
    </div>
</section>
