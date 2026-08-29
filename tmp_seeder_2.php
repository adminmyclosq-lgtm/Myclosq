<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$page = \App\Models\CmsPage::where('slug', 'home')->first();
if (!$page) die("No home page");

// 1. The Problem
if (!$page->sections()->where('section_type', 'problem')->exists()) {
    $page->sections()->create([
        'section_type' => 'problem',
        'title' => 'Most gut resets leave you guessing.',
        'subtitle' => 'A good day feels encouraging. A bad day feels like the capsule is not working. Without a fair 30-day trial, most decisions are made on impression.',
        'sort_order' => 20,
        'content' => json_encode([
            'badge' => 'The problem',
            'cards' => [
                'No clear starting point',
                'Real-life disruptions get read as failure',
                'The most recent moment becomes the impression'
            ],
            'settings' => ['top_spacing'=>'80px', 'bottom_spacing'=>'80px']
        ])
    ]);
}

// 2. Feature Cards (How it works)
if (!$page->sections()->where('section_type', 'features')->exists()) {
    $page->sections()->create([
        'section_type' => 'features',
        'title' => 'The capsule creates the response.<br>The guided course helps make it readable.',
        'subtitle' => '',
        'sort_order' => 30,
        'content' => json_encode([
            'badge' => 'Standout experience',
            'cards' => [
                ['title' => 'The daily capsule', 'text' => 'A gut-support supplement designed to support baseline gut health, taken once daily.'],
                ['title' => 'The guided experience', 'text' => 'Eight components that guide you through a 30-day process, observing signals and coming to an honest read.'],
                ['title' => 'Take it daily.', 'text' => 'Check in lightly. Know what changed.']
            ],
            'settings' => ['top_spacing'=>'80px', 'bottom_spacing'=>'80px']
        ])
    ]);
}

// 3. Standards
if (!$page->sections()->where('section_type', 'standards')->exists()) {
    $page->sections()->create([
        'section_type' => 'standards',
        'title' => 'Standards you should be able to inspect.',
        'subtitle' => 'Transparency is not a feature; it is the foundation of trust.',
        'sort_order' => 40,
        'content' => json_encode([
            'badge' => 'Transparency',
            'cards' => [
                ['num' => '01', 'title' => 'Formulation', 'text' => 'Every ingredient is disclosed with its intended role and rationale.'],
                ['num' => '02', 'title' => 'Quality', 'text' => 'How quality is tested and where the limits of testing are.'],
                ['num' => '03', 'title' => 'Guidance', 'text' => 'Safety information is presented up-front so the product is used well.']
            ],
            'settings' => ['top_spacing'=>'80px', 'bottom_spacing'=>'80px']
        ])
    ]);
}

echo "Seeded remaining sections!";
