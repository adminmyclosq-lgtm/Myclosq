<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use Illuminate\Database\Seeder;

class ProductPageContentSeeder extends Seeder
{
    public function run()
    {
        $page = CmsPage::firstOrCreate(
            ['slug' => 'product-page-content'],
            [
                'title' => 'Product Page CMS Content',
                'page_type' => 'standard',
                'status' => 'published',
            ]
        );

        $page->sections()->delete();

        $sections = [
            [
                'section_type' => 'product_arrives',
                'title' => 'What arrives with your 30-day experience.',
                'content' => [
                    'cards' => [
                        ['title' => 'The Product', 'text' => 'A tightly sized 30-day supply, designed for one simple daily moment.'],
                        ['title' => 'Course Companion', 'text' => 'A concise guide covering activation, missed days, safety, and support.'],
                        ['title' => 'Permanent Course Access', 'text' => 'Start, resume, or return to your guided course whenever you need.'],
                        ['title' => 'Welcome Guide', 'text' => 'How to begin, what to expect, and how to read your response.']
                    ]
                ]
            ],
            [
                'section_type' => 'product_creates_response',
                'title' => 'The product creates the response.<br>The guided course makes it readable.',
                'content' => [
                    'cards' => [
                        ['badge' => 'The Daily Product', 'items' => ['A consistent daily routine', 'Designed for 30 days of support', 'Clear use and storage guidance']],
                        ['badge' => 'The Guided Experience', 'items' => ['A few short course moments', 'Around 15 minutes in total', 'A personal response brief at Day 30']]
                    ],
                    'button' => 'See how it works',
                    'button_url' => '/#how-it-works'
                ]
            ],
            [
                'section_type' => 'product_routine',
                'title' => 'A simple routine designed for real life.',
                'content' => [
                    'cards' => [
                        ['num' => '01', 'title' => 'Use it daily', 'text' => 'Follow the instructions on your product with water.'],
                        ['num' => '02', 'title' => 'A few course moments', 'text' => 'Short check-ins keep the experience light and useful.'],
                        ['num' => '03', 'title' => 'Complete 30 days', 'text' => 'Real-life disruptions are useful context, not failure.'],
                        ['num' => '04', 'title' => 'Receive your brief', 'text' => 'Get an honest read and a relevant next step at Day 30.']
                    ]
                ]
            ],
            [
                'section_type' => 'product_receive_day30',
                'title' => 'What you receive at Day 30',
                'subtitle' => 'The Gut Response Brief brings together your product routine, course moments, and real-life context into one useful, honest read.',
                'content' => [
                    'items' => ['Observations, not conclusions', 'A recommended next step'],
                    'button' => 'See a sample',
                    'button_url' => '/#how-it-works'
                ]
            ],
            [
                'section_type' => 'product_inspect',
                'title' => 'Product information you should be able to inspect.',
                'content' => [
                    'cards' => [
                        ['title' => 'Ingredients & Form', 'text' => 'Ingredients and format are disclosed clearly.'],
                        ['title' => 'Suitability Notes', 'text' => 'Review safety and fit before starting.'],
                        ['title' => 'Usage & Storage', 'text' => 'Follow the label and store in a cool, dry place.']
                    ]
                ]
            ],
            [
                'section_type' => 'product_check_fit',
                'title' => 'Check whether this product is likely to be right for you.',
                'content' => [
                    'col1_badge' => 'May fit',
                    'col1_items' => ['Looking for a structured daily routine', 'Want to observe your response over time', 'Value a clear, lighter process'],
                    'col2_badge' => 'Doctor first',
                    'col2_items' => ['Pregnant or breast-feeding', 'Diagnosed condition or new severe symptoms', 'Taking prescription medication', 'Under 18']
                ]
            ],
            [
                'section_type' => 'product_faq',
                'title' => 'Frequently Asked Questions',
                'content' => [
                    'items' => [
                        ['q' => 'What is included with my order?', 'a' => 'Your product, a Course Companion, and access to the guided 30-day experience.'],
                        ['q' => 'How do I access the guided experience?', 'a' => 'Use the course access details included with your order to begin, resume, or return.'],
                        ['q' => 'What if I miss a day?', 'a' => 'Continue when you can. A missed day is part of the context considered in your experience.'],
                        ['q' => 'What happens at Day 30?', 'a' => 'You receive a Gut Response Brief that summarises what you observed and suggests a next step.']
                    ]
                ]
            ],
            [
                'section_type' => 'product_cta',
                'title' => 'Ready to give it a fair 30-day trial?',
                'subtitle' => 'Start the course and receive a Gut Response Brief at Day 30.',
                'content' => [
                    'button_2' => 'Check Fit',
                    'button_2_url' => '#fit'
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
