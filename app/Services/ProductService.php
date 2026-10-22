<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Media;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductBatche;
use App\Models\ProductImage;
use App\Models\ProductPrice;
use App\Models\ProductVariant;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService
{
    /**
     * Retrieve filtered list of products along with KPI metrics.
     */
    public function getProductList(array $filters = [], int $perPage = 15): array
    {
        $query = Product::with([
            'category',
            'variants.currentPrice',
            'variants.prices',
            'variants.inventory',
            'productImages.media',
        ]);

        // Search filter
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('base_sku', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhereHas('variants', function ($vq) use ($search) {
                        $vq->where('sku', 'like', "%{$search}%")
                            ->orWhere('barcode', 'like', "%{$search}%");
                    });
            });
        }

        // Category filter
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Brand filter
        if (!empty($filters['brand'])) {
            $query->where('brand', $filters['brand']);
        }

        // Status filter
        if (!empty($filters['status']) && in_array($filters['status'], ['active', 'draft', 'archived'])) {
            $query->where('status', $filters['status']);
        }

        // Stock filter
        if (!empty($filters['stock'])) {
            switch ($filters['stock']) {
                case 'in_stock':
                    $query->whereHas('variants.inventory', function ($q) {
                        $q->havingRaw('SUM(quantity_on_hand) > 5');
                    });
                    break;
                case 'low_stock':
                    $query->whereHas('variants.inventory', function ($q) {
                        $q->havingRaw('SUM(quantity_on_hand) > 0 AND SUM(quantity_on_hand) <= 5');
                    });
                    break;
                case 'out_of_stock':
                    $query->where(function ($q) {
                        $q->whereDoesntHave('variants.inventory')
                            ->orWhereHas('variants.inventory', function ($iq) {
                                $iq->havingRaw('SUM(quantity_on_hand) <= 0');
                            });
                    });
                    break;
            }
        }

        // Price filter
        if (isset($filters['price_min']) && is_numeric($filters['price_min'])) {
            $min = (float) $filters['price_min'];
            $query->whereHas('variants.prices', function ($q) use ($min) {
                $q->where('selling_price', '>=', $min)->where('is_active', true);
            });
        }
        if (isset($filters['price_max']) && is_numeric($filters['price_max'])) {
            $max = (float) $filters['price_max'];
            $query->whereHas('variants.prices', function ($q) use ($max) {
                $q->where('selling_price', '<=', $max)->where('is_active', true);
            });
        }

        // Sorting
        $sort = $filters['sort'] ?? 'newest';
        switch ($sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'sku_asc':
                $query->orderBy('base_sku', 'asc');
                break;
            case 'oldest':
                $query->orderBy('id', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('id', 'desc');
                break;
        }

        $products = $query->paginate($perPage)->withQueryString();

        // Compute Orders Sold & Revenue for each product in current page efficiently
        $this->attachOrderAggregations($products);

        // Compute KPIs
        $kpis = $this->calculateKpiCards();

        // Get unique brands and categories for dropdown filters
        $brands = Product::whereNotNull('brand')->where('brand', '!=', '')->distinct()->pluck('brand')->sort()->values();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return [
            'products' => $products,
            'kpis' => $kpis,
            'brands' => $brands,
            'categories' => $categories,
            'filters' => $filters,
        ];
    }

    /**
     * Calculate summary KPI Cards for Products List.
     */
    public function calculateKpiCards(): array
    {
        $total = Product::count();
        $active = Product::where('status', 'active')->count();
        $draft = Product::where('status', 'draft')->count();
        $archived = Product::where('status', 'archived')->count();

        // Out of stock count
        $outOfStock = Product::whereDoesntHave('variants.inventory')
            ->orWhereHas('variants.inventory', function ($q) {
                $q->havingRaw('SUM(quantity_on_hand) <= 0');
            })->count();

        // Low stock count (1 to 5)
        $lowStock = Product::whereHas('variants.inventory', function ($q) {
            $q->havingRaw('SUM(quantity_on_hand) > 0 AND SUM(quantity_on_hand) <= 5');
        })->count();

        return [
            'total_products' => $total,
            'active_products' => $active,
            'out_of_stock' => $outOfStock,
            'low_stock' => $lowStock,
            'draft_products' => $draft,
            'archived_products' => $archived,
        ];
    }

    /**
     * Efficiently attach orders sold & revenue stats to a collection of products.
     */
    private function attachOrderAggregations($products): void
    {
        $productIds = $products->pluck('id')->all();
        if (empty($productIds)) {
            return;
        }

        $stats = DB::table('order_items')
            ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('product_variants.product_id', $productIds)
            ->where('orders.payment_status', 'paid')
            ->groupBy('product_variants.product_id')
            ->select(
                'product_variants.product_id',
                DB::raw('SUM(order_items.quantity) as units_sold'),
                DB::raw('COUNT(DISTINCT orders.id) as orders_count'),
                DB::raw('SUM(order_items.line_total) as total_revenue')
            )
            ->get()
            ->keyBy('product_id');

        foreach ($products as $product) {
            $item = $stats->get($product->id);
            $product->units_sold_computed = (int) ($item->units_sold ?? 0);
            $product->orders_count_computed = (int) ($item->orders_count ?? 0);
            $product->total_revenue_computed = (float) ($item->total_revenue ?? 0);
        }
    }

    /**
     * Retrieve full master details for a single Product across all 7 tabs.
     */
    public function getProductDetails(Product $product): array
    {
        $product->load([
            'category',
            'variants.prices',
            'variants.inventory.batch',
            'variants.inventory.transactions.creator',
            'productImages.media',
            'auditLogs.user',
        ]);

        $primaryVariant = $product->variants->first();

        // 1. Orders Tab Data: Retrieve all orders that purchased this product (live via Product ID)
        $orderItems = OrderItem::with(['order.user', 'order.shippingAddress'])
            ->whereHas('productVariant', function ($q) use ($product) {
                $q->where('product_id', $product->id);
            })
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->orderBy('orders.created_at', 'desc')
            ->select('order_items.*')
            ->paginate(15, ['*'], 'orders_page');

        // 2. Customers Tab Data: Aggregate unique customers who ordered this product
        $customers = DB::table('order_items')
            ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->leftJoin('customer_profiles', 'users.id', '=', 'customer_profiles.user_id')
            ->leftJoin('order_addresses', function ($join) {
                $join->on('orders.id', '=', 'order_addresses.order_id')
                    ->where('order_addresses.type', '=', 'shipping');
            })
            ->where('product_variants.product_id', $product->id)
            ->where('orders.payment_status', 'paid')
            ->groupBy(
                'orders.user_id',
                'order_addresses.email',
                'order_addresses.full_name',
                'order_addresses.phone',
                'customer_profiles.display_name',
                'customer_profiles.first_name',
                'customer_profiles.last_name',
                'users.email',
                'users.mobile'
            )
            ->select(
                'orders.user_id',
                DB::raw('COALESCE(customer_profiles.display_name, NULLIF(TRIM(CONCAT(COALESCE(customer_profiles.first_name, ""), " ", COALESCE(customer_profiles.last_name, ""))), ""), order_addresses.full_name, "Guest Customer") as customer_name'),
                DB::raw('COALESCE(users.email, order_addresses.email, "N/A") as customer_email'),
                DB::raw('COALESCE(order_addresses.phone, users.mobile, "—") as customer_phone'),
                DB::raw('SUM(order_items.quantity) as total_units_bought'),
                DB::raw('SUM(order_items.line_total) as total_spent'),
                DB::raw('COUNT(DISTINCT orders.id) as orders_count'),
                DB::raw('MAX(orders.created_at) as last_order_date')
            )
            ->orderByDesc('total_spent')
            ->get();

        // 3. Inventory Stock Breakdown & History
        $inventories = Inventory::with(['batch', 'transactions.creator'])
            ->whereHas('productVariant', fn ($q) => $q->where('product_id', $product->id))
            ->get();

        $inventoryTransactions = InventoryTransaction::with(['creator', 'inventory.productVariant'])
            ->whereHas('inventory.productVariant', fn ($q) => $q->where('product_id', $product->id))
            ->orderByDesc('id')
            ->limit(30)
            ->get();

        // 4. Pricing Analytics & Rules
        $activePrice = $primaryVariant?->currentPrice ?? $primaryVariant?->prices->first();
        $costPrice = (float) ($product->cost_price ?? 0);
        $sellingPrice = (float) ($activePrice?->selling_price ?? 0);
        $mrp = (float) ($activePrice?->mrp ?? 0);
        $profitPerUnit = max(0, $sellingPrice - $costPrice);
        $marginPercentage = $sellingPrice > 0 ? round(($profitPerUnit / $sellingPrice) * 100, 1) : 0;
        $discountFromMrp = $mrp > $sellingPrice ? round((($mrp - $sellingPrice) / $mrp) * 100, 1) : 0;

        // 5. Total Performance Metrics
        $totalUnitsSold = DB::table('order_items')
            ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('product_variants.product_id', $product->id)
            ->where('orders.payment_status', 'paid')
            ->sum('order_items.quantity');

        $totalRevenue = DB::table('order_items')
            ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('product_variants.product_id', $product->id)
            ->where('orders.payment_status', 'paid')
            ->sum('order_items.line_total');

        $totalStockOnHand = $inventories->sum('quantity_on_hand');
        $totalStockReserved = $inventories->sum('quantity_reserved');

        return [
            'product' => $product,
            'primaryVariant' => $primaryVariant,
            'orderItems' => $orderItems,
            'customers' => $customers,
            'inventories' => $inventories,
            'inventoryTransactions' => $inventoryTransactions,
            'priceHistory' => $primaryVariant ? $primaryVariant->prices()->orderByDesc('effective_from')->get() : collect(),
            'pricingStats' => [
                'mrp' => $mrp,
                'selling_price' => $sellingPrice,
                'cost_price' => $costPrice,
                'profit_per_unit' => $profitPerUnit,
                'margin_percentage' => $marginPercentage,
                'discount_percentage' => $discountFromMrp,
                'tax_percentage' => (float) ($activePrice?->tax_percentage ?? 0),
            ],
            'performance' => [
                'units_sold' => (int) $totalUnitsSold,
                'total_revenue' => (float) $totalRevenue,
                'unique_customers' => $customers->count(),
                'stock_on_hand' => $totalStockOnHand,
                'stock_reserved' => $totalStockReserved,
                'available_stock' => max(0, $totalStockOnHand - $totalStockReserved),
            ],
            'auditLogs' => $product->auditLogs()->with('user')->limit(40)->get(),
        ];
    }

    /**
     * Create a new enterprise product with variant, pricing, initial stock, and images.
     */
    public function createProduct(array $data, ?UploadedFile $mainImage = null, array $galleryImages = [], ?int $userId = null): Product
    {
        return DB::transaction(function () use ($data, $mainImage, $galleryImages, $userId) {
            $name = trim($data['name']);
            $slug = !empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($name);

            // Ensure unique slug
            $originalSlug = $slug;
            $count = 1;
            while (Product::where('slug', $slug)->exists()) {
                $slug = "{$originalSlug}-{$count}";
                $count++;
            }

            $product = Product::create([
                'category_id' => $data['category_id'] ?? null,
                'brand' => $data['brand'] ?? 'Guided Wellness',
                'product_type' => $data['product_type'] ?? null,
                'tags' => $data['tags'] ?? null,
                'name' => $name,
                'slug' => $slug,
                'base_sku' => trim($data['base_sku']),
                'cost_price' => !empty($data['cost_price']) ? (float) $data['cost_price'] : null,
                'track_quantity' => isset($data['track_quantity']) ? (bool) $data['track_quantity'] : true,
                'continue_selling_out_of_stock' => !empty($data['continue_selling_out_of_stock']),
                'low_stock_threshold' => isset($data['low_stock_threshold']) ? (int) $data['low_stock_threshold'] : 5,
                'requires_shipping' => isset($data['requires_shipping']) ? (bool) $data['requires_shipping'] : true,
                'hs_code' => $data['hs_code'] ?? null,
                'short_description' => $data['short_description'] ?? null,
                'description' => $data['description'] ?? null,
                'ingredients' => $data['ingredients'] ?? null,
                'benefits' => $data['benefits'] ?? null,
                'usage_instructions' => $data['usage_instructions'] ?? null,
                'warnings' => $data['warnings'] ?? null,
                'internal_notes' => $data['internal_notes'] ?? null,
                'status' => $data['status'] ?? 'draft',
                'is_featured' => !empty($data['is_featured']),
                'sort_order' => (int) ($data['sort_order'] ?? 0),
                'seo_title' => $data['seo_title'] ?? $name,
                'seo_description' => $data['seo_description'] ?? Str::limit(strip_tags($data['short_description'] ?? $data['description'] ?? ''), 160),
            ]);

            // Create Primary Variant
            $variant = $product->variants()->create([
                'name' => $data['variant_name'] ?? $name,
                'sku' => !empty($data['variant_sku']) ? trim($data['variant_sku']) : $product->base_sku,
                'barcode' => $data['barcode'] ?? null,
                'unit_value' => !empty($data['unit_value']) ? (float) $data['unit_value'] : null,
                'unit_label' => $data['unit_label'] ?? null,
                'weight_grams' => !empty($data['weight_grams']) ? (float) $data['weight_grams'] : 0,
                'length_cm' => !empty($data['length_cm']) ? (float) $data['length_cm'] : null,
                'width_cm' => !empty($data['width_cm']) ? (float) $data['width_cm'] : null,
                'height_cm' => !empty($data['height_cm']) ? (float) $data['height_cm'] : null,
                'status' => $product->status === 'active' ? 'active' : 'inactive',
            ]);

            // Create Price
            $mrp = (float) ($data['mrp'] ?? $data['selling_price'] ?? 0);
            $sellingPrice = (float) ($data['selling_price'] ?? $mrp);
            $variant->prices()->create([
                'mrp' => $mrp,
                'selling_price' => $sellingPrice,
                'tax_percentage' => (float) ($data['tax_percentage'] ?? 0),
                'customer_group' => $data['customer_group'] ?? 'default',
                'effective_from' => now(),
                'is_active' => true,
            ]);

            // Opening Inventory
            $openingStock = (int) ($data['quantity_on_hand'] ?? 0);
            $batchNumber = 'BATCH-' . $product->id . '-' . date('Ymd');
            $batch = ProductBatche::create([
                'product_variant_id' => $variant->id,
                'batch_number' => $batchNumber,
                'manufacture_date' => now()->toDateString(),
                'quantity_received' => $openingStock,
                'status' => 'active',
            ]);

            $inventory = Inventory::create([
                'product_variant_id' => $variant->id,
                'batch_id' => $batch->id,
                'quantity_on_hand' => $openingStock,
                'quantity_reserved' => 0,
                'reorder_level' => (int) ($data['low_stock_threshold'] ?? 5),
                'warehouse_code' => $data['warehouse_code'] ?? 'DEFAULT',
                'status' => 'active',
            ]);

            if ($openingStock > 0) {
                InventoryTransaction::create([
                    'inventory_id' => $inventory->id,
                    'transaction_type' => 'opening_stock',
                    'quantity' => $openingStock,
                    'reference_type' => 'initial_creation',
                    'reference_id' => $product->id,
                    'balance_after' => $openingStock,
                    'remarks' => 'Initial opening stock upon product creation.',
                    'created_by' => $userId,
                    'created_at' => now(),
                ]);
            }

            // Sync Images
            $this->storeProductImages($product, $variant, $mainImage, $galleryImages, $userId);

            // Audit Log
            AuditLog::create([
                'user_id' => $userId,
                'action' => 'product_created',
                'auditable_type' => Product::class,
                'auditable_id' => $product->id,
                'new_values' => [
                    'name' => $product->name,
                    'sku' => $product->base_sku,
                    'price' => $sellingPrice,
                    'stock' => $openingStock,
                    'status' => $product->status,
                ],
                'created_at' => now(),
            ]);

            return $product;
        });
    }

    /**
     * Update an existing product.
     */
    public function updateProduct(Product $product, array $data, ?UploadedFile $mainImage = null, array $galleryImages = [], ?int $userId = null): Product
    {
        return DB::transaction(function () use ($product, $data, $mainImage, $galleryImages, $userId) {
            $oldValues = [
                'name' => $product->name,
                'base_sku' => $product->base_sku,
                'status' => $product->status,
                'cost_price' => $product->cost_price,
                'category_id' => $product->category_id,
            ];

            $name = trim($data['name']);
            $slug = !empty($data['slug']) ? Str::slug($data['slug']) : $product->slug;

            $product->update([
                'category_id' => $data['category_id'] ?? null,
                'brand' => $data['brand'] ?? $product->brand,
                'product_type' => $data['product_type'] ?? null,
                'tags' => $data['tags'] ?? null,
                'name' => $name,
                'slug' => $slug,
                'base_sku' => trim($data['base_sku']),
                'cost_price' => isset($data['cost_price']) && $data['cost_price'] !== '' ? (float) $data['cost_price'] : null,
                'track_quantity' => isset($data['track_quantity']) ? (bool) $data['track_quantity'] : true,
                'continue_selling_out_of_stock' => !empty($data['continue_selling_out_of_stock']),
                'low_stock_threshold' => isset($data['low_stock_threshold']) ? (int) $data['low_stock_threshold'] : 5,
                'requires_shipping' => isset($data['requires_shipping']) ? (bool) $data['requires_shipping'] : true,
                'hs_code' => $data['hs_code'] ?? null,
                'short_description' => $data['short_description'] ?? null,
                'description' => $data['description'] ?? null,
                'ingredients' => $data['ingredients'] ?? null,
                'benefits' => $data['benefits'] ?? null,
                'usage_instructions' => $data['usage_instructions'] ?? null,
                'warnings' => $data['warnings'] ?? null,
                'internal_notes' => $data['internal_notes'] ?? null,
                'status' => $data['status'] ?? $product->status,
                'is_featured' => !empty($data['is_featured']),
                'sort_order' => (int) ($data['sort_order'] ?? $product->sort_order),
                'seo_title' => $data['seo_title'] ?? $name,
                'seo_description' => $data['seo_description'] ?? $product->seo_description,
            ]);

            // Update primary variant
            $variant = $product->variants()->first();
            if ($variant) {
                $variant->update([
                    'name' => $data['variant_name'] ?? $name,
                    'sku' => !empty($data['variant_sku']) ? trim($data['variant_sku']) : $product->base_sku,
                    'barcode' => $data['barcode'] ?? $variant->barcode,
                    'unit_value' => !empty($data['unit_value']) ? (float) $data['unit_value'] : null,
                    'unit_label' => $data['unit_label'] ?? null,
                    'weight_grams' => !empty($data['weight_grams']) ? (float) $data['weight_grams'] : 0,
                    'length_cm' => !empty($data['length_cm']) ? (float) $data['length_cm'] : null,
                    'width_cm' => !empty($data['width_cm']) ? (float) $data['width_cm'] : null,
                    'height_cm' => !empty($data['height_cm']) ? (float) $data['height_cm'] : null,
                    'status' => $product->status === 'active' ? 'active' : 'inactive',
                ]);

                // Update Price if changed
                if (isset($data['selling_price']) || isset($data['mrp'])) {
                    $activePrice = $variant->currentPrice ?? $variant->prices()->latest('effective_from')->first();
                    $newMrp = (float) ($data['mrp'] ?? $activePrice?->mrp ?? $data['selling_price']);
                    $newSelling = (float) ($data['selling_price'] ?? $activePrice?->selling_price ?? $newMrp);
                    $newTax = (float) ($data['tax_percentage'] ?? $activePrice?->tax_percentage ?? 0);

                    if (!$activePrice || $activePrice->selling_price != $newSelling || $activePrice->mrp != $newMrp) {
                        // Deactivate old price and insert new record for historical auditability
                        if ($activePrice) {
                            $activePrice->update(['effective_to' => now(), 'is_active' => false]);
                        }
                        $variant->prices()->create([
                            'mrp' => $newMrp,
                            'selling_price' => $newSelling,
                            'tax_percentage' => $newTax,
                            'customer_group' => $data['customer_group'] ?? 'default',
                            'effective_from' => now(),
                            'is_active' => true,
                        ]);
                    }
                }
            }

            // Sync Images if provided
            if ($mainImage || !empty($galleryImages)) {
                $this->storeProductImages($product, $variant, $mainImage, $galleryImages, $userId);
            }

            // Audit Log
            AuditLog::create([
                'user_id' => $userId,
                'action' => 'product_updated',
                'auditable_type' => Product::class,
                'auditable_id' => $product->id,
                'old_values' => $oldValues,
                'new_values' => [
                    'name' => $product->name,
                    'base_sku' => $product->base_sku,
                    'status' => $product->status,
                    'cost_price' => $product->cost_price,
                    'category_id' => $product->category_id,
                ],
                'created_at' => now(),
            ]);

            return $product;
        });
    }

    /**
     * Store and associate product images cleanly.
     */
    private function storeProductImages(Product $product, ?ProductVariant $variant, ?UploadedFile $mainImage, array $galleryImages, ?int $userId): void
    {
        if ($mainImage instanceof UploadedFile) {
            // Replace existing primary image
            $existingPrimary = ProductImage::where('product_id', $product->id)
                ->orWhere(function ($q) use ($variant) {
                    if ($variant) $q->where('product_variant_id', $variant->id);
                })
                ->where('is_primary', true)
                ->first();

            if ($existingPrimary) {
                $existingPrimary->update(['is_primary' => false]);
            }

            $media = $this->uploadMedia($mainImage, $product->name, $userId);
            ProductImage::create([
                'product_id' => $product->id,
                'product_variant_id' => $variant ? $variant->id : 0,
                'media_id' => $media->id,
                'image_type' => 'primary',
                'sort_order' => 0,
                'alt_text' => $product->name,
                'is_primary' => true,
            ]);
        }

        $currentMaxSort = ProductImage::where('product_id', $product->id)->max('sort_order') ?? 0;
        foreach ($galleryImages as $file) {
            if ($file instanceof UploadedFile) {
                $currentMaxSort++;
                $media = $this->uploadMedia($file, $product->name, $userId);
                ProductImage::create([
                    'product_id' => $product->id,
                    'product_variant_id' => $variant ? $variant->id : 0,
                    'media_id' => $media->id,
                    'image_type' => 'gallery',
                    'sort_order' => $currentMaxSort,
                    'alt_text' => $product->name . ' - Gallery ' . $currentMaxSort,
                    'is_primary' => false,
                ]);
            }
        }
    }

    /**
     * Upload an image file to storage and register in media table.
     */
    public function uploadMedia(UploadedFile $file, string $altText = '', ?int $userId = null): Media
    {
        $path = $file->storePublicly('products', ['disk' => 'public']);
        return Media::create([
            'uuid' => (string) Str::uuid(),
            'file_name' => $file->getClientOriginalName(),
            'storage_path' => $path,
            'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
            'file_size' => $file->getSize(),
            'alt_text' => $altText,
            'uploaded_by' => $userId,
            'created_at' => now(),
        ]);
    }

    /**
     * Duplicate a product.
     */
    public function duplicateProduct(Product $product, ?int $userId = null): Product
    {
        return DB::transaction(function () use ($product, $userId) {
            $cloneName = 'Copy of ' . $product->name;
            $cloneSku = $product->base_sku . '-COPY-' . strtoupper(Str::random(3));
            $cloneSlug = Str::slug($cloneName . '-' . Str::random(4));

            $newProduct = $product->replicate([
                'created_at',
                'updated_at',
                'deleted_at',
            ]);
            $newProduct->name = $cloneName;
            $newProduct->slug = $cloneSlug;
            $newProduct->base_sku = $cloneSku;
            $newProduct->status = 'draft';
            $newProduct->save();

            // Clone primary variant
            $origVariant = $product->variants->first();
            if ($origVariant) {
                $newVariant = $origVariant->replicate(['created_at', 'updated_at']);
                $newVariant->product_id = $newProduct->id;
                $newVariant->name = $cloneName;
                $newVariant->sku = $cloneSku;
                $newVariant->barcode = null;
                $newVariant->save();

                // Clone price
                $activePrice = $origVariant->currentPrice ?? $origVariant->prices->first();
                if ($activePrice) {
                    $newPrice = $activePrice->replicate(['created_at', 'updated_at']);
                    $newPrice->product_variant_id = $newVariant->id;
                    $newPrice->effective_from = now();
                    $newPrice->effective_to = null;
                    $newPrice->is_active = true;
                    $newPrice->save();
                }

                // Initialize opening zero stock
                $batch = ProductBatche::create([
                    'product_variant_id' => $newVariant->id,
                    'batch_number' => 'BATCH-' . $newProduct->id . '-COPY',
                    'quantity_received' => 0,
                    'status' => 'active',
                ]);

                Inventory::create([
                    'product_variant_id' => $newVariant->id,
                    'batch_id' => $batch->id,
                    'quantity_on_hand' => 0,
                    'quantity_reserved' => 0,
                    'reorder_level' => 5,
                    'warehouse_code' => 'DEFAULT',
                    'status' => 'active',
                ]);

                // Copy image links
                $images = ProductImage::where('product_id', $product->id)
                    ->orWhere('product_variant_id', $origVariant->id)
                    ->get();
                foreach ($images as $img) {
                    ProductImage::create([
                        'product_id' => $newProduct->id,
                        'product_variant_id' => $newVariant->id,
                        'media_id' => $img->media_id,
                        'image_type' => $img->image_type,
                        'sort_order' => $img->sort_order,
                        'alt_text' => $cloneName,
                        'is_primary' => $img->is_primary,
                    ]);
                }
            }

            AuditLog::create([
                'user_id' => $userId,
                'action' => 'product_duplicated',
                'auditable_type' => Product::class,
                'auditable_id' => $newProduct->id,
                'new_values' => ['source_product_id' => $product->id, 'new_sku' => $cloneSku],
                'created_at' => now(),
            ]);

            return $newProduct;
        });
    }

    /**
     * Adjust inventory stock with full transaction audit logging.
     */
    public function adjustInventory(Product $product, array $params, ?int $userId = null): Inventory
    {
        return DB::transaction(function () use ($product, $params, $userId) {
            $variant = $product->variants->first();
            if (!$variant) {
                throw new \Exception("Product has no active variant.");
            }

            $inventory = $variant->inventory()->first();
            if (!$inventory) {
                $batch = ProductBatche::firstOrCreate(
                    ['product_variant_id' => $variant->id, 'batch_number' => 'OPENING-' . $variant->id],
                    ['quantity_received' => 0, 'status' => 'active']
                );
                $inventory = Inventory::create([
                    'product_variant_id' => $variant->id,
                    'batch_id' => $batch->id,
                    'quantity_on_hand' => 0,
                    'quantity_reserved' => 0,
                    'reorder_level' => 5,
                    'warehouse_code' => 'DEFAULT',
                    'status' => 'active',
                ]);
            }

            $oldOnHand = $inventory->quantity_on_hand;
            $type = $params['adjustment_mode'] ?? 'add'; // 'add', 'subtract', 'set'
            $qty = (int) ($params['quantity'] ?? 0);
            $reason = $params['reason'] ?? 'manual_adjustment';
            $remarks = $params['remarks'] ?? 'Inventory adjusted by administrator';

            if ($type === 'add') {
                $newOnHand = $oldOnHand + $qty;
                $delta = $qty;
            } elseif ($type === 'subtract') {
                $newOnHand = max(0, $oldOnHand - $qty);
                $delta = -$qty;
            } else { // 'set'
                $newOnHand = max(0, $qty);
                $delta = $newOnHand - $oldOnHand;
            }

            $inventory->update([
                'quantity_on_hand' => $newOnHand,
            ]);

            InventoryTransaction::create([
                'inventory_id' => $inventory->id,
                'transaction_type' => $reason,
                'quantity' => $delta,
                'reference_type' => 'manual_adjustment',
                'reference_id' => $product->id,
                'balance_after' => $newOnHand,
                'remarks' => $remarks,
                'created_by' => $userId,
                'created_at' => now(),
            ]);

            AuditLog::create([
                'user_id' => $userId,
                'action' => 'inventory_adjusted',
                'auditable_type' => Product::class,
                'auditable_id' => $product->id,
                'old_values' => ['quantity_on_hand' => $oldOnHand],
                'new_values' => ['quantity_on_hand' => $newOnHand, 'delta' => $delta, 'reason' => $reason],
                'created_at' => now(),
            ]);

            return $inventory;
        });
    }

    /**
     * Execute bulk actions on multiple product IDs.
     */
    public function bulkAction(string $action, array $productIds, ?string $value = null, ?int $userId = null): int
    {
        if (empty($productIds)) {
            return 0;
        }

        return DB::transaction(function () use ($action, $productIds, $value, $userId) {
            $count = 0;
            switch ($action) {
                case 'status':
                    if (in_array($value, ['active', 'draft', 'archived'])) {
                        $count = Product::whereIn('id', $productIds)->update(['status' => $value]);
                    }
                    break;
                case 'category':
                    if (!empty($value) && Category::where('id', $value)->exists()) {
                        $count = Product::whereIn('id', $productIds)->update(['category_id' => $value]);
                    }
                    break;
                case 'delete':
                    $count = Product::whereIn('id', $productIds)->delete();
                    break;
            }

            AuditLog::create([
                'user_id' => $userId,
                'action' => "bulk_{$action}",
                'auditable_type' => Product::class,
                'auditable_id' => null,
                'new_values' => ['action' => $action, 'value' => $value, 'ids' => $productIds, 'count' => $count],
                'created_at' => now(),
            ]);

            return $count;
        });
    }

    /**
     * Format export dataset for CSV or Excel.
     */
    public function getExportDataset(array $filters = []): array
    {
        $result = $this->getProductList($filters, 10000);
        $products = $result['products'];

        $headers = [
            'Product ID',
            'Product Name',
            'SKU',
            'Category',
            'Brand',
            'Price (INR)',
            'MRP (INR)',
            'Cost Price (INR)',
            'Stock on Hand',
            'Status',
            'Units Sold',
            'Total Revenue (INR)',
            'Created At',
        ];

        $rows = [];
        foreach ($products as $p) {
            $variant = $p->variants->first();
            $price = $variant?->currentPrice ?? $variant?->prices->first();
            $stock = $variant?->inventory->sum('quantity_on_hand') ?? 0;

            $rows[] = [
                $p->id,
                $p->name,
                $p->base_sku,
                $p->category?->name ?? 'Uncategorized',
                $p->brand ?? 'Guided Wellness',
                number_format((float) ($price?->selling_price ?? 0), 2, '.', ''),
                number_format((float) ($price?->mrp ?? 0), 2, '.', ''),
                number_format((float) ($p->cost_price ?? 0), 2, '.', ''),
                $stock,
                ucfirst($p->status),
                $p->units_sold_computed ?? 0,
                number_format((float) ($p->total_revenue_computed ?? 0), 2, '.', ''),
                $p->created_at->format('Y-m-d H:i:s'),
            ];
        }

        return [
            'headers' => $headers,
            'rows' => $rows,
        ];
    }
}
