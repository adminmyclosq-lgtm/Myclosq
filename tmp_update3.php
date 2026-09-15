<?php
$file = __DIR__ . '/resources/views/learn.blade.php';
$content = file_get_contents($file);

// Replace section attributes
$content = preg_replace('/<section class="([^"]+)">/', '<section class="$1 {{ $bgClass }}" style="{{ $styleAttr }}">', $content);

// Replace h1, h2, h3
$content = preg_replace('/<h1 class="([^"]+font-serif[^"]*)">/', '<h1 class="$1 {{ $titleFont }} {{ $titleSize }}" style="{{ $titleColor }}">', $content);
$content = preg_replace('/<h2([^>]*)class="([^"]*font-serif[^"]*)"([^>]*)>/', '<h2$1class="$2 {{ $titleFont }} {{ $titleSize }}"$3 style="{{ $titleColor }}">', $content);
$content = preg_replace('/<h3 class="([^"]+font-serif[^"]*)">/', '<h3 class="$1 {{ $titleFont }} {{ $titleSize }}" style="{{ $titleColor }}">', $content);

// Replace paragraph texts
$content = preg_replace('/<p([^>]*)class="([^"]*text-\[15px\][^"]*)"([^>]*)>/', '<p$1class="$2 {{ $bodyFont }} {{ $bodySize }}"$3 style="{{ $bodyColor }}">', $content);

file_put_contents($file, $content);
echo "Replaced HTML attributes successfully.\n";
