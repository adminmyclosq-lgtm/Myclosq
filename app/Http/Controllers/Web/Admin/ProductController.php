<?php
namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request) {
        $products=Product::with(['category','variants.currentPrice'])->when($request->search,fn($q,$v)=>$q->where('name','like',"%{$v}%"))->latest('id')->paginate(20);
        return view('admin.products.index',compact('products'));
    }

    public function create() {
        return view('admin.products.form',['product'=>new Product(),'categories'=>Category::where('is_active',true)->orderBy('name')->get()]);
    }

    public function store(Request $request) {
        $data=$this->validated($request);
        $product=DB::transaction(function() use($data) {
            $variantData=$data['variant']; unset($data['variant']);
            $priceData=$data['price']; unset($data['price']);
            $data['slug']=$data['slug'] ?: Str::slug($data['name']);
            $product=Product::create($data);
            $variant=$product->variants()->create($variantData);
            $variant->prices()->create(array_merge($priceData,['effective_from'=>now(),'is_active'=>true]));
            return $product;
        });
        return redirect()->route('admin.products.index')->with('success',"Product {$product->name} created.");
    }

    public function edit(Product $product) {
        $product->load('variants.prices');
        return view('admin.products.form',['product'=>$product,'categories'=>Category::where('is_active',true)->orderBy('name')->get()]);
    }

    public function update(Request $request, Product $product) {
        $data=$this->validated($request);
        $variantData=$data['variant']; $priceData=$data['price'];
        unset($data['variant'],$data['price']);
        $product->update($data);
        $variant=$product->variants()->firstOrCreate(['sku'=>$variantData['sku']],$variantData);
        $variant->update($variantData);
        $price=$variant->prices()->where('is_active',true)->latest('effective_from')->first();
        if($price) $price->update($priceData); else $variant->prices()->create(array_merge($priceData,['effective_from'=>now(),'is_active'=>true]));
        return redirect()->route('admin.products.index')->with('success','Product updated.');
    }

    public function destroy(Product $product) {
        $product->delete();
        return back()->with('success','Product archived.');
    }

    private function validated(Request $request): array {
        return $request->validate([
            'category_id'=>['nullable','integer','exists:categories,id'],
            'name'=>['required','string','max:255'],
            'slug'=>['nullable','string','max:255'],
            'base_sku'=>['required','string','max:100'],
            'short_description'=>['nullable','string'],
            'description'=>['nullable','string'],
            'ingredients'=>['nullable','string'],
            'benefits'=>['nullable','string'],
            'usage_instructions'=>['nullable','string'],
            'warnings'=>['nullable','string'],
            'status'=>['required','string','max:30'],
            'is_featured'=>['boolean'],
            'seo_title'=>['nullable','string','max:255'],
            'seo_description'=>['nullable','string','max:500'],
            'variant.name'=>['required','string','max:255'],
            'variant.sku'=>['required','string','max:100'],
            'variant.barcode'=>['nullable','string','max:100'],
            'variant.unit_value'=>['nullable','numeric'],
            'variant.unit_label'=>['nullable','string','max:50'],
            'variant.weight_grams'=>['nullable','numeric'],
            'variant.status'=>['required','string','max:30'],
            'price.mrp'=>['required','numeric','min:0'],
            'price.selling_price'=>['required','numeric','min:0'],
            'price.tax_percentage'=>['nullable','numeric','min:0'],
            'price.customer_group'=>['nullable','string','max:50'],
        ]);
    }
}
