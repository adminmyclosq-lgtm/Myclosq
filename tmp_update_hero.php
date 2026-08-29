<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$hero1 = \App\Models\CmsSection::where('id', 12)->first();
if ($hero1) {
    echo "Updating Hero 12 to use image layout\n";
    $content = json_decode($hero1->content, true);
    $content['layout'] = 'image';
    $hero1->content = json_encode($content);
    $hero1->save();
}

$hero2 = \App\Models\CmsSection::where('id', 24)->first();
if ($hero2) {
    echo "Updating Hero 24 to use box layout\n";
    $content = json_decode($hero2->content, true);
    $content['layout'] = 'box';
    $hero2->content = json_encode($content);
    $hero2->save();
}
