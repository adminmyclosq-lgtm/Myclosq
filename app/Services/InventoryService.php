<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReturn;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Retrieve Inventory Dashboard dataset with 6 KPI cards, stock levels list, and filters.
     */
    public function getInventoryDashboard(Request $request): array
    {
        // 1. Calculate the 6 Master KPI Cards
        $kpis = $this->calculateInventoryKpis();

        // 2. Query Inventory Stock Levels
        $query = Inventory::with([
            'productVariant.product.category',
            'productVariant.product.productImages.media',
            'productVariant.prices',
            'batch',
        ]);

        // Join for sorting and product-level filtering
        $query->join('product_variants', 'inventory.product_variant_id', '=', 'product_variants.id')
              ->join('products', 'product_variants.product_id', '=', 'products.id');

        // Text Search (Product name, SKU, base SKU, barcode)
        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('products.name', 'like', "%{$search}%")
                  ->orWhere('product_variants.sku', 'like', "%{$search}%")
                  ->orWhere('products.base_sku', 'like', "%{$search}%")
                  ->orWhere('product_variants.barcode', 'like', "%{$search}%");
            });
        }

        // Warehouse Filter
        if ($warehouse = $request->input('warehouse')) {
            $query->where('inventory.warehouse_code', $warehouse);
        }

        // Category Filter
        if ($category = $request->input('category_id')) {
            $query->where('products.category_id', $category);
        }

        // Stock Status Filter: 'all', 'in_stock', 'low_stock', 'out_of_stock'
        $status = $request->input('status', 'all');
        if ($status === 'out_of_stock') {
            $query->whereRaw('(inventory.quantity_on_hand - inventory.quantity_reserved) <= 0');
        } elseif ($status === 'low_stock') {
            $query->whereRaw('(inventory.quantity_on_hand - inventory.quantity_reserved) > 0')
                  ->whereRaw('(inventory.quantity_on_hand - inventory.quantity_reserved) <= COALESCE(inventory.reorder_level, inventory.min_level, 10)');
        } elseif ($status === 'in_stock') {
            $query->whereRaw('(inventory.quantity_on_hand - inventory.quantity_reserved) > COALESCE(inventory.reorder_level, inventory.min_level, 10)');
        }

        // Sorting
        $sort = $request->input('sort', 'inventory.id');
        $direction = $request->input('direction', 'asc');

        if ($sort === 'stock') {
            $query->orderBy('inventory.quantity_on_hand', $direction);
        } elseif ($sort === 'available') {
            $query->orderByRaw('(inventory.quantity_on_hand - inventory.quantity_reserved) ' . $direction);
        } elseif ($sort === 'sku') {
            $query->orderBy('product_variants.sku', $direction);
        } elseif ($sort === 'product') {
            $query->orderBy('products.name', $direction);
        } else {
            $query->orderBy('inventory.id', $direction);
        }

        $items = $query->select('inventory.*')->paginate(20)->withQueryString();

        // Warehouse list for filter
        $warehouses = Inventory::distinct()->pluck('warehouse_code')->filter()->values();

        return [
            'kpis' => $kpis,
            'items' => $items,
            'warehouses' => $warehouses,
            'filters' => $request->only(['search', 'status', 'warehouse', 'category_id', 'sort', 'direction']),
        ];
    }

    /**
     * Compute 6 Master Inventory KPI Cards
     */
    public function calculateInventoryKpis(): array
    {
        // 1. Total SKUs tracked
        $totalSkus = Inventory::distinct('product_variant_id')->count('product_variant_id');

        // 2. Total Stock on Hand
        $totalStock = (int) Inventory::sum('quantity_on_hand');

        // 3. Low Stock Count
        $lowStock = Inventory::whereRaw('(quantity_on_hand - quantity_reserved) > 0')
            ->whereRaw('(quantity_on_hand - quantity_reserved) <= COALESCE(reorder_level, min_level, 10)')
            ->count();

        // 4. Out of Stock Count
        $outOfStock = Inventory::whereRaw('(quantity_on_hand - quantity_reserved) <= 0')
            ->count();

        // 5. Inventory Valuation at Cost
        $inventoryValue = DB::table('inventory')
            ->join('product_variants', 'inventory.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->select(DB::raw('SUM(inventory.quantity_on_hand * COALESCE(products.cost_price, 0)) as total_val'))
            ->value('total_val') ?: 0.0;

        // 6. Stock Movements Today
        $movementsToday = InventoryTransaction::where('created_at', '>=', Carbon::today())->count();

        return [
            'total_skus' => $totalSkus,
            'total_stock' => $totalStock,
            'low_stock' => $lowStock,
            'out_of_stock' => $outOfStock,
            'inventory_value' => (float) $inventoryValue,
            'movements_today' => $movementsToday,
        ];
    }

    /**
     * Retrieve complete 6-Tab Product Inventory Details.
     */
    public function getInventoryDetails(Inventory $inventory): array
    {
        $inventory->load([
            'productVariant.product.category',
            'productVariant.product.productImages.media',
            'productVariant.prices',
            'batch',
            'transactions.creator',
        ]);

        $variant = $inventory->productVariant;
        $product = $variant?->product;

        // 1. Stock Movements for this inventory item
        $movements = InventoryTransaction::with('creator')
            ->where('inventory_id', $inventory->id)
            ->orderByDesc('id')
            ->paginate(15, ['*'], 'movements_page');

        // 2. Orders that purchased this variant
        $orders = OrderItem::with(['order.user', 'order.shippingAddress'])
            ->where('product_variant_id', $variant?->id)
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->orderByDesc('orders.created_at')
            ->select('order_items.*')
            ->paginate(10, ['*'], 'orders_page');

        // 3. Manual Adjustments filter
        $adjustments = InventoryTransaction::with('creator')
            ->where('inventory_id', $inventory->id)
            ->whereIn('transaction_type', ['adjustment', 'manual_adjustment', 'damage', 'recount'])
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'adjustments_page');

        // 4. Purchase History / Batch Replenishments
        $batches = ProductBatch::where('product_variant_id', $variant?->id)
            ->orderByDesc('id')
            ->get();

        // 5. Valuation metrics
        $costPrice = (float) ($product?->cost_price ?: 0);
        $sellingPrice = (float) ($variant?->prices->first()?->selling_price ?: $product?->selling_price ?: 0);
        $totalCostValue = $inventory->quantity_on_hand * $costPrice;
        $totalRetailValue = $inventory->quantity_on_hand * $sellingPrice;

        return [
            'inventory' => $inventory,
            'variant' => $variant,
            'product' => $product,
            'movements' => $movements,
            'orders' => $orders,
            'adjustments' => $adjustments,
            'batches' => $batches,
            'valuation' => [
                'cost_price' => $costPrice,
                'selling_price' => $sellingPrice,
                'total_cost_value' => $totalCostValue,
                'total_retail_value' => $totalRetailValue,
            ],
        ];
    }

    /**
     * Adjust stock for an inventory record and log audit transaction.
     */
    public function adjustStock(Inventory $inventory, array $data, ?int $userId): Inventory
    {
        return DB::transaction(function () use ($inventory, $data, $userId) {
            $inventory->refresh();

            $prevQty = (int) $inventory->quantity_on_hand;
            $qtyInput = (int) $data['quantity'];
            $mode = $data['adjustment_mode'] ?? 'add'; // 'add', 'remove', 'set'
            $reason = $data['reason'] ?? 'manual_adjustment'; // 'purchase', 'manual_adjustment', 'damage', 'refund', 'transfer'
            $remarks = $data['remarks'] ?? null;

            if ($mode === 'set') {
                $newQty = max(0, $qtyInput);
                $delta = $newQty - $prevQty;
            } elseif ($mode === 'remove') {
                $delta = -abs($qtyInput);
                $newQty = max(0, $prevQty + $delta);
                $delta = $newQty - $prevQty; // in case of floor at 0
            } else {
                // 'add'
                $delta = abs($qtyInput);
                $newQty = $prevQty + $delta;
            }

            // Update inventory
            $inventory->quantity_on_hand = $newQty;
            $inventory->save();

            $balanceAfter = max(0, $newQty - $inventory->quantity_reserved);

            // Create movement log
            $inventory->transactions()->create([
                'transaction_type' => $reason,
                'previous_quantity' => $prevQty,
                'quantity' => $delta,
                'balance_after' => $balanceAfter,
                'reference_type' => 'admin_adjustment',
                'reference_id' => $inventory->id,
                'remarks' => $remarks ?: "Manual adjustment: {$mode} " . abs($delta) . " units. Reason: {$reason}.",
                'created_by' => $userId,
                'created_at' => now(),
            ]);

            return $inventory;
        });
    }

    /**
     * Global Inventory Movements / History log.
     */
    public function getMovementsHistory(Request $request)
    {
        $query = InventoryTransaction::with([
            'inventory.productVariant.product',
            'creator',
        ])->orderByDesc('id');

        if ($type = $request->input('type')) {
            $query->where('transaction_type', $type);
        }

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('remarks', 'like', "%{$search}%")
                  ->orWhereHas('inventory.productVariant.product', fn ($qp) => $qp->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('inventory.productVariant', fn ($qv) => $qv->where('sku', 'like', "%{$search}%"));
            });
        }

        if ($dateFrom = $request->input('date_from')) {
            $query->where('created_at', '>=', Carbon::parse($dateFrom)->startOfDay());
        }

        if ($dateTo = $request->input('date_to')) {
            $query->where('created_at', '<=', Carbon::parse($dateTo)->endOfDay());
        }

        return $query->paginate(25)->withQueryString();
    }

    /**
     * Automated Stock Reduction when Order is marked Paid.
     */
    public function reduceStockForOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->orderItems as $item) {
                $inventory = Inventory::where('product_variant_id', $item->product_variant_id)->first();
                if (! $inventory) {
                    continue;
                }

                $prevQty = (int) $inventory->quantity_on_hand;
                $deductQty = (int) $item->quantity;
                $newQty = max(0, $prevQty - $deductQty);

                $inventory->quantity_on_hand = $newQty;
                // If it was reserved, release reservation
                if ($inventory->quantity_reserved >= $deductQty) {
                    $inventory->quantity_reserved -= $deductQty;
                }
                $inventory->save();

                $balanceAfter = max(0, $newQty - $inventory->quantity_reserved);

                $inventory->transactions()->create([
                    'transaction_type' => 'sale',
                    'previous_quantity' => $prevQty,
                    'quantity' => -$deductQty,
                    'balance_after' => $balanceAfter,
                    'reference_type' => 'order',
                    'reference_id' => $order->id,
                    'remarks' => "Order #{$order->order_number} paid: deducted {$deductQty} unit(s).",
                    'created_by' => auth()->id() ?: 1,
                    'created_at' => now(),
                ]);
            }
        });
    }

    /**
     * Automated Stock Restoration when Order is Cancelled or Returned.
     */
    public function restoreStockForOrder(Order $order, string $reason = 'cancelled'): void
    {
        DB::transaction(function () use ($order, $reason) {
            foreach ($order->orderItems as $item) {
                $inventory = Inventory::where('product_variant_id', $item->product_variant_id)->first();
                if (! $inventory) {
                    continue;
                }

                $prevQty = (int) $inventory->quantity_on_hand;
                $restoreQty = (int) $item->quantity;
                $newQty = $prevQty + $restoreQty;

                $inventory->quantity_on_hand = $newQty;
                $inventory->save();

                $balanceAfter = max(0, $newQty - $inventory->quantity_reserved);

                $inventory->transactions()->create([
                    'transaction_type' => $reason === 'refund' ? 'refund' : 'cancellation',
                    'previous_quantity' => $prevQty,
                    'quantity' => +$restoreQty,
                    'balance_after' => $balanceAfter,
                    'reference_type' => 'order',
                    'reference_id' => $order->id,
                    'remarks' => "Order #{$order->order_number} {$reason}: restored {$restoreQty} unit(s).",
                    'created_by' => auth()->id() ?: 1,
                    'created_at' => now(),
                ]);
            }
        });
    }

    /**
     * Automated Stock Restoration when Return/Refund is processed.
     */
    public function restoreStockForReturn(OrderReturn $return): void
    {
        if ($return->order) {
            $this->restoreStockForOrder($return->order, 'refund');
        }
    }

    /**
     * Dataset generator for CSV/Excel export.
     */
    public function getExportDataset(Request $request): array
    {
        $headers = [
            'Inventory ID',
            'Product Name',
            'SKU',
            'Warehouse',
            'On Hand',
            'Reserved',
            'Available',
            'Min Level',
            'Max Level',
            'Reorder Level',
            'Status',
            'Cost Price (INR)',
            'Inventory Value (INR)',
        ];

        // Fetch all matching records without pagination limit
        $data = $this->getInventoryDashboard($request->merge(['limit' => 10000]));
        $items = $data['items'];

        $rows = [];
        foreach ($items as $i) {
            $variant = $i->productVariant;
            $product = $variant?->product;
            $costPrice = (float) ($product?->cost_price ?: 0);
            $invVal = $i->quantity_on_hand * $costPrice;

            $rows[] = [
                $i->id,
                $product?->name ?? 'Unknown Product',
                $variant?->sku ?? '—',
                $i->warehouse_code ?? 'DEFAULT',
                $i->quantity_on_hand,
                $i->quantity_reserved,
                $i->available_stock,
                $i->min_level ?? 5,
                $i->max_level ?? 1000,
                $i->reorder_level ?? 10,
                ucfirst(str_replace('_', ' ', $i->stock_status)),
                number_format($costPrice, 2, '.', ''),
                number_format($invVal, 2, '.', ''),
            ];
        }

        return [
            'headers' => $headers,
            'rows' => $rows,
        ];
    }
}
