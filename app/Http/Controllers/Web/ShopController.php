<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['productImages.media','variants.prices'])
            ->where('status','active')
            ->when($request->search, fn($q,$v) => $q->where('name','like',"%{$v}%"))
            ->orderBy('sort_order')
            ->paginate(12);

        return view('shop.index', compact('products'));
    }

    public function show(Product $product)
    {
        abort_unless($product->status === 'active', 404);
        $product->load(['productImages.media','variants.prices','category']);
        return view('shop.show', compact('product'));
    }
}
