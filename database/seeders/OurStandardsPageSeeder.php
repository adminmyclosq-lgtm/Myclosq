<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use Illuminate\Database\Seeder;

class OurStandardsPageSeeder extends Seeder
{
    public function run()
    {
        $page = CmsPage::firstOrCreate(
            ['slug' => 'our-standards'],
            [
                'title' => 'Our Standards',
                'page_type' => 'standard',
                'status' => 'published',
            ]
        );

        $page->sections()->delete(); // Clear old ones

        $sections = [
            [
                'section_type' => 'standards_hero',
                'title' => 'Standards you should be able to inspect.',
                'subtitle' => 'Before you buy, you should be able to understand what is in the product, why it is there, how quality is checked and where the limits of the product and guided experience are.',
                'content' => [
                    'button_1' => 'Explore Our Standards',
                    'button_1_url' => '/shop',
                    'button_2' => 'Review Safety & Suitability',
                    'button_2_url' => '/fit-check',
                    'cards' => [
                        ['num' => '01', 'title' => 'Transparent formulation', 'text' => 'Every ingredient is disclosed with its intended role and rationale.'],
                        ['num' => '02', 'title' => 'Verifiable quality', 'text' => 'How data and testing certificates are made accessible for independent verification.'],
                        ['num' => '03', 'title' => 'Responsible guidance', 'text' => 'Safety information is presented up-front to ensure our products are the right fit for you.']
                    ]
                ]
            ],
            [
                'section_type' => 'standards_formulation',
                'title' => 'Ingredients & Formulation',
                'subtitle' => 'Selected product: 30-Day Myclosq',
                'content' => []
            ],
            [
                'section_type' => 'standards_quality',
                'title' => 'Quality information should be specific — not implied.',
                'subtitle' => '"Premium Quality" is a marketing term. We replace it with specific testing frameworks and verifiable batch data recorded for every production run.',
                'content' => [
                    'cards' => [
                        ['title' => 'Ingredient Identity'],
                        ['title' => 'Purity Testing'],
                        ['title' => 'Declared Potency'],
                        ['title' => 'Microbiological Quality'],
                        ['title' => 'Packaging Integrity'],
                        ['title' => 'Batch Traceability']
                    ]
                ]
            ],
            [
                'section_type' => 'standards_observation',
                'title' => 'An observation is not the same as a conclusion.',
                'subtitle' => 'We distinguish between established facts and observations within our guided courses. These observations do not diagnose conditions or prove universal causation.',
                'content' => [
                    'tags' => ['Product Fact', 'Ingredient Rationale', 'Observed Within the Course', 'Possible Pattern', 'Not Established'],
                    'col1_title' => 'What this information helps with',
                    'col1_items' => [
                        'Understanding documented physiological support for specific bodily systems.',
                        'Providing biological context for how results are typically interpreted.',
                        'Setting realistic timeframes for biological adaptation.'
                    ],
                    'col2_title' => 'What it cannot establish',
                    'col2_items' => [
                        'Cures for diagnosed medical conditions or disease treatments.',
                        'Universal outcomes — biological individuality means responses will vary.',
                        'Results that override the need for foundational healthy lifestyle choices.'
                    ]
                ]
            ],
            [
                'section_type' => 'standards_safety',
                'title' => 'Safety and suitability are part of the product.',
                'subtitle' => 'We help you qualify yourself for our products before purchase. Review the criteria below.',
                'content' => [
                    'col1_title' => 'May be relevant',
                    'col1_items' => [
                        'Seeking structured gut-health support',
                        'Struggling with supplement consistency',
                        'Desiring data-driven personal health insights'
                    ],
                    'col2_title' => 'Speak to a doctor first',
                    'col2_items' => [
                        'If you are pregnant or breast-feeding',
                        'Diagnosed gastrointestinal condition',
                        'Taking prescription medication'
                    ],
                    'button' => 'Check Your Fit',
                    'button_url' => '/fit-check'
                ]
            ],
            [
                'section_type' => 'standards_review',
                'title' => 'Product information you can review.',
                'subtitle' => 'Document Unavailable',
                'content' => []
            ],
            [
                'section_type' => 'standards_cta',
                'title' => 'Understand the product before you begin.',
                'subtitle' => '',
                'content' => [
                    'button_1' => 'View the 30-Day Myclosq',
                    'button_1_url' => '/shop',
                    'button_2' => 'See How It Works',
                    'button_2_url' => '/how-it-works'
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
