<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use Illuminate\Database\Seeder;

class OtherPagesSeeder extends Seeder
{
    public function run()
    {
        $pages = [
            'how-it-works' => 'How It Works',
            'product-page-content' => 'Product Page CMS Content',
            'learn' => 'Learn',
            'our-standards' => 'Our Standards',
            'support' => 'Support',
        ];

        foreach ($pages as $slug => $title) {
            $page = CmsPage::firstOrCreate(
                ['slug' => $slug],
                [
                    'title' => $title,
                    'page_type' => 'standard',
                    'status' => 'published',
                ]
            );

            // Add a placeholder section if none exist so it's editable in CMS
            if ($page->sections()->count() === 0) {
                $page->sections()->create([
                    'section_type' => 'hero',
                    'title' => $title,
                    'subtitle' => 'Content managed via CMS. Please edit sections in the dashboard.',
                    'content' => json_encode([
                        'eyebrow' => strtoupper($title),
                        'primary_button_text' => 'Learn More',
                        'primary_button_url' => '/',
                    ]),
                    'sort_order' => 10,
                ]);
            }
        }
    }
}
