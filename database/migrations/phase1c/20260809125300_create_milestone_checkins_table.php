<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE milestone_checkins (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    milestone_day TINYINT UNSIGNED NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'pending',
    started_at DATETIME,
    completed_at DATETIME,
    completion_channel VARCHAR(30),
    summary_generated BOOLEAN NOT NULL DEFAULT FALSE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_milestone_checkins_profile_day (reset_profile_id,milestone_day),
    KEY idx_milestone_checkins_day (milestone_day),
    KEY idx_milestone_checkins_status (status),
    KEY idx_milestone_checkins_completed_at (completed_at),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CHECK (milestone_day IN (3,7,14,21,30))
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `milestone_checkins`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
