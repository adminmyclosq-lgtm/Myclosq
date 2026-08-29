<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE grs_scores (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    day0_symptom_burden DECIMAL(6,2) NOT NULL,
    day30_symptom_burden DECIMAL(6,2) NOT NULL,
    grs_movement DECIMAL(6,2) NOT NULL,
    day0_comfort TINYINT UNSIGNED NOT NULL,
    day30_comfort TINYINT UNSIGNED NOT NULL,
    comfort_delta DECIMAL(6,2) NOT NULL,
    movement_classification VARCHAR(50) NOT NULL,
    formula_version VARCHAR(20) NOT NULL DEFAULT 'v1',
    calculated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_grs_scores_reset_profile_id (reset_profile_id),
    KEY idx_grs_scores_movement (grs_movement),
    KEY idx_grs_scores_classification (movement_classification),
    KEY idx_grs_scores_calculated_at (calculated_at),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CHECK (day0_comfort BETWEEN 1 AND 10 AND day30_comfort BETWEEN 1 AND 10)
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `grs_scores`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
