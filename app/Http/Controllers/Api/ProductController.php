<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = Product::with(['productImages.media','variants.prices'])
            ->where('status', 'active');

        if ($request->filled('category_id')) {
            $q->where('category_id', $request->integer('category_id'));
        }

        if ($request->filled('search')) {
            $term = $request->string('search')->toString();
            $q->where(fn ($x) => $x->where('name','like',"%{$term}%")
                ->orWhere('short_description','like',"%{$term}%"));
        }

        return ProductResource::collection($q->orderBy('sort_order')->paginate(20));
    }

    public function show(Product $product)
    {
        abort_unless($product->status === 'active', 404);

        return new ProductResource(
            $product->load(['productImages.media','variants.prices','category'])
        );
    }
}
