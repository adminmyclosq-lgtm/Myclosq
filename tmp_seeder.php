<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$page = \App\Models\CmsPage::where('slug', 'home')->first();
if (!$page) die("No home page");

$hero = $page->sections()->where('section_type', 'hero')->first();
if (!$hero) {
    $page->sections()->create([
        'section_type' => 'hero',
        'title' => 'Take it daily.<br>Check in lightly.<br>Know what changed.',
        'subtitle' => 'A 30-day guided gut-support capsule course. Take one capsule daily, complete a few short course moments, and receive a personal Gut Response Brief showing what changed and what to do next.',
        'sort_order' => 10,
        'content' => json_encode(['button_1' => 'Explore the Reset', 'button_2' => 'Check Your Fit', 'badge' => '30-Day Guided Gut Reset'])
    ]);
    echo "Seeded!";
} else {
    echo "Already exists!";
}
