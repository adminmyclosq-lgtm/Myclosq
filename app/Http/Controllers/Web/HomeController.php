<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $page = CmsPage::with('sections')->where('slug','home')->where('status','published')->first();
        $products = Product::with(['productImages.media','variants.prices'])
            ->where('status','active')->where('is_featured',true)->orderBy('sort_order')->limit(4)->get();

        return view('home', compact('page','products'));
    }
}
