<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('product_variants') || ! Schema::hasTable('product_batches') || ! Schema::hasTable('inventory')) {
            return;
        }

        DB::table('product_variants')->orderBy('id')->each(function (object $variant): void {
            $legacyBatchNumber = $variant->sku.'-OPENING';
            $batchNumber = 'OPENING-'.$variant->id;
            $batch = DB::table('product_batches')
                ->where('product_variant_id', $variant->id)
                ->whereIn('batch_number', [$legacyBatchNumber, $batchNumber])
                ->first();

            $batchId = $batch?->id ?? DB::table('product_batches')->insertGetId([
                'product_variant_id' => $variant->id,
                'batch_number' => $batchNumber,
                'quantity_received' => 0,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('inventory')->updateOrInsert(
                ['product_variant_id' => $variant->id, 'batch_id' => $batchId, 'warehouse_code' => 'DEFAULT'],
                ['quantity_on_hand' => 0, 'quantity_reserved' => 0, 'reorder_level' => 0, 'status' => 'active', 'updated_at' => now()]
            );
        });
    }

    public function down(): void
    {
    }
};