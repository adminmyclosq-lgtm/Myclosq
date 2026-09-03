<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use Illuminate\Database\Seeder;

class HowItWorksPageSeeder extends Seeder
{
    public function run()
    {
        $page = CmsPage::firstOrCreate(
            ['slug' => 'how-it-works'],
            [
                'title' => 'How It Works',
                'page_type' => 'standard',
                'status' => 'published',
            ]
        );

        $page->sections()->delete(); // Clear old ones

        $sections = [
            [
                'section_type' => 'hiw_hero',
                'title' => 'One daily product.\nA few guided moments.\nA clearer decision at Day 30.',
                'subtitle' => 'The 30-day guided course records enough about your daily routine and reported response to make the end read honest.',
                'content' => [
                    'button_1' => 'View Product',
                    'button_1_url' => '/shop',
                    'button_2' => 'Review Standards',
                    'button_2_url' => '/#standards'
                ]
            ],
            [
                'section_type' => 'hiw_product_layer',
                'title' => 'The product creates the response.\nThe guided experience helps make it readable.',
                'content' => [
                    'col1_title' => 'The Product',
                    'col1_items' => ['Targeted daily product', '30 days of support', 'One daily moment'],
                    'col2_title' => 'The Guided Layer',
                    'col2_items' => ['Starting point', 'Course moments', 'Gut Response Brief']
                ]
            ],
            [
                'section_type' => 'hiw_timeline',
                'title' => 'A 30-Day Guided Journey',
                'subtitle' => '15 minutes total · 6 milestones',
                'content' => [
                    'cards' => [
                        ['day' => 'Before Day 1', 'label' => 'Starting Point'],
                        ['day' => 'Day 3', 'label' => 'Product Fit'],
                        ['day' => 'Day 7', 'label' => 'First-Week Signal'],
                        ['day' => 'Day 14', 'label' => 'Midpoint Pattern'],
                        ['day' => 'Day 21', 'label' => 'Final Stretch'],
                        ['day' => 'Day 30', 'label' => 'Gut Response Brief']
                    ]
                ]
            ],
            [
                'section_type' => 'hiw_cards_grid',
                'title' => 'Designed to fit real life.',
                'subtitle' => 'A gentle guided layer, sensitive to the ways real life interrupts.',
                'content' => [
                    'cards' => [
                        ['title' => 'Life Load', 'text' => 'Real-life demands are recorded as context, not failure.'],
                        ['title' => 'Credibility', 'text' => 'Every observation is stored honestly.'],
                        ['title' => 'Add Context', 'text' => 'A few short moments capture what matters.'],
                        ['title' => 'Return Ready', 'text' => 'Return to your course at any time via your product access.']
                    ]
                ]
            ],
            [
                'section_type' => 'hiw_cards_grid_flat',
                'title' => 'A missed day does not erase the journey.',
                'content' => [
                    'cards' => [
                        ['title' => 'Missed Product', 'text' => 'Recorded, then continued.'],
                        ['title' => 'Travel or Diet Change', 'text' => 'Captured as context.'],
                        ['title' => 'Missed Milestone', 'text' => 'Return to it. The milestone waits.'],
                        ['title' => 'Uneven Feeling', 'text' => 'Add a short note if it helps.']
                    ]
                ]
            ],
            [
                'section_type' => 'hiw_honest_read',
                'title' => 'Built for an honest read.',
                'content' => [
                    'cards' => [
                        'Anchoring on your starting intention.',
                        'Separating routine disruptions from response signals.',
                        'Weighing product exposure against reported response.',
                        'Preserving observations, not conclusions.',
                        'Offering the most relevant next step, not a score.'
                    ]
                ]
            ],
            [
                'section_type' => 'hiw_personal_brief',
                'title' => 'Your personal Gut Response Brief.',
                'subtitle' => 'A single honest read of the 30 days: what changed, what did not, the context around it, and the most relevant next step.',
                'content' => [
                    'tags' => ['Repeat', 'Maintenance', 'Try another', 'Stop', 'Doctor first']
                ]
            ],
            [
                'section_type' => 'hiw_physical_pack',
                'title' => 'The physical pack helps you start correctly.',
                'content' => [
                    'cards' => [
                        'Product-attached start cue',
                        'Course Companion card',
                        'Welcome Guide'
                    ]
                ]
            ],
            [
                'section_type' => 'hiw_help',
                'title' => 'Help is available when you need it.',
                'content' => [
                    'button' => 'Contact Us',
                    'button_url' => '/#faq',
                    'cards' => [
                        ['title' => 'WhatsApp', 'text' => 'Ask a question or share a concern.'],
                        ['title' => 'Email', 'text' => 'Reach us for account or product help.'],
                        ['title' => 'Guidance', 'text' => 'Speak to a doctor first if unsure.'],
                        ['title' => 'Support', 'text' => 'See our support page for common questions.']
                    ]
                ]
            ],
            [
                'section_type' => 'hiw_ready',
                'title' => 'Ready if relevant',
                'content' => [
                    'col1_badge' => 'Ready if relevant',
                    'col1_items' => ['Recurring gut discomfort', 'Looking for structure', 'Want to observe response'],
                    'col2_badge' => 'Speak to a doctor first',
                    'col2_items' => ['Pregnant or breast-feeding', 'Diagnosed condition', 'On prescription medication']
                ]
            ],
            [
                'section_type' => 'hiw_cta',
                'title' => 'Give the product a fair 30-day trial.',
                'content' => [
                    'button_1' => 'Shop',
                    'button_1_url' => '/shop',
                    'button_2' => 'Check Fit',
                    'button_2_url' => '/#fit'
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
