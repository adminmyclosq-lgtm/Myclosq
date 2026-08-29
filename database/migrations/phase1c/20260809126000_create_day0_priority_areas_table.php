<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE day0_priority_areas (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    day0_baseline_id BIGINT UNSIGNED NOT NULL,
    priority_area VARCHAR(100) NOT NULL,
    custom_text VARCHAR(255),
    priority_rank TINYINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_day0_priority_rank (day0_baseline_id,priority_rank),
    KEY idx_day0_priority_area (priority_area),
    FOREIGN KEY (day0_baseline_id) REFERENCES day0_baselines(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CHECK (priority_rank BETWEEN 1 AND 3)
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `day0_priority_areas`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
