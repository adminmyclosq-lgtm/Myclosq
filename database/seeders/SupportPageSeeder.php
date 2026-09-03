<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use Illuminate\Database\Seeder;

class SupportPageSeeder extends Seeder
{
    public function run()
    {
        $page = CmsPage::firstOrCreate(
            ['slug' => 'support'],
            [
                'title' => 'Support',
                'page_type' => 'standard',
                'status' => 'published',
            ]
        );

        $page->sections()->delete(); // Clear old ones

        $sections = [
            [
                'section_type' => 'support_hero',
                'title' => 'How can we help?',
                'subtitle' => 'Get help with your order, product, account or course activation.',
                'content' => [
                    'cards' => ['Track my order', 'Ask a product question', 'Suitability question', 'Something else']
                ]
            ],
            [
                'section_type' => 'support_purchase_origin',
                'title' => 'Where did you purchase the product?',
                'content' => [
                    'col1_title' => 'Guided Wellness Website',
                    'col1_text' => 'Order details, status, invoice, returns and refunds are all supported here.',
                    'col1_btn' => 'Get help with your order',
                    'col1_btn_url' => '#',
                    'col2_title' => 'Amazon Purchase',
                    'col2_text' => 'Guidance for order, delivery or return questions from Amazon. Product questions still route to us.',
                    'col2_btn' => 'Get Amazon Help',
                    'col2_btn_url' => '#'
                ]
            ],
            [
                'section_type' => 'support_topics',
                'title' => 'Choose what you need help with.',
                'content' => [
                    'topics' => [
                        ['title' => 'Order & Delivery', 'items' => ['Delayed order', 'Missing item', 'Damaged package']],
                        ['title' => 'Product Questions', 'items' => ['Ingredient list', 'Suitability check', 'Dosage']],
                        ['title' => 'Course Activation', 'items' => ['QR not scanning', 'Change my email', 'Restart course']],
                        ['title' => 'Amazon Support', 'items' => ['Order enquiry', 'Return via Amazon', 'Amazon refunds']]
                    ]
                ]
            ],
            [
                'section_type' => 'support_course_start',
                'title' => 'Having trouble starting your course?',
                'content' => [
                    'cards' => [
                        ['title' => 'Scan the QR', 'text' => 'Point your camera at the QR on the bottle.'],
                        ['title' => 'Set Day 0', 'text' => 'Around 2-3 minutes to complete.'],
                        ['title' => 'Begin daily', 'text' => 'Take your first capsule from Day 1.']
                    ],
                    'button_1' => 'Activate Course',
                    'button_1_url' => '/how-it-works',
                    'button_2' => 'Contact Us',
                    'button_2_url' => '#'
                ]
            ],
            [
                'section_type' => 'support_concerns',
                'title' => 'Concerned about the product\nor how you are feeling?',
                'content' => [
                    'col1_badge' => 'Report a product concern',
                    'col1_text' => 'If you are experiencing an issue with the product itself (such as packaging defects, unexpected smell, or other manufacturing-related concerns), please let us know.',
                    'col1_btn' => 'Report Now',
                    'col1_btn_url' => '#',
                    'col2_badge' => 'Seek professional guidance first',
                    'col2_text' => 'If you have severe or new symptoms, contact a healthcare professional before continuing. Supplements are not a replacement for medical care.',
                    'col2_btn' => 'Read Safety Standards',
                    'col2_btn_url' => '/our-standards'
                ]
            ],
            [
                'section_type' => 'support_contact_methods',
                'content' => [
                    'cards' => [
                        ['title' => 'WhatsApp Support', 'text' => 'Send us a quick message directly from your phone and our team will reply as soon as possible.', 'btn' => 'Open WhatsApp', 'url' => '#'],
                        ['title' => 'Email Support', 'text' => 'For non-urgent questions, documentation, or detailed inquiries about your course formulation.', 'btn' => 'Send Email', 'url' => '#'],
                        ['title' => 'Contact Form', 'text' => 'Share a detailed message through our secure portal to ensure all correct context is provided.', 'btn' => 'Open Form', 'url' => '#']
                    ]
                ]
            ],
            [
                'section_type' => 'support_details_ready',
                'title' => 'Keep these details ready.',
                'subtitle' => 'To help us assist you faster, please have this information on hand before reaching out:',
                'content' => [
                    'items' => [
                        ['label' => 'Order number', 'hint' => ''],
                        ['label' => 'Purchase channel', 'hint' => '(site or Amazon)'],
                        ['label' => 'Batch number', 'hint' => '(bottom of bottle)'],
                        ['label' => 'Delivery date', 'hint' => '']
                    ]
                ]
            ],
            [
                'section_type' => 'support_faq',
                'title' => 'Common support questions.',
                'content' => [
                    'items' => [
                        ['q' => 'How can I track my order?', 'a' => 'Once your order ships, you will receive a tracking link by email. You can also view your order status by logging into your account dashboard.'],
                        ['q' => 'How do I cancel my order?', 'a' => 'You can cancel your order within the first 2 hours of placement. Navigate to your recent orders in your account settings and select "Cancel".'],
                        ['q' => 'How do I activate my course?', 'a' => 'Simply scan the QR code located on the top of your product bottle using your smartphone camera. This will take you to your personal Day 0 setup page.'],
                        ['q' => 'When are refunds processed?', 'a' => 'Refunds are typically processed within 5-7 business days of an approved return reaching our facility. Your bank may take an additional 3-5 days to post the credit.'],
                        ['q' => 'How do I use and store the product?', 'a' => 'Store in a cool, dark place out of direct sunlight. Do not keep in the bathroom where humidity fluctuates. Take exactly as directed on your personalized portal.'],
                        ['q' => 'What is your return and refund policy?', 'a' => 'We offer a 30-day return policy for unopened items in their original packaging. Please contact support to initiate a return request with your order details.'],
                        ['q' => 'I bought this from Amazon, can you help?', 'a' => 'We can answer all product and course-related questions. However, for issues regarding delivery, missing packages, or returns, you must go through Amazon\'s customer service portal.'],
                        ['q' => 'I have a concern about the packaging?', 'a' => 'Please take a photo of the defect along with the batch number on the bottom of the bottle, and email it to our support team so we can investigate.'],
                        ['q' => 'Should I consult a doctor before starting?', 'a' => 'Yes. We always advise checking with a qualified healthcare provider before starting any new supplement regimen, especially if you have known medical conditions or take prescription medication.']
                    ]
                ]
            ],
            [
                'section_type' => 'support_auth_cta',
                'title' => 'Already in your 30-day course?',
                'subtitle' => 'Log in for specific help based on your current product, course day and journey state.',
                'content' => [
                    'button' => 'Log in for help',
                    'button_url' => '/login'
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
