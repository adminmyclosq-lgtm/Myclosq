<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use Illuminate\Database\Seeder;

class LearnPageSeeder extends Seeder
{
    public function run()
    {
        $page = CmsPage::firstOrCreate(
            ['slug' => 'learn'],
            [
                'title' => 'Learn',
                'page_type' => 'standard',
                'status' => 'published',
            ]
        );

        $page->sections()->delete(); // Clear old ones

        $sections = [
            [
                'section_type' => 'learn_hero',
                'title' => 'Understand your gut — and judge a product trial more clearly.',
                'subtitle' => 'Practical guidance for observing meaningful signals from ordinary day-to-day variation, giving a product a fair trial, and deciding what to do next.',
                'content' => [
                    'button_1' => 'Product Guides',
                    'button_1_url' => '/how-it-works',
                    'button_2' => 'See Our Standards',
                    'button_2_url' => '/our-standards',
                    'image' => 'assets/hero-product-CftTmpl1.jpg'
                ]
            ],
            [
                'section_type' => 'learn_start_understanding',
                'title' => 'Start with what you are trying to understand.',
                'content' => [
                    'cards' => [
                        ['title' => 'Understand everyday gut patterns', 'text' => 'Why digestion can feel different from one day to the next.', 'btn' => 'Read the guide →', 'url' => '#'],
                        ['title' => 'Judge a product trial', 'text' => 'How to give a product a fair chance.', 'btn' => 'Read the guide →', 'url' => '#'],
                        ['title' => 'Know when guidance is needed', 'text' => 'What self-observation is meant for — and where it ends.', 'btn' => 'Read the guide →', 'url' => '#']
                    ]
                ]
            ],
            [
                'section_type' => 'learn_product_trials',
                'title' => 'Why supplements are easy to start — and difficult to judge.',
                'subtitle' => 'Taking the first capsule is simple. Interpreting what happened is harder. Starting conditions, variables, observation and drift — the context all influences when a product trial leads to a useful conclusion.',
                'content' => [
                    'badge' => 'Product Trials',
                    'button_1' => 'Read the guide →',
                    'button_1_url' => '#',
                    'image' => 'assets/bottle-capsules-COsDFlLa.jpg'
                ]
            ],
            [
                'section_type' => 'learn_guides_grid',
                'title' => '',
                'content' => [
                    'cards' => [
                        ['tag' => 'Trial Design', 'title' => 'What a fair 30-day product trial looks like', 'btn' => 'Read the guide →', 'url' => '#', 'image' => 'assets/hero-product-CftTmpl1.jpg'],
                        ['tag' => 'Observation', 'title' => 'Why one good day — or one difficult day — does not prove much', 'btn' => 'Read the guide →', 'url' => '#', 'image' => 'assets/bottle-capsules-COsDFlLa.jpg'],
                        ['tag' => 'Context', 'title' => 'How meals, sleep, stress and travel affect what you notice', 'btn' => 'Read the guide →', 'url' => '#', 'image' => 'assets/course-kit-DtVPc2tT.jpg'],
                        ['tag' => 'Reporting', 'title' => 'What the Gut Response Brief can — and cannot — tell you', 'btn' => 'Read the guide →', 'url' => '#', 'image' => 'assets/hero-product-CftTmpl1.jpg'],
                        ['tag' => 'Practicality', 'title' => 'How to use a daily supplement without building your life around it', 'btn' => 'Read the guide →', 'url' => '#', 'image' => 'assets/bottle-capsules-COsDFlLa.jpg'],
                        ['tag' => 'Safety', 'title' => 'When digestive symptoms need medical attention', 'btn' => 'Read the guide →', 'url' => '#', 'image' => 'assets/course-kit-DtVPc2tT.jpg']
                    ]
                ]
            ],
            [
                'section_type' => 'learn_language_clarity',
                'title' => 'An observation is not the same as a conclusion.',
                'subtitle' => 'We use precise language to help differentiate between immediate changes and established long-term fits.',
                'content' => [
                    'button_1' => 'Review Our Standards',
                    'button_1_url' => '/our-standards',
                    'cards' => [
                        ['badge' => 'Reported', 'text' => 'An experience or observation shared by the user during the course.'],
                        ['badge' => 'Observed During the Course', 'text' => 'A change that plausibly reflects a signal, not a whole outcome.'],
                        ['badge' => 'Possible Pattern', 'text' => 'Emerging response signal that requires more time to confirm.'],
                        ['badge' => 'Not Established', 'text' => 'Signals that need external evaluation to become a conclusion.']
                    ]
                ]
            ],
            [
                'section_type' => 'learn_cta',
                'title' => 'A product trial should lead to a clearer decision.',
                'subtitle' => 'The 30-Day Gut Reset combines the daily capsule, a few guided moments and a personal Gut Response Brief.',
                'content' => [
                    'badge' => 'Ready when you are',
                    'button_1' => 'See How It Works',
                    'button_1_url' => '/how-it-works',
                    'button_2' => 'View the 30-Day Gut Reset',
                    'button_2_url' => '/shop'
                ]
            ],
            [
                'section_type' => 'learn_footer_note',
                'content' => [
                    'text' => 'Some symptoms should not wait for a product trial.',
                    'links' => [
                        ['title' => 'Review Safety & Suitability', 'url' => '/our-standards'],
                        ['title' => 'Visit Support', 'url' => '/support']
                    ]
                ]
            ]
        ];

        foreach ($sections as $index => $section) {
            $page->sections()->create([
                'section_type' => $section['section_type'],
                'title' => $section['title'] ?? '',
                'subtitle' => $section['subtitle'] ?? '',
                'sort_order' => ($index + 1) * 10,
                'content' => json_encode(array_merge($section['content'] ?? [], ['settings' => []]))
            ]);
        }
    }
}
