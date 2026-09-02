<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use Illuminate\Database\Seeder;

class HomePageSeeder extends Seeder
{
    public function run()
    {
        $page = CmsPage::firstOrCreate(
            ['slug' => 'home'],
            [
                'title' => 'Home',
                'page_type' => 'standard',
                'status' => 'published',
            ]
        );

        $page->sections()->delete();

        $sections = [
            [
                'section_type' => 'hero',
                'title' => 'Take it daily.<br>Check in lightly.<br>Know what changed.',
                'subtitle' => 'A 30-day guided gut-support capsule course. Take one capsule daily, complete a few short course moments, and receive a personal Gut Response Brief showing what changed and what to do next.',
                'content' => [
                    'eyebrow' => '30-Day Guided Gut Reset',
                    'primary_button_text' => 'Explore the Reset',
                    'primary_button_url' => '/shop',
                    'primary_button_size' => 'btn-lg',
                    'secondary_button_text' => 'Check Your Fit',
                    'secondary_button_url' => '#fit',
                    'secondary_button_size' => 'btn-lg',
                    'settings' => ['hidden' => false]
                ],
                'sort_order' => 10,
            ],
            [
                'section_type' => 'problem',
                'title' => 'Most gut resets leave you guessing.',
                'subtitle' => 'We replace confusion with a clear read on what actually happened.',
                'content' => [
                    'badge' => 'The Problem',
                    'cards' => [
                        ['title' => 'No clear starting point'],
                        ['title' => 'Real-life disruptions get read as failure'],
                        ['title' => 'The most recent moment becomes the impression']
                    ]
                ],
                'sort_order' => 20,
            ],
            [
                'section_type' => 'features',
                'title' => 'The capsule creates the response.<br>The guided course helps make it readable.',
                'content' => [
                    'badge' => 'Standout experience',
                    'cards' => [
                        ['title' => 'The daily capsule', 'text' => 'A gut-support supplement designed to support baseline gut health, taken once daily.'],
                        ['title' => 'The guided experience', 'text' => 'Eight components that guide you through a 30-day process, observing signals and coming to an honest read.']
                    ]
                ],
                'sort_order' => 30,
            ],
            [
                'section_type' => 'showcase',
                'title' => 'Everything needed to start correctly and return easily.',
                'subtitle' => "The Capsule Bottle — discreetly labelled, tightly designed for one capsule daily.\nCourse Companion — a concise activation card and course guide.\nGuided Course Access — permanent bottle QR to start or return to your guided course.",
                'content' => [
                    'eyebrow' => 'What you receive',
                    'primary_button_text' => 'Take 30. Spend 15. Know your gut.'
                ],
                'sort_order' => 40,
            ],
            [
                'section_type' => 'cta',
                'title' => 'Give the capsule a fair 30-day trial.',
                'subtitle' => 'Start the course and receive your Gut Response Brief at Day 30.',
                'content' => [
                    'eyebrow' => 'Start the course',
                    'primary_button_text' => 'Shop Gut Reset',
                    'primary_button_url' => '/shop',
                    'primary_button_size' => 'btn-lg',
                    'secondary_button_text' => 'Check Your Fit',
                    'secondary_button_url' => '#fit',
                    'secondary_button_size' => 'btn-lg',
                ],
                'sort_order' => 50,
            ]
        ];

        foreach ($sections as $section) {
            $page->sections()->create([
                'section_type' => $section['section_type'],
                'title' => $section['title'],
                'subtitle' => $section['subtitle'] ?? null,
                'content' => json_encode($section['content']),
                'sort_order' => $section['sort_order'],
            ]);
        }
    }
}
