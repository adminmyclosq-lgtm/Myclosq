@props(['section' => null])
@php
    $cms = \App\Services\CmsSectionHelper::parse($section);
    $content = $cms['content'];
    $html = $content['html'] ?? ($section->subtitle ?? '');
@endphp
<section id="{{ $cms['anchorId'] }}" class="{{ $cms['visibility'] }}" style="{{ $cms['style'] }}">
    <div class="section {{ $cms['containerWidth'] }} {{ $cms['alignment'] }}">
        @if($section && $section->title)
            <div class="mb-8">
                <h2 class="display-serif text-3xl md:text-4xl {{ $cms['fontFamily'] }}" style="{{ $cms['headingStyle'] }}">
                    {!! $section->title !!}
                </h2>
            </div>
        @endif
        <div class="prose max-w-none" style="{{ $cms['bodyStyle'] }}">
            {!! $html !!}
        </div>
    </div>
</section>
