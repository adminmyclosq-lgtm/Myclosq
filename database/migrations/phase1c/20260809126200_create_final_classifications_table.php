<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE final_classifications (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    classification_code VARCHAR(60) NOT NULL,
    gri_score_id BIGINT UNSIGNED,
    grs_score_id BIGINT UNSIGNED,
    adherence_assessment VARCHAR(50),
    product_comfort_assessment VARCHAR(50),
    priority_area_movement VARCHAR(50),
    user_verdict VARCHAR(100),
    trigger_pattern VARCHAR(100),
    safety_override BOOLEAN NOT NULL DEFAULT FALSE,
    rule_version VARCHAR(20) NOT NULL DEFAULT 'v1',
    classified_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_final_classifications_reset_profile_id (reset_profile_id),
    KEY idx_final_classifications_code (classification_code),
    KEY idx_final_classifications_gri_id (gri_score_id),
    KEY idx_final_classifications_grs_id (grs_score_id),
    KEY idx_final_classifications_safety_override (safety_override),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (gri_score_id) REFERENCES gri_scores(id) ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (grs_score_id) REFERENCES grs_scores(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `final_classifications`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
