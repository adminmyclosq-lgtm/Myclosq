<?php
$files = glob(__DIR__ . '/resources/views/*.blade.php');

foreach($files as $file) {
    $content = file_get_contents($file);
    if (!str_contains($content, "\$settings = \$contentData['settings'] ?? [];")) {
        continue;
    }
    
    // In support.blade.php for instance
    $oldLogic = <<<'EOL'
        @php
            $contentData = is_string($section->content) ? json_decode($section->content, true) : ($section->content ?? []);
            if (!is_array($contentData)) $contentData = [];
            $settings = $contentData['settings'] ?? [];
            if (!empty($settings['hidden_desktop']) && !empty($settings['hidden_mobile'])) continue;
        @endphp
EOL;
    $newLogic = <<<'EOL'
        @php
            $contentData = is_string($section->content) ? json_decode($section->content, true) : ($section->content ?? []);
            if (!is_array($contentData)) $contentData = [];
            $settings = $contentData['settings'] ?? [];
            if (!empty($settings['hidden_desktop']) && !empty($settings['hidden_mobile'])) continue;
            
            $topStyle = isset($settings['top_spacing']) && $settings['top_spacing'] ? "padding-top: {$settings['top_spacing']};" : "";
            $bottomStyle = isset($settings['bottom_spacing']) && $settings['bottom_spacing'] ? "padding-bottom: {$settings['bottom_spacing']};" : "";
            $bgColor = $settings['bg_color'] ?? '';
            $bgClass = !str_starts_with($bgColor, '#') ? $bgColor : '';
            $bgStyle = str_starts_with($bgColor, '#') ? "background-color: $bgColor;" : '';
            
            $btnPrimaryVar = !empty($settings['button_primary_color']) ? "--btn-primary-color: {$settings['button_primary_color']};" : "";
            $btnSecondaryVar = !empty($settings['button_secondary_color']) ? "--btn-secondary-color: {$settings['button_secondary_color']};" : "";
            
            $styleAttr = trim("$topStyle $bottomStyle $bgStyle $btnPrimaryVar $btnSecondaryVar");
            
            $titleFont = $settings['title_font_family'] ?? 'font-serif';
            $titleSize = $settings['title_font_size'] ?? 'text-3xl md:text-4xl';
            $titleColor = !empty($settings['title_font_color']) ? "color: {$settings['title_font_color']};" : '';
            
            $bodyFont = $settings['body_font_family'] ?? '';
            $bodySize = $settings['body_font_size'] ?? 'text-[15px]';
            $bodyColor = !empty($settings['body_font_color']) ? "color: {$settings['body_font_color']};" : '';
        @endphp
EOL;

    if (str_contains($content, stripslashes($oldLogic))) {
        $content = str_replace(stripslashes($oldLogic), stripslashes($newLogic), $content);
    }
    
    // Replace html classes and styles
    $content = preg_replace('/<section([^>]*)class="([^"]+)"(.*?)>/', '<section$1class="$2 {{ $bgClass }}" style="{{ $styleAttr }}"$3>', $content);
    
    // h1, h2, h3
    $content = preg_replace('/<h1([^>]*)class="([^"]*font-serif[^"]*)"([^>]*)>/', '<h1$1class="$2 {{ $titleFont }} {{ $titleSize }}"$3 style="{{ $titleColor }}">', $content);
    $content = preg_replace('/<h2([^>]*)class="([^"]*font-serif[^"]*)"([^>]*)>/', '<h2$1class="$2 {{ $titleFont }} {{ $titleSize }}"$3 style="{{ $titleColor }}">', $content);
    $content = preg_replace('/<h3([^>]*)class="([^"]*font-serif[^"]*)"([^>]*)>/', '<h3$1class="$2 {{ $titleFont }} {{ $titleSize }}"$3 style="{{ $titleColor }}">', $content);
    
    // paragraphs texts
    $content = preg_replace('/<p([^>]*)class="([^"]*text-\[(14|15)px\][^"]*)"([^>]*)>/', '<p$1class="$2 {{ $bodyFont }} {{ $bodySize }}"$3 style="{{ $bodyColor }}">', $content);
    
    // Make sure we only add {{ $bgClass }} and style="{{ $styleAttr }}" ONCE per section
    $content = preg_replace('/\{\{ \$bgClass \}\}\s+\{\{ \$bgClass \}\}/', '{{ $bgClass }}', $content);
    $content = preg_replace('/style="\{\{ \$styleAttr \}\}"\s+style="\{\{ \$styleAttr \}\}"/', 'style="{{ $styleAttr }}"', $content);

    // Apply the btn colors to inline styles? No, they are set on the <section> via style="{{ $styleAttr }}" !
    // So the cascade will automatically make btn-primary and btn-secondary pick up the colors!

    file_put_contents($file, $content);
    echo "Processed " . basename($file) . "\n";
}
