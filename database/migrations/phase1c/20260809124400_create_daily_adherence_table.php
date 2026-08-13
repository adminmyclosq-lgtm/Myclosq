<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE daily_adherence (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    reset_day TINYINT UNSIGNED NOT NULL,
    calendar_date DATE NOT NULL,
    reminder_sent BOOLEAN NOT NULL DEFAULT FALSE,
    reminder_sent_at DATETIME,
    response_status VARCHAR(30),
    responded_at DATETIME,
    followup_sent BOOLEAN NOT NULL DEFAULT FALSE,
    followup_sent_at DATETIME,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_daily_adherence_profile_day (reset_profile_id,reset_day),
    KEY idx_daily_adherence_calendar_date (calendar_date),
    KEY idx_daily_adherence_response_status (response_status),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CHECK (reset_day BETWEEN 1 AND 30)
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `daily_adherence`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
