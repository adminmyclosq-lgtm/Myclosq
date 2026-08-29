<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$page = \App\Models\CmPage::where('slug', 'home')->first();
if (!$page) die("No home page");

// Clear existing sections to avoid duplicates while we fix the exact setup
$page->sections()->delete();

// 1. Hero
$page->sections()->create([
    'section_type' => 'hero',
    'title' => 'Take it daily.<br>Check in lightly.<br>Know what changed.',
    'subtitle' => 'A 30-day guided gut-support capsule course. Take one capsule daily, complete a few short course moments, and receive a personal Gut Response Brief showing what changed and what to do next.',
    'sort_order' => 10,
    'content' => json_encode([
        'badge' => '30-Day Guided Gut Reset',
        'button_1' => 'Explore the Reset',
        'button_2' => 'Check Your Fit',
        'settings' => ['top_spacing'=>'', 'bottom_spacing'=>'', 'bg_color'=>'']
    ])
]);

// 2. Problem
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
        'settings' => ['top_spacing'=>'', 'bottom_spacing'=>'']
    ])
]);

// 3. Features (How it works)
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
        'settings' => ['top_spacing'=>'', 'bottom_spacing'=>'']
    ])
]);

// 4. Standards
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
        'settings' => ['top_spacing'=>'', 'bottom_spacing'=>'']
    ])
]);

// 5. Showcase 
$page->sections()->create([
    'section_type' => 'showcase',
    'title' => 'Everything needed to start correctly and return easily.',
    'subtitle' => 'Take 30. Spend 15. Know your gut.',
    'sort_order' => 50,
    'content' => json_encode([
        'badge' => 'What you receive',
        'settings' => ['top_spacing'=>'', 'bottom_spacing'=>'']
    ])
]);

// 6. Testimonials (Brief)
$page->sections()->create([
    'section_type' => 'testimonials',
    'title' => 'Your Gut Response Brief at Day 30.',
    'subtitle' => 'At the end of the 30 days you receive a personal Gut Response Brief. It is the read after your capsule course and a few short course moments.',
    'sort_order' => 60,
    'content' => json_encode([
        'badge' => 'Day 30',
        'settings' => ['top_spacing'=>'', 'bottom_spacing'=>'']
    ])
]);

// 7. Feature Cards (Fit)
$page->sections()->create([
    'section_type' => 'feature_cards',
    'title' => 'Is this likely to be right for you?',
    'sort_order' => 70,
    'content' => json_encode([
        'badge' => 'Check your fit',
        'settings' => ['top_spacing'=>'', 'bottom_spacing'=>'']
    ])
]);

// 8. Learn
$page->sections()->create([
    'section_type' => 'learn',
    'title' => 'Understand your gut — and how to judge a trial.',
    'sort_order' => 80,
    'content' => json_encode([
        'badge' => 'Learn',
        'cards' => [
            ['title' => 'Methodology', 'text' => 'Why supplements are easy to start and difficult to judge.'],
            ['title' => 'Medical Context', 'text' => 'What digestive symptoms need medical attention.'],
            ['title' => 'Trial Design', 'text' => 'What a fair 30-day product trial looks like.']
        ],
        'settings' => ['top_spacing'=>'', 'bottom_spacing'=>'']
    ])
]);

// 9. FAQ
$page->sections()->create([
    'section_type' => 'faq',
    'title' => 'Questions you might have.',
    'sort_order' => 90,
    'content' => json_encode([
        'badge' => 'Questions',
        'settings' => ['top_spacing'=>'', 'bottom_spacing'=>'']
    ])
]);

// 10. CTA 
$page->sections()->create([
    'section_type' => 'cta',
    'title' => 'Give the capsule a fair 30-day trial.',
    'subtitle' => 'Start the course and receive your Gut Response Brief at Day 30.',
    'sort_order' => 100,
    'content' => json_encode([
        'badge' => 'Start the course',
        'settings' => ['top_spacing'=>'', 'bottom_spacing'=>'']
    ])
]);

echo "Re-seeded exact matched sections!\n";
