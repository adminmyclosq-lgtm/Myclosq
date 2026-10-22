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
        $page = CmsPage::with(['sections.media'])->where('slug', 'home')->where('status', 'published')->first();
        $products = Product::with(['productImages.media', 'variants.prices'])
            ->where('status', 'active')->where('is_featured', true)->orderBy('sort_order')->limit(4)->get();
        $heroContent = $page?->sections->firstWhere('section_type', 'hero')?->content;
        $heroContent = is_string($heroContent) ? json_decode($heroContent, true) : [];
        $headerLogo = !empty($heroContent['header_logo_media_id'] ?? null)
            ? Media::find($heroContent['header_logo_media_id'])
            : null;

        return view('home', compact('page', 'products', 'headerLogo'));
    }

    public function howItWorks()
    {
        $page = CmsPage::with(['sections.media'])->where('slug', 'how-it-works')->first();
        return view('how-it-works', compact('page'));
    }

    public function showPage(string $slug)
    {
        $page = CmsPage::with(['sections.media'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return view('cms-page', compact('page'));
    }
}
