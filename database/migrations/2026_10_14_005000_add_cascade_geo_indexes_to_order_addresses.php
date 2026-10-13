<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to add optimized covering indexes for location cascading and filtering.
     */
    public function up(): void
    {
        Schema::table('order_addresses', function (Blueprint $table) {
            // Composite covering index for Country -> State -> City cascading
            $table->index(['type', 'country', 'state', 'city'], 'idx_order_addr_type_geo_cascade');

            // Composite covering index for City -> Area -> Postal code lookup
            $table->index(['type', 'city', 'area', 'postal_code'], 'idx_order_addr_type_locality');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_addresses', function (Blueprint $table) {
            $table->dropIndex('idx_order_addr_type_geo_cascade');
            $table->dropIndex('idx_order_addr_type_locality');
        });
    }
};
