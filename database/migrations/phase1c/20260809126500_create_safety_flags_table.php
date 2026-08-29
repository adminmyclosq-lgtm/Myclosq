<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE safety_flags (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    unusual_event_id BIGINT UNSIGNED,
    flag_type VARCHAR(50) NOT NULL,
    trigger_source VARCHAR(50),
    detected_keyword VARCHAR(100),
    severity VARCHAR(30) NOT NULL,
    automation_paused BOOLEAN NOT NULL DEFAULT TRUE,
    manual_review_required BOOLEAN NOT NULL DEFAULT TRUE,
    reviewed_by BIGINT UNSIGNED,
    reviewed_at DATETIME,
    resolution VARCHAR(1000),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_safety_flags_reset_profile_id (reset_profile_id),
    KEY idx_safety_flags_unusual_event_id (unusual_event_id),
    KEY idx_safety_flags_flag_type (flag_type),
    KEY idx_safety_flags_severity (severity),
    KEY idx_safety_flags_manual_review (manual_review_required),
    KEY idx_safety_flags_created_at (created_at),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (unusual_event_id) REFERENCES unusual_events(id) ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `safety_flags`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
