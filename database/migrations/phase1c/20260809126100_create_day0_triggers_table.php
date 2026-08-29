<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE day0_triggers (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    day0_baseline_id BIGINT UNSIGNED NOT NULL,
    trigger_name VARCHAR(100) NOT NULL,
    custom_text VARCHAR(255),
    trigger_rank TINYINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_day0_trigger_rank (day0_baseline_id,trigger_rank),
    KEY idx_day0_trigger_name (trigger_name),
    FOREIGN KEY (day0_baseline_id) REFERENCES day0_baselines(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CHECK (trigger_rank BETWEEN 1 AND 3)
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `day0_triggers`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
