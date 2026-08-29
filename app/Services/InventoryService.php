<?php

namespace App\Services;

use App\Models\Inventory;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function reserve(int $variantId, int $quantity, string $referenceType, int $referenceId): void
    {
        $inventory = Inventory::where('product_variant_id',$variantId)
            ->where('status','active')
            ->lockForUpdate()
            ->first();

        if (!$inventory) {
            throw new \RuntimeException('Inventory record not configured.');
        }

        $available = $inventory->quantity_on_hand - $inventory->quantity_reserved;
        if ($available < $quantity) {
            throw new \RuntimeException('Insufficient inventory.');
        }

        $inventory->quantity_reserved += $quantity;
        $inventory->save();

        $inventory->transactions()->create([
            'transaction_type'=>'reserve',
            'quantity'=>$quantity,
            'reference_type'=>$referenceType,
            'reference_id'=>$referenceId,
            'balance_after'=>$available - $quantity,
            'remarks'=>'Order inventory reservation',
            'created_by'=>auth()->id(),
            'created_at'=>now(),
        ]);
    }

    public function release(int $variantId, int $quantity, string $referenceType, int $referenceId): void
    {
        $inventory = Inventory::where('product_variant_id',$variantId)->lockForUpdate()->firstOrFail();
        $inventory->quantity_reserved = max(0, $inventory->quantity_reserved - $quantity);
        $inventory->save();

        $inventory->transactions()->create([
            'transaction_type'=>'release',
            'quantity'=>$quantity,
            'reference_type'=>$referenceType,
            'reference_id'=>$referenceId,
            'balance_after'=>$inventory->quantity_on_hand - $inventory->quantity_reserved,
            'remarks'=>'Order inventory release',
            'created_by'=>auth()->id(),
            'created_at'=>now(),
        ]);
    }
}
