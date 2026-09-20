<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('reset_profiles')) {
            if (! Schema::hasColumn('reset_profiles', 'cycle_number')) {
                DB::statement('ALTER TABLE reset_profiles ADD COLUMN cycle_number INT UNSIGNED NOT NULL DEFAULT 1 AFTER user_id');
            }

            DB::statement('UPDATE reset_profiles SET cycle_number = 1 WHERE cycle_number IS NULL OR cycle_number = 0');

            if (! Schema::hasColumn('reset_profiles', 'parent_reset_profile_id')) {
                DB::statement('ALTER TABLE reset_profiles ADD COLUMN parent_reset_profile_id BIGINT UNSIGNED NULL AFTER product_batch_id');
            }

            DB::statement('UPDATE reset_profiles SET parent_reset_profile_id = NULL WHERE parent_reset_profile_id = 0');
        }

        if (Schema::hasTable('reset_reentry_requests')) {
            DB::statement("UPDATE reset_reentry_requests SET status = 'pending' WHERE status IS NULL OR TRIM(status) = ''");
            DB::statement('UPDATE reset_reentry_requests SET requested_at = NOW() WHERE requested_at IS NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration normalizes local data only and is intentionally non-destructive.
    }
};
