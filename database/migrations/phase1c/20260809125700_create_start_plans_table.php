<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE start_plans (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    selected_start_date DATE NOT NULL,
    actual_start_date DATE,
    capsule_timing VARCHAR(80),
    custom_capsule_time TIME,
    capsule_card_location VARCHAR(100),
    custom_location VARCHAR(255),
    reminder_style VARCHAR(40) NOT NULL DEFAULT 'milestone_only',
    reminder_time TIME,
    daily_reminder_enabled BOOLEAN NOT NULL DEFAULT FALSE,
    milestone_reminder_enabled BOOLEAN NOT NULL DEFAULT TRUE,
    missed_day_rule_confirmed BOOLEAN NOT NULL DEFAULT FALSE,
    unusual_day_rule_confirmed BOOLEAN NOT NULL DEFAULT FALSE,
    positive_shift_rule_confirmed BOOLEAN NOT NULL DEFAULT FALSE,
    completed_at DATETIME,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_start_plans_reset_profile_id (reset_profile_id),
    KEY idx_start_plans_selected_start_date (selected_start_date),
    KEY idx_start_plans_actual_start_date (actual_start_date),
    KEY idx_start_plans_daily_reminder (daily_reminder_enabled),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `start_plans`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
