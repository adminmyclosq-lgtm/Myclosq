<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE day30_decisions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    classification_id BIGINT UNSIGNED NOT NULL,
    user_recommendation VARCHAR(1000),
    commercial_action VARCHAR(500),
    next_step_code VARCHAR(80) NOT NULL,
    continuation_allowed BOOLEAN NOT NULL DEFAULT FALSE,
    upsell_allowed BOOLEAN NOT NULL DEFAULT FALSE,
    testimonial_request_allowed BOOLEAN NOT NULL DEFAULT FALSE,
    restart_allowed BOOLEAN NOT NULL DEFAULT FALSE,
    doctor_guidance_required BOOLEAN NOT NULL DEFAULT FALSE,
    generated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_day30_decisions_reset_profile_id (reset_profile_id),
    KEY idx_day30_decisions_classification_id (classification_id),
    KEY idx_day30_decisions_next_step_code (next_step_code),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (classification_id) REFERENCES final_classifications(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `day30_decisions`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
