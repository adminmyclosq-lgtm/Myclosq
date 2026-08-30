<?php
namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductBatche;
use App\Models\ProductPrice;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
            $mainImageFile = $data['main_image'] ?? $data['image'] ?? null;
            $galleryImageFiles = $data['gallery_images'] ?? [];
            $variantData = $data['variant'];
            $priceData = $data['price'];
            $productData = Arr::except($data, ['variant', 'price', 'main_image', 'gallery_images', 'image', 'inventory']);
            $productData['slug']=$productData['slug'] ?: Str::slug($productData['name']);
            $product=Product::create($productData);
            $variant=$product->variants()->create($variantData);
            $variant->prices()->create(array_merge($priceData,['effective_from'=>now(),'is_active'=>true]));
            $this->ensureInventory($variant, (int) data_get($data, 'inventory.quantity_on_hand', 0));
            $this->syncVariantImages($variant, $mainImageFile, $galleryImageFiles);
            return $product;
        });
        return redirect()->route('admin.products.index')->with('success',"Product {$product->name} created.");
    }

    public function edit(Product $product) {
        $product->load(['variants.prices', 'productImages.media']);
        return view('admin.products.form',['product'=>$product,'categories'=>Category::where('is_active',true)->orderBy('name')->get()]);
    }

    public function update(Request $request, Product $product) {
        $data=$this->validated($request);
        DB::transaction(function() use($data, $product) {
            $mainImageFile = $data['main_image'] ?? $data['image'] ?? null;
            $galleryImageFiles = $data['gallery_images'] ?? [];
            $variantData = $data['variant'];
            $priceData = $data['price'];
            $productData = Arr::except($data, ['variant', 'price', 'main_image', 'gallery_images', 'image', 'inventory']);

            $product->update($productData);
            $variant=$product->variants()->firstOrCreate(['sku'=>$variantData['sku']],$variantData);
            $variant->update($variantData);
            $price=$variant->prices()->where('is_active',true)->latest('effective_from')->first();
            if($price) {
                $price->update($priceData);
            } else {
                $variant->prices()->create(array_merge($priceData,['effective_from'=>now(),'is_active'=>true]));
            }

            $this->ensureInventory($variant);

            $this->syncVariantImages($variant, $mainImageFile, $galleryImageFiles);
        });

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
            'main_image'=>['nullable','file','mimes:jpg,jpeg,png,webp','max:5120'],
            'gallery_images'=>['nullable','array','max:3'],
            'gallery_images.*'=>['nullable','file','mimes:jpg,jpeg,png,webp','max:5120'],
            'image'=>['nullable','file','mimes:jpg,jpeg,png,webp','max:5120'],
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
            'inventory.quantity_on_hand'=>['nullable','integer','min:0'],
        ]);
    }

    private function ensureInventory(ProductVariant $variant, int $quantityOnHand = 0): void
    {
        $legacyBatchNumber = $variant->sku.'-OPENING';
        $batchNumber = 'OPENING-'.$variant->id;

        $batch = ProductBatche::where('product_variant_id', $variant->id)
            ->whereIn('batch_number', [$legacyBatchNumber, $batchNumber])
            ->first();

        $batch ??= ProductBatche::firstOrCreate(
            ['product_variant_id' => $variant->id, 'batch_number' => $batchNumber],
            ['quantity_received' => $quantityOnHand, 'status' => 'active']
        );

        Inventory::firstOrCreate(
            ['product_variant_id' => $variant->id, 'batch_id' => $batch->id, 'warehouse_code' => 'DEFAULT'],
            ['quantity_on_hand' => $quantityOnHand, 'quantity_reserved' => 0, 'reorder_level' => 0, 'status' => 'active']
        );
    }

    private function syncVariantImages(ProductVariant $variant, ?UploadedFile $mainImageFile, array $galleryImageFiles): void
    {
        if ($mainImageFile instanceof UploadedFile) {
            $this->replaceImageSlot($variant, $mainImageFile, true, 0);
        }

        foreach ($galleryImageFiles as $slot => $file) {
            if ($file instanceof UploadedFile && $slot < 3) {
                $this->replaceImageSlot($variant, $file, false, $slot + 1);
            }
        }
    }

    private function replaceImageSlot(ProductVariant $variant, UploadedFile $file, bool $isPrimary, int $sortOrder): void
    {
        $existingImages = $variant->images()
            ->with('media')
            ->when($isPrimary, fn ($query) => $query->where('is_primary', true))
            ->unless($isPrimary, fn ($query) => $query->where('is_primary', false)->where('sort_order', $sortOrder))
            ->get();

        foreach ($existingImages as $image) {
            $this->deleteImageAndUnusedMedia($image);
        }

        $path = $file->storePublicly('products', ['disk' => 'public']);
        $mediaId = DB::table('media')->insertGetId([
            'uuid' => (string) Str::uuid(),
            'file_name' => $file->getClientOriginalName(),
            'storage_path' => $path,
            'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
            'file_size' => $file->getSize(),
            'width' => null,
            'height' => null,
            'alt_text' => $variant->product->name,
            'uploaded_by' => null,
            'created_at' => now(),
        ]);

        $variant->images()->create([
            'media_id' => $mediaId,
            'image_type' => $isPrimary ? 'primary' : 'gallery',
            'sort_order' => $sortOrder,
            'alt_text' => $variant->product->name,
            'is_primary' => $isPrimary,
        ]);
    }

    private function deleteImageAndUnusedMedia($image): void
    {
        $mediaId = $image->media_id;
        $storagePath = $image->media?->storage_path;
        $image->delete();

        if (!DB::table('product_images')->where('media_id', $mediaId)->exists()) {
            DB::table('media')->where('id', $mediaId)->delete();
            if ($storagePath) {
                Storage::disk('public')->delete($storagePath);
            }
        }
    }
}
