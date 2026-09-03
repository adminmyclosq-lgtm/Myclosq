<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
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
        $variant = $product->variants->first();
        $cartItem = auth()->check() && $variant
            ? CartItem::where('product_variant_id', $variant->id)
                ->whereHas('cart', fn ($query) => $query->where('user_id', auth()->id())->where('status', 'active'))
                ->first()
            : null;

        return view('shop.show', compact('product', 'cartItem'));
    }
}
