<?php
$files = glob(__DIR__ . '/resources/views/*.blade.php');
$componentFiles = glob(__DIR__ . '/resources/views/components/sections/*.blade.php');
$allFiles = array_merge($files, $componentFiles);

foreach($allFiles as $file) {
    if (basename($file) === 'app.blade.php') continue;
    
    $content = file_get_contents($file);
    if (!str_contains($content, "\$settings = \$contentData['settings'] ?? [];") && !str_contains($content, "\$settings = is_string(\$section->content)")) {
        continue;
    }
    
    $oldLogic = <<<'EOL'
            $btnPrimaryVar = !empty($settings['button_primary_color']) ? "--btn-primary-color: {$settings['button_primary_color']};" : "";
            $btnSecondaryVar = !empty($settings['button_secondary_color']) ? "--btn-secondary-color: {$settings['button_secondary_color']};" : "";
            
            $styleAttr = trim("$topStyle $bottomStyle $bgStyle $btnPrimaryVar $btnSecondaryVar");
EOL;

    $newLogic = <<<'EOL'
            $btnPrimaryVar = !empty($settings['button_primary_color']) ? "--btn-primary-color: {$settings['button_primary_color']};" : "";
            $btnPrimaryTextVar = !empty($settings['button_primary_text_color']) ? "--btn-primary-text-color: {$settings['button_primary_text_color']};" : "";
            $btnSecondaryVar = !empty($settings['button_secondary_color']) ? "--btn-secondary-color: {$settings['button_secondary_color']};" : "";
            $btnSecondaryTextVar = !empty($settings['button_secondary_text_color']) ? "--btn-secondary-text-color: {$settings['button_secondary_text_color']};" : "";
            
            $styleAttr = trim("$topStyle $bottomStyle $bgStyle $btnPrimaryVar $btnPrimaryTextVar $btnSecondaryVar $btnSecondaryTextVar");
EOL;

    if (str_contains($content, stripslashes($oldLogic))) {
        $content = str_replace(stripslashes($oldLogic), stripslashes($newLogic), $content);
        file_put_contents($file, $content);
        echo "Updated Logic Type 1 in " . basename($file) . "\n";
        continue;
    }

    // Now for components which use preg_replace on style attribute directly (from tmp_update2)
    // In components it was like:
    // {{ !empty($settings['button_primary_color']) ? '--btn-primary-color: '.$settings['button_primary_color'].';' : '' }} {{ !empty($settings['button_secondary_color']) ? '--btn-secondary-color: '.$settings['button_secondary_color'].';' : '' }}
    
    $oldComponentLogic = <<<'EOL'
{{ !empty($settings['button_primary_color']) ? '--btn-primary-color: '.$settings['button_primary_color'].';' : '' }} {{ !empty($settings['button_secondary_color']) ? '--btn-secondary-color: '.$settings['button_secondary_color'].';' : '' }}
EOL;
    $newComponentLogic = <<<'EOL'
{{ !empty($settings['button_primary_color']) ? '--btn-primary-color: '.$settings['button_primary_color'].';' : '' }} {{ !empty($settings['button_primary_text_color']) ? '--btn-primary-text-color: '.$settings['button_primary_text_color'].';' : '' }} {{ !empty($settings['button_secondary_color']) ? '--btn-secondary-color: '.$settings['button_secondary_color'].';' : '' }} {{ !empty($settings['button_secondary_text_color']) ? '--btn-secondary-text-color: '.$settings['button_secondary_text_color'].';' : '' }}
EOL;

    if (str_contains($content, stripslashes($oldComponentLogic))) {
        $content = str_replace(stripslashes($oldComponentLogic), stripslashes($newComponentLogic), $content);
        file_put_contents($file, $content);
        echo "Updated Logic Type 2 in " . basename($file) . "\n";
    }
}
echo "Done.\n";
