<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'brand')) {
                $table->string('brand', 150)->nullable()->after('category_id');
            }
            if (!Schema::hasColumn('products', 'product_type')) {
                $table->string('product_type', 150)->nullable()->after('brand');
            }
            if (!Schema::hasColumn('products', 'tags')) {
                $table->text('tags')->nullable()->after('product_type');
            }
            if (!Schema::hasColumn('products', 'cost_price')) {
                $table->decimal('cost_price', 12, 2)->nullable()->after('base_sku');
            }
            if (!Schema::hasColumn('products', 'track_quantity')) {
                $table->boolean('track_quantity')->default(true)->after('cost_price');
            }
            if (!Schema::hasColumn('products', 'continue_selling_out_of_stock')) {
                $table->boolean('continue_selling_out_of_stock')->default(false)->after('track_quantity');
            }
            if (!Schema::hasColumn('products', 'low_stock_threshold')) {
                $table->integer('low_stock_threshold')->default(5)->after('continue_selling_out_of_stock');
            }
            if (!Schema::hasColumn('products', 'requires_shipping')) {
                $table->boolean('requires_shipping')->default(true)->after('low_stock_threshold');
            }
            if (!Schema::hasColumn('products', 'hs_code')) {
                $table->string('hs_code', 50)->nullable()->after('requires_shipping');
            }
            if (!Schema::hasColumn('products', 'internal_notes')) {
                $table->text('internal_notes')->nullable()->after('seo_description');
            }
        });

        if (Schema::hasTable('product_images') && !Schema::hasColumn('product_images', 'product_id')) {
            Schema::table('product_images', function (Blueprint $table) {
                $table->unsignedBigInteger('product_id')->nullable()->after('id')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $cols = [
                'brand', 'product_type', 'tags', 'cost_price', 'track_quantity',
                'continue_selling_out_of_stock', 'low_stock_threshold', 'requires_shipping',
                'hs_code', 'internal_notes'
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('products', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        if (Schema::hasTable('product_images') && Schema::hasColumn('product_images', 'product_id')) {
            Schema::table('product_images', function (Blueprint $table) {
                $table->dropColumn('product_id');
            });
        }
    }
};
