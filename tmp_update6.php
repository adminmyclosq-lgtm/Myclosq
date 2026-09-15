<?php
$files = glob(__DIR__ . '/resources/views/*.blade.php');

$setupLogic = <<<'EOL'
            $settings = $contentData['settings'] ?? [];
            if (!empty($settings['hidden_desktop']) && !empty($settings['hidden_mobile'])) continue;
            
            $imageUrl = $section->media ? ($section->media->storage_path ? asset('storage/'.$section->media->storage_path) : $section->media->url) : '';

            $topSpacing = $settings['top_spacing'] ?? '0px';
            $bottomSpacing = $settings['bottom_spacing'] ?? '0px';
            $topStyle = $topSpacing !== '0px' ? "padding-top: $topSpacing;" : "";
            $bottomStyle = $bottomSpacing !== '0px' ? "padding-bottom: $bottomSpacing;" : "";
            
            $bgColor = $settings['bg_color'] ?? '';
            $bgClass = $bgColor && str_starts_with($bgColor, 'bg-') ? $bgColor : '';
            $bgStyle = $bgColor && !str_starts_with($bgColor, 'bg-') ? "background-color: $bgColor;" : "";

            $btnPrimaryVar = !empty($settings['button_primary_color']) ? "--btn-primary-color: {$settings['button_primary_color']};" : "";
            $btnPrimaryTextVar = !empty($settings['button_primary_text_color']) ? "--btn-primary-text-color: {$settings['button_primary_text_color']};" : "";
            $btnSecondaryVar = !empty($settings['button_secondary_color']) ? "--btn-secondary-color: {$settings['button_secondary_color']};" : "";
            $btnSecondaryTextVar = !empty($settings['button_secondary_text_color']) ? "--btn-secondary-text-color: {$settings['button_secondary_text_color']};" : "";
            
            $styleAttr = trim("$topStyle $bottomStyle $bgStyle $btnPrimaryVar $btnPrimaryTextVar $btnSecondaryVar $btnSecondaryTextVar");

            $titleFont = isset($settings['title_font_family']) ? $settings['title_font_family'] : '';
            $titleSize = isset($settings['title_font_size']) ? $settings['title_font_size'] : '';
            $titleColor = isset($settings['title_font_color']) && $settings['title_font_color'] ? "color: {$settings['title_font_color']};" : '';

            $bodyFont = isset($settings['body_font_family']) ? $settings['body_font_family'] : '';
            $bodySize = isset($settings['body_font_size']) ? $settings['body_font_size'] : '';
            $bodyColor = isset($settings['body_font_color']) && $settings['body_font_color'] ? "color: {$settings['body_font_color']};" : '';
EOL;

foreach($files as $file) {
    if (in_array(basename($file), ['app.blade.php', 'home.blade.php'])) continue;
    $content = file_get_contents($file);
    
    // Check if the file has the standard settings block
    if (preg_match('/\$settings = \$contentData\[\'settings\'\] \?\? \[\];(.*?)\$imageUrl =/s', $content)) {
        // Replace everything from $settings = ... up to the start of @if($section->section_type
        $content = preg_replace('/\$settings = \$contentData\[\'settings\'\] \?\? \[\];.*?(?=@if\(\$section->section_type ===)/s', $setupLogic . "\n        @php\n        ", $content);
        
        // Remove duplicate @php opening if we accidentally messed it up
        $content = str_replace("@php\n        @php\n", "@php\n", $content);
        $content = str_replace("        @php\n        \n        @if", "        \n        @if", $content);
        
        file_put_contents($file, $content);
        echo "Updated setup block in " . basename($file) . "\n";
    }
}
echo "Done.\n";
