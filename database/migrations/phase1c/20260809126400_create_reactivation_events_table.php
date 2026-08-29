<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE reactivation_events (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    dropoff_event_id BIGINT UNSIGNED NOT NULL,
    prompt_sent_at DATETIME,
    response VARCHAR(50),
    response_at DATETIME,
    new_status VARCHAR(40),
    restart_requested BOOLEAN NOT NULL DEFAULT FALSE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_reactivation_events_reset_profile_id (reset_profile_id),
    KEY idx_reactivation_events_dropoff_event_id (dropoff_event_id),
    KEY idx_reactivation_events_response (response),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (dropoff_event_id) REFERENCES dropoff_events(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `reactivation_events`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
