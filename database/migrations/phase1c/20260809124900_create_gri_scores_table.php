<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE gri_scores (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    capsules_taken TINYINT UNSIGNED NOT NULL DEFAULT 0,
    day30_comfort_rating TINYINT UNSIGNED NOT NULL,
    gri_score DECIMAL(5,2) NOT NULL,
    gri_band VARCHAR(40) NOT NULL,
    formula_version VARCHAR(20) NOT NULL DEFAULT 'v1',
    calculated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_gri_scores_reset_profile_id (reset_profile_id),
    KEY idx_gri_scores_score (gri_score),
    KEY idx_gri_scores_band (gri_band),
    KEY idx_gri_scores_calculated_at (calculated_at),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CHECK (capsules_taken BETWEEN 0 AND 30),
    CHECK (day30_comfort_rating BETWEEN 1 AND 10),
    CHECK (gri_score BETWEEN 0 AND 100)
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `gri_scores`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
