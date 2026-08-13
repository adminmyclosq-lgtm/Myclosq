<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE reset_profiles (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    source_channel VARCHAR(50),
    cohort VARCHAR(100),
    product_batch_id BIGINT UNSIGNED,
    current_reset_day SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    status VARCHAR(40) NOT NULL DEFAULT 'created',
    actual_start_date DATE,
    day0_completed_at DATETIME,
    day30_completed_at DATETIME,
    manual_review_required BOOLEAN NOT NULL DEFAULT FALSE,
    safety_flag_active BOOLEAN NOT NULL DEFAULT FALSE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_reset_profiles_user_id (user_id),
    KEY idx_reset_profiles_source_channel (source_channel),
    KEY idx_reset_profiles_cohort (cohort),
    KEY idx_reset_profiles_product_batch_id (product_batch_id),
    KEY idx_reset_profiles_current_day (current_reset_day),
    KEY idx_reset_profiles_status (status),
    KEY idx_reset_profiles_start_date (actual_start_date),
    KEY idx_reset_profiles_manual_review (manual_review_required),
    KEY idx_reset_profiles_safety (safety_flag_active),
    FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (product_batch_id) REFERENCES product_batches(id) ON UPDATE CASCADE ON DELETE SET NULL,
    CHECK (current_reset_day BETWEEN 0 AND 30)
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `reset_profiles`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
