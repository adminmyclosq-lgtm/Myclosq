<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\AdminExcelExportService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Display a listing of products with KPIs, search, and advanced filters.
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'search', 'category_id', 'brand', 'status', 'stock',
            'price_min', 'price_max', 'sort'
        ]);

        $data = $this->productService->getProductList($filters, 15);

        return view('admin.products.index', $data);
    }

    /**
     * Export products to CSV or Excel (.xls) file.
     */
    public function export(Request $request, AdminExcelExportService $exporter)
    {
        $filters = $request->only([
            'search', 'category_id', 'brand', 'status', 'stock',
            'price_min', 'price_max', 'sort'
        ]);

        $format = $request->get('format', 'csv');
        $dataset = $this->productService->getExportDataset($filters);
        $filename = 'products-catalog-' . now()->format('Y-m-d');

        if ($format === 'excel') {
            return $exporter->download($filename, $dataset['headers'], collect($dataset['rows']));
        }

        return $exporter->downloadCsv($filename . '.csv', $dataset['headers'], $dataset['rows']);
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $brands = Product::whereNotNull('brand')->where('brand', '!=', '')->distinct()->pluck('brand')->sort()->values();

        return view('admin.products.form', [
            'product' => new Product(),
            'categories' => $categories,
            'brands' => $brands,
            'isEdit' => false,
        ]);
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateProductRequest($request);

        $mainImage = $request->file('main_image');
        $galleryImages = $request->file('gallery_images') ?? [];

        $product = $this->productService->createProduct(
            $validated,
            $mainImage,
            is_array($galleryImages) ? $galleryImages : [],
            Auth::id()
        );

        return redirect()->route('admin.products.show', $product)
            ->with('success', "Product '{$product->name}' was successfully created.");
    }

    /**
     * Display the master Product Details hub with 7 live tabs.
     */
    public function show(Product $product)
    {
        $details = $this->productService->getProductDetails($product);

        return view('admin.products.show', $details);
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $product->load([
            'category',
            'variants.currentPrice',
            'variants.prices',
            'variants.inventory',
            'productImages.media',
            'directImages.media',
        ]);

        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $brands = Product::whereNotNull('brand')->where('brand', '!=', '')->distinct()->pluck('brand')->sort()->values();

        return view('admin.products.form', [
            'product' => $product,
            'categories' => $categories,
            'brands' => $brands,
            'isEdit' => true,
        ]);
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $this->validateProductRequest($request, $product->id);

        $mainImage = $request->file('main_image');
        $galleryImages = $request->file('gallery_images') ?? [];

        $this->productService->updateProduct(
            $product,
            $validated,
            $mainImage,
            is_array($galleryImages) ? $galleryImages : [],
            Auth::id()
        );

        return redirect()->route('admin.products.show', $product)
            ->with('success', "Product '{$product->name}' was updated successfully.");
    }

    /**
     * Clone / Duplicate a product.
     */
    public function duplicate(Product $product)
    {
        $duplicated = $this->productService->duplicateProduct($product, Auth::id());

        return redirect()->route('admin.products.edit', $duplicated)
            ->with('success', "Product was duplicated as '{$duplicated->name}'. You can now edit its specifications.");
    }

    /**
     * Archive the specified product.
     */
    public function archive(Product $product)
    {
        $product->update(['status' => 'archived']);

        return back()->with('success', "Product '{$product->name}' has been archived.");
    }

    /**
     * Restore the specified product.
     */
    public function restore(Product $product)
    {
        if ($product->trashed()) {
            $product->restore();
        }
        $product->update(['status' => 'active']);

        return back()->with('success', "Product '{$product->name}' has been restored to active status.");
    }

    /**
     * Soft delete the specified product.
     */
    public function destroy(Product $product)
    {
        $name = $product->name;
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', "Product '{$name}' was deleted.");
    }

    /**
     * Handle bulk actions across multiple products.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|string|in:status,category,delete',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'integer|exists:products,id',
            'value' => 'nullable|string',
        ]);

        $count = $this->productService->bulkAction(
            $request->action,
            $request->product_ids,
            $request->value,
            Auth::id()
        );

        return back()->with('success', "Bulk action successfully applied to {$count} product(s).");
    }

    /**
     * Adjust inventory quantity and record transaction with remarks.
     */
    public function adjustInventory(Request $request, Product $product)
    {
        $validated = $request->validate([
            'adjustment_mode' => 'required|string|in:add,subtract,set',
            'quantity' => 'required|integer|min:0',
            'reason' => 'required|string|in:manual_adjustment,new_stock_received,damaged,correction,cycle_count,customer_return',
            'remarks' => 'nullable|string|max:500',
        ]);

        $this->productService->adjustInventory($product, $validated, Auth::id());

        return redirect()->route('admin.products.show', ['product' => $product, 'tab' => 'inventory'])
            ->with('success', 'Inventory stock adjusted and logged successfully.');
    }

    /**
     * AJAX/POST to reorder images or set primary image.
     */
    public function reorderImages(Request $request, Product $product)
    {
        $request->validate([
            'image_ids' => 'required|array',
            'image_ids.*' => 'integer|exists:product_images,id',
        ]);

        foreach ($request->image_ids as $order => $id) {
            ProductImage::where('id', $id)
                ->where(function ($q) use ($product) {
                    $q->where('product_id', $product->id)
                      ->orWhereHas('variant', fn ($vq) => $vq->where('product_id', $product->id));
                })
                ->update(['sort_order' => $order]);
        }

        return response()->json(['success' => true, 'message' => 'Images reordered successfully.']);
    }

    /**
     * Make an image the primary image for the product.
     */
    public function makePrimaryImage(Request $request, Product $product)
    {
        $request->validate([
            'image_id' => 'required|integer|exists:product_images,id',
        ]);

        // Unset existing primary
        ProductImage::where('product_id', $product->id)
            ->orWhereHas('variant', fn ($vq) => $vq->where('product_id', $product->id))
            ->update(['is_primary' => false, 'image_type' => 'gallery']);

        // Set new primary
        ProductImage::where('id', $request->image_id)->update([
            'is_primary' => true,
            'image_type' => 'primary',
            'sort_order' => 0,
        ]);

        return back()->with('success', 'Primary product image updated.');
    }

    /**
     * Delete an image from product.
     */
    public function deleteImage(Product $product, ProductImage $image)
    {
        $mediaId = $image->media_id;
        $storagePath = $image->media?->storage_path;
        $image->delete();

        // If no other product images reference this media, purge storage and media record
        if (!ProductImage::where('media_id', $mediaId)->exists()) {
            \App\Models\Media::where('id', $mediaId)->delete();
            if ($storagePath) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($storagePath);
            }
        }

        return back()->with('success', 'Product image removed.');
    }

    /**
     * Upload an additional image via AJAX.
     */
    public function uploadImageAjax(Request $request, Product $product)
    {
        $request->validate([
            'image' => 'required|file|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $file = $request->file('image');
        $media = $this->productService->uploadMedia($file, $product->name, Auth::id());

        $hasPrimary = ProductImage::where('product_id', $product->id)
            ->orWhereHas('variant', fn ($vq) => $vq->where('product_id', $product->id))
            ->where('is_primary', true)
            ->exists();

        $maxSort = (ProductImage::where('product_id', $product->id)->max('sort_order') ?? 0) + 1;

        $img = ProductImage::create([
            'product_id' => $product->id,
            'product_variant_id' => $product->variants->first()?->id ?? 0,
            'media_id' => $media->id,
            'image_type' => $hasPrimary ? 'gallery' : 'primary',
            'sort_order' => $hasPrimary ? $maxSort : 0,
            'alt_text' => $product->name,
            'is_primary' => !$hasPrimary,
        ]);

        return response()->json([
            'success' => true,
            'image_id' => $img->id,
            'url' => $media->url,
            'is_primary' => $img->is_primary,
        ]);
    }

    /**
     * Validation rules for product create / update.
     */
    protected function validateProductRequest(Request $request, ?int $productId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'base_sku' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'brand' => ['nullable', 'string', 'max:150'],
            'product_type' => ['nullable', 'string', 'max:150'],
            'tags' => ['nullable', 'string'],
            'short_description' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'ingredients' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
            'usage_instructions' => ['nullable', 'string'],
            'warnings' => ['nullable', 'string'],
            'internal_notes' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:active,draft,archived'],
            'is_featured' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],

            // Pricing
            'selling_price' => ['required', 'numeric', 'min:0'],
            'mrp' => ['nullable', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'tax_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'customer_group' => ['nullable', 'string', 'max:50'],

            // Inventory
            'variant_name' => ['nullable', 'string', 'max:255'],
            'variant_sku' => ['nullable', 'string', 'max:100'],
            'barcode' => ['nullable', 'string', 'max:100'],
            'track_quantity' => ['nullable', 'boolean'],
            'quantity_on_hand' => ['nullable', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'continue_selling_out_of_stock' => ['nullable', 'boolean'],
            'warehouse_code' => ['nullable', 'string', 'max:50'],

            // Shipping
            'requires_shipping' => ['nullable', 'boolean'],
            'weight_grams' => ['nullable', 'numeric', 'min:0'],
            'unit_value' => ['nullable', 'numeric'],
            'unit_label' => ['nullable', 'string', 'max:50'],
            'length_cm' => ['nullable', 'numeric', 'min:0'],
            'width_cm' => ['nullable', 'numeric', 'min:0'],
            'height_cm' => ['nullable', 'numeric', 'min:0'],
            'hs_code' => ['nullable', 'string', 'max:50'],

            // SEO
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],

            // Images
            'main_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'gallery_images' => ['nullable', 'array', 'max:10'],
            'gallery_images.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
    }
}
