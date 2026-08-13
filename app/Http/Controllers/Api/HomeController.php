<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $home = CmsPage::with('sections')->where('slug', 'home')->where('status', 'published')->first();

        return response()->json([
            'page' => $home,
            'featured_products' => Product::with(['productImages.media','variants.prices'])
                ->where('status', 'active')
                ->where('is_featured', true)
                ->orderBy('sort_order')
                ->limit(8)
                ->get(),
        ]);
    }
}
