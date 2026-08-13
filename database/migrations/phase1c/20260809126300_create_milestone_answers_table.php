<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE milestone_answers (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    milestone_checkin_id BIGINT UNSIGNED NOT NULL,
    question_code VARCHAR(100) NOT NULL,
    answer_type VARCHAR(30) NOT NULL,
    answer_text TEXT,
    answer_number DECIMAL(10,2),
    answer_boolean BOOLEAN,
    answer_json JSON,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_milestone_answers_question (milestone_checkin_id,question_code),
    KEY idx_milestone_answers_question_code (question_code),
    FOREIGN KEY (milestone_checkin_id) REFERENCES milestone_checkins(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `milestone_answers`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
