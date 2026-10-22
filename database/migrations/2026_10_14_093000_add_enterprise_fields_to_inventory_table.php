<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('inventory')) {
            Schema::table('inventory', function (Blueprint $table) {
                if (!Schema::hasColumn('inventory', 'min_level')) {
                    $table->integer('min_level')->default(5)->after('reorder_level');
                }
                if (!Schema::hasColumn('inventory', 'max_level')) {
                    $table->integer('max_level')->default(1000)->after('min_level');
                }
            });
        }

        if (Schema::hasTable('inventory_transactions')) {
            Schema::table('inventory_transactions', function (Blueprint $table) {
                if (!Schema::hasColumn('inventory_transactions', 'previous_quantity')) {
                    $table->integer('previous_quantity')->nullable()->after('quantity');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('inventory')) {
            Schema::table('inventory', function (Blueprint $table) {
                if (Schema::hasColumn('inventory', 'min_level')) {
                    $table->dropColumn('min_level');
                }
                if (Schema::hasColumn('inventory', 'max_level')) {
                    $table->dropColumn('max_level');
                }
            });
        }

        if (Schema::hasTable('inventory_transactions')) {
            Schema::table('inventory_transactions', function (Blueprint $table) {
                if (Schema::hasColumn('inventory_transactions', 'previous_quantity')) {
                    $table->dropColumn('previous_quantity');
                }
            });
        }
    }
};
