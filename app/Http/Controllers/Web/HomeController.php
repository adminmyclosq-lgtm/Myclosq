<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Models\Media;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $page = CmsPage::with('sections')->where('slug','home')->where('status','published')->first();
        $products = Product::with(['productImages.media','variants.prices'])
            ->where('status','active')->where('is_featured',true)->orderBy('sort_order')->limit(4)->get();
        $heroContent = $page?->sections->firstWhere('section_type', 'hero')?->content;
        $heroContent = is_string($heroContent) ? json_decode($heroContent, true) : [];
        $headerLogo = !empty($heroContent['header_logo_media_id'] ?? null)
            ? Media::find($heroContent['header_logo_media_id'])
            : null;

        return view('home', compact('page', 'products', 'headerLogo'));
    }

    public function cmsPage($slug)
    {
        $page = CmsPage::with('sections')->where('slug', $slug)->first();
        if (!$page) {
            abort(404);
        }
        
        // We will render home.blade.php layout which is already dynamic for sections
        return view('home', compact('page'));
    }
}
