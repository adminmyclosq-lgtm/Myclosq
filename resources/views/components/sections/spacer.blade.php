@props(['section' => null])
@php
    $cms = \App\Services\CmsSectionHelper::parse($section);
    $content = $cms['content'];
    $showLine = !empty($content['show_line']);
    $height = $content['height'] ?? '80px';
@endphp
<div id="{{ $cms['anchorId'] }}" class="{{ $cms['bgColor'] }} {{ $cms['visibility'] }}" style="{{ $cms['style'] }}; min-height: {{ $height }};">
    <div class="section {{ $cms['containerWidth'] }} h-full flex items-center justify-center">
        @if($showLine)
            <hr class="w-full border-t border-stone-200">
        @endif
    </div>
</div>
