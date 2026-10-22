@props(['buttons' => [], 'cms' => [], 'fallback' => null])
@php
    $btnList = !empty($buttons) && is_array($buttons) ? $buttons : [];
    // If no buttons array, check fallback
    if (empty($btnList) && !empty($fallback) && is_array($fallback)) {
        $btnList = $fallback;
    }
    $globalRadius = $cms['buttonBorderRadius'] ?? '12px';
    $globalFont = $cms['buttonFont'] ?? '';
    $globalBg = $cms['buttonBgColor'] ?? '';
    $globalText = $cms['buttonTextColor'] ?? '';
@endphp

@if(!empty($btnList))
    <div class="mt-8 flex flex-col sm:flex-row sm:flex-wrap items-stretch sm:items-center gap-3 w-full sm:w-auto">
        @foreach($btnList as $btn)
            @php
                $text = is_array($btn) ? ($btn['text'] ?? 'Button') : (string)$btn;
                $url = is_array($btn) ? ($btn['url'] ?? '#') : '#';
                $style = is_array($btn) ? ($btn['style'] ?? 'primary') : 'primary';
                $target = is_array($btn) ? ($btn['target'] ?? '_self') : '_self';
                $customBg = is_array($btn) ? ($btn['bg_color'] ?? '') : '';
                $customText = is_array($btn) ? ($btn['text_color'] ?? '') : '';

                // Inline styles
                $btnStyles = [];
                if (!empty($globalRadius)) {
                    $btnStyles[] = "border-radius: {$globalRadius};";
                }
                if (!empty($customBg)) {
                    $btnStyles[] = "background-color: {$customBg} !important;";
                    $btnStyles[] = "border-color: {$customBg} !important;";
                } elseif (!empty($globalBg) && $style === 'primary') {
                    $btnStyles[] = "background-color: {$globalBg} !important;";
                    $btnStyles[] = "border-color: {$globalBg} !important;";
                }
                if (!empty($customText)) {
                    $btnStyles[] = "color: {$customText} !important;";
                } elseif (!empty($globalText) && $style === 'primary') {
                    $btnStyles[] = "color: {$globalText} !important;";
                }

                $styleClass = match($style) {
                    'secondary' => 'btn-secondary',
                    'outline' => 'inline-flex items-center justify-center px-6 py-3 border-2 border-current text-sm font-semibold transition hover:opacity-80',
                    'ghost' => 'inline-flex items-center justify-center px-4 py-2 text-sm font-semibold underline underline-offset-4 hover:opacity-80',
                    default => 'btn-primary',
                };
            @endphp
            @if(!empty(trim($text)))
                <a href="{{ $url }}"
                   target="{{ $target }}"
                   class="{{ $styleClass }} {{ $globalFont }} w-full sm:w-auto text-center justify-center"
                   style="{{ implode(' ', $btnStyles) }}">
                    {{ $text }}
                </a>
            @endif
        @endforeach
    </div>
@endif
