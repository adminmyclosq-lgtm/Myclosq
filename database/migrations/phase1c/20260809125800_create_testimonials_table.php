<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE testimonials (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    consent VARCHAR(30) NOT NULL,
    display_name VARCHAR(150),
    testimonial_text TEXT,
    usage_permission VARCHAR(30),
    moderation_status VARCHAR(30) NOT NULL DEFAULT 'pending',
    published_at DATETIME,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_testimonials_reset_profile_id (reset_profile_id),
    KEY idx_testimonials_consent (consent),
    KEY idx_testimonials_moderation_status (moderation_status),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `testimonials`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
