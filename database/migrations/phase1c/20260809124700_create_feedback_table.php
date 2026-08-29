<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE feedback (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    usefulness_rating VARCHAR(30),
    most_valuable_element_1 VARCHAR(80),
    most_valuable_element_2 VARCHAR(80),
    least_useful_element VARCHAR(80),
    final_feedback_text TEXT,
    submitted_at DATETIME,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_feedback_reset_profile_id (reset_profile_id),
    KEY idx_feedback_usefulness_rating (usefulness_rating),
    KEY idx_feedback_submitted_at (submitted_at),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `feedback`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
