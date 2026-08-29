<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$heroes = \App\Models\CmsSection::where('section_type', 'hero')->orderBy('sort_order')->get();
echo "Heroes count: " . $heroes->count() . "\n";
foreach($heroes as $h) {
    echo "ID: $h->id, Sort: $h->sort_order, Title: $h->title\n";
}
