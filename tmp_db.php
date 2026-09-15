<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$sections = \DB::table('cms_sections')->where('page_id', 4)->orderBy('sort_order')->get();
foreach($sections as $s) {
    echo "ID: $s->id | Type: $s->section_type | Order: $s->sort_order\n";
}
