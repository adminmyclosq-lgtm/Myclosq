<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE positive_events (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    reset_day TINYINT UNSIGNED NOT NULL,
    event_type VARCHAR(100) NOT NULL,
    context_type VARCHAR(100),
    notes VARCHAR(1000),
    recorded_via VARCHAR(30),
    event_date DATE NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_positive_events_reset_profile_id (reset_profile_id),
    KEY idx_positive_events_reset_day (reset_day),
    KEY idx_positive_events_event_type (event_type),
    KEY idx_positive_events_event_date (event_date),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CHECK (reset_day BETWEEN 1 AND 30)
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `positive_events`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
