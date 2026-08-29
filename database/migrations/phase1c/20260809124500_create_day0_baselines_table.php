<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE day0_baselines (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    success_expectation_primary VARCHAR(100),
    success_expectation_open_text VARCHAR(1000),
    regularity_frequency VARCHAR(40),
    rescue_remedy_used VARCHAR(50),
    rescue_remedy_detail VARCHAR(255),
    recent_disruption_yes_no BOOLEAN,
    recent_disruption_detail VARCHAR(1000),
    safety_acknowledged BOOLEAN NOT NULL DEFAULT FALSE,
    medical_disclaimer_acknowledged BOOLEAN NOT NULL DEFAULT FALSE,
    whatsapp_opt_in BOOLEAN NOT NULL DEFAULT FALSE,
    completed_at DATETIME,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_day0_baselines_reset_profile_id (reset_profile_id),
    KEY idx_day0_baselines_completed_at (completed_at),
    KEY idx_day0_baselines_whatsapp_opt_in (whatsapp_opt_in),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `day0_baselines`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
