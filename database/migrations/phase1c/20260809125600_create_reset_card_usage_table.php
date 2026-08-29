<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE reset_card_usage (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    reset_day TINYINT UNSIGNED NOT NULL,
    usage_date DATE NOT NULL,
    marked_yes_no BOOLEAN NOT NULL,
    tracking_method VARCHAR(30),
    notes VARCHAR(500),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_reset_card_usage_profile_day (reset_profile_id,reset_day),
    KEY idx_reset_card_usage_date (usage_date),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CHECK (reset_day BETWEEN 1 AND 30)
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `reset_card_usage`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
