<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE qr_scan_events (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    qr_code_id BIGINT UNSIGNED NOT NULL,
    reset_profile_id BIGINT UNSIGNED,
    user_id BIGINT UNSIGNED,
    qr_source VARCHAR(50),
    qr_location VARCHAR(50),
    scan_timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    source_channel VARCHAR(50),
    milestone_context VARCHAR(50),
    action_taken VARCHAR(100),
    new_user_or_existing VARCHAR(20),
    device_type VARCHAR(30),
    ip_hash CHAR(64),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_qr_scan_events_qr_code_id (qr_code_id),
    KEY idx_qr_scan_events_reset_profile_id (reset_profile_id),
    KEY idx_qr_scan_events_user_id (user_id),
    KEY idx_qr_scan_events_scan_timestamp (scan_timestamp),
    KEY idx_qr_scan_events_source_channel (source_channel),
    FOREIGN KEY (qr_code_id) REFERENCES qr_codes(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `qr_scan_events`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
