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
        Schema::table('customer_addresses', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_addresses', 'email')) {
                $table->string('email', 150)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('customer_addresses', 'area')) {
                $table->string('area', 100)->nullable()->after('landmark');
            }
            if (!Schema::hasColumn('customer_addresses', 'district')) {
                $table->string('district', 100)->nullable()->after('city');
            }
            if (!Schema::hasColumn('customer_addresses', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable()->after('country');
            }
            if (!Schema::hasColumn('customer_addresses', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('customer_addresses', 'delivery_instructions')) {
                $table->string('delivery_instructions', 500)->nullable()->after('longitude');
            }
            if (!Schema::hasColumn('customer_addresses', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('is_default');
            }

            // Indexes for high-performance searching
            $table->index(['country', 'state', 'city'], 'idx_cust_addr_country_state_city');
            $table->index('district', 'idx_cust_addr_district');
            $table->index('area', 'idx_cust_addr_area');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_addresses', function (Blueprint $table) {
            $table->dropIndex('idx_cust_addr_country_state_city');
            $table->dropIndex('idx_cust_addr_district');
            $table->dropIndex('idx_cust_addr_area');

            $table->dropColumn([
                'email',
                'area',
                'district',
                'latitude',
                'longitude',
                'delivery_instructions',
                'is_verified',
            ]);
        });
    }
};
