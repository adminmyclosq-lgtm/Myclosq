<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE dropoff_events (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    detected_day TINYINT UNSIGNED NOT NULL,
    last_activity_at DATETIME,
    dropoff_reason VARCHAR(100),
    reminder_fatigue BOOLEAN NOT NULL DEFAULT FALSE,
    product_discomfort BOOLEAN NOT NULL DEFAULT FALSE,
    detected_automatically BOOLEAN NOT NULL DEFAULT TRUE,
    reactivation_prompt_sent BOOLEAN NOT NULL DEFAULT FALSE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_dropoff_events_reset_profile_id (reset_profile_id),
    KEY idx_dropoff_events_detected_day (detected_day),
    KEY idx_dropoff_events_reason (dropoff_reason),
    KEY idx_dropoff_events_created_at (created_at),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CHECK (detected_day BETWEEN 1 AND 30)
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `dropoff_events`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
