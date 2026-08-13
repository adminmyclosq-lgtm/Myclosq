<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE commercial_intents (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    paid_repeat_intent VARCHAR(60),
    price_band VARCHAR(60),
    recommendation_intent VARCHAR(30),
    phase2_interest BOOLEAN,
    continue_repeat_intent VARCHAR(50),
    referral_intent VARCHAR(30),
    captured_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_commercial_intents_reset_profile_id (reset_profile_id),
    KEY idx_commercial_intents_paid_repeat (paid_repeat_intent),
    KEY idx_commercial_intents_price_band (price_band),
    KEY idx_commercial_intents_recommendation (recommendation_intent),
    KEY idx_commercial_intents_phase2 (phase2_interest),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `commercial_intents`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
