<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE final_reviews (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    capsule_consistency VARCHAR(50),
    final_tracking_usage VARCHAR(50),
    product_comfort VARCHAR(50),
    areas_improved_count TINYINT UNSIGNED NOT NULL DEFAULT 0,
    areas_unresolved_count TINYINT UNSIGNED NOT NULL DEFAULT 0,
    trigger_pattern VARCHAR(100),
    user_verdict VARCHAR(100),
    continue_repeat_intent VARCHAR(50),
    recommendation_intent VARCHAR(30),
    completed_at DATETIME,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_final_reviews_reset_profile_id (reset_profile_id),
    KEY idx_final_reviews_product_comfort (product_comfort),
    KEY idx_final_reviews_user_verdict (user_verdict),
    KEY idx_final_reviews_continue_repeat (continue_repeat_intent),
    KEY idx_final_reviews_recommendation (recommendation_intent),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `final_reviews`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
