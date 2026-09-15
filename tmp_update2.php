<?php
$files = glob(__DIR__ . '/resources/views/components/sections/*.blade.php');
foreach($files as $file) {
    if (basename($file) === 'app.blade.php') continue;
    $content = file_get_contents($file);
    // Replace the existing logic to inject BOTH primary and secondary
    $content = preg_replace_callback('/style="([^"]+)"/', function($m) {
        $style = $m[1];
        // Remove previous insertions to avoid duplicates
        $style = preg_replace('/\{\{ !empty\(\$settings\[\'button_primary_color\'\]\).*?\}\}/', '', $style);
        $style = preg_replace('/\{\{ !empty\(\$settings\[\'button_secondary_color\'\]\).*?\}\}/', '', $style);
        
        // Strip trailing spaces
        $style = trim($style);
        
        $additions = " {{ !empty(\$settings['button_primary_color']) ? '--btn-primary-color: '.\$settings['button_primary_color'].';' : '' }} {{ !empty(\$settings['button_secondary_color']) ? '--btn-secondary-color: '.\$settings['button_secondary_color'].';' : '' }}";
        
        return 'style="' . $style . $additions . '"';
    }, $content);
    file_put_contents($file, $content);
}
echo "Done replacing style attribute.\n";
