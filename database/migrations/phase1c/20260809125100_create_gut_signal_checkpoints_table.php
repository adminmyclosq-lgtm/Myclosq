<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE gut_signal_checkpoints (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    checkpoint_day TINYINT UNSIGNED NOT NULL,
    bloating_score TINYINT UNSIGNED,
    gas_burping_score TINYINT UNSIGNED,
    heaviness_score TINYINT UNSIGNED,
    acidity_score TINYINT UNSIGNED,
    overall_comfort_score TINYINT UNSIGNED,
    recorded_via VARCHAR(30),
    recorded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_gut_signal_checkpoint (reset_profile_id,checkpoint_day),
    KEY idx_gut_signal_checkpoint_day (checkpoint_day),
    KEY idx_gut_signal_recorded_at (recorded_at),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CHECK (checkpoint_day IN (0,3,7,14,21,30)),
    CHECK ((bloating_score IS NULL OR bloating_score BETWEEN 0 AND 10) AND (gas_burping_score IS NULL OR gas_burping_score BETWEEN 0 AND 10) AND (heaviness_score IS NULL OR heaviness_score BETWEEN 0 AND 10) AND (acidity_score IS NULL OR acidity_score BETWEEN 0 AND 10)),
    CHECK (overall_comfort_score IS NULL OR overall_comfort_score BETWEEN 1 AND 10)
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `gut_signal_checkpoints`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
