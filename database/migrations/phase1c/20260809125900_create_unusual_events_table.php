<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE unusual_events (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    reset_day TINYINT UNSIGNED NOT NULL,
    symptom_type VARCHAR(100) NOT NULL,
    severity VARCHAR(30) NOT NULL,
    description VARCHAR(2000),
    product_related_yes_no BOOLEAN,
    action_taken VARCHAR(50),
    recorded_via VARCHAR(30),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_unusual_events_reset_profile_id (reset_profile_id),
    KEY idx_unusual_events_reset_day (reset_day),
    KEY idx_unusual_events_symptom_type (symptom_type),
    KEY idx_unusual_events_severity (severity),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CHECK (reset_day BETWEEN 1 AND 30)
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `unusual_events`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
