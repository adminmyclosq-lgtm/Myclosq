<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Http\Request::create('/admin/cms/4/sections', 'GET');
$response = $kernel->handle($request);
$html = $response->getContent();

if (str_contains($html, 'modal-content-111')) {
    echo "ID 111 IS IN THE HTML DOM.\n";
} else {
    echo "ID 111 IS MISSING FROM HTML DOM.\n";
}

preg_match_all('/data-id="(\d+)"/', $html, $matches);
echo "Rendered IDs: " . implode(', ', $matches[1]) . "\n";
