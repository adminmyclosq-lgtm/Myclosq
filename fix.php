<?php
$f = '/var/www/html/resources/views/shop/show.blade.php';
$content = file_get_contents($f);
$content = str_replace('{{ $styleAttr }}', '{{ $styleAttr ?? \'\' }}', $content);
file_put_contents($f, $content);
echo "FIXED\n";
