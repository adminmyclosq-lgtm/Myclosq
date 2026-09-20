<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE reset_profiles DROP FOREIGN KEY reset_profiles_ibfk_1');
        DB::statement('ALTER TABLE reset_profiles DROP INDEX uq_reset_profiles_user_id');

        DB::statement("ALTER TABLE reset_profiles
            ADD cycle_number INT UNSIGNED NOT NULL DEFAULT 1 AFTER user_id,
            ADD parent_reset_profile_id BIGINT UNSIGNED NULL AFTER product_batch_id,
            ADD KEY idx_reset_profiles_user_cycle (user_id, cycle_number),
            ADD KEY idx_reset_profiles_parent (parent_reset_profile_id),
            ADD CONSTRAINT fk_reset_profiles_parent FOREIGN KEY (parent_reset_profile_id)
                REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE SET NULL,
            ADD UNIQUE KEY uq_reset_profiles_user_cycle (user_id, cycle_number),
            ADD CONSTRAINT reset_profiles_ibfk_1 FOREIGN KEY (user_id)
                REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT");

        DB::unprepared(<<<'SQL'
CREATE TABLE reset_reentry_requests (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    previous_reset_profile_id BIGINT UNSIGNED NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'pending',
    request_reason VARCHAR(1000),
    requested_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    reviewed_by BIGINT UNSIGNED,
    reviewed_at DATETIME,
    review_notes VARCHAR(1000),
    new_reset_profile_id BIGINT UNSIGNED,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_reset_reentry_requests_user_id (user_id),
    KEY idx_reset_reentry_requests_status (status),
    KEY idx_reset_reentry_requests_previous (previous_reset_profile_id),
    KEY idx_reset_reentry_requests_new_profile (new_reset_profile_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (previous_reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (new_reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;
SQL);

        DB::statement("UPDATE reset_profiles SET cycle_number = 1 WHERE cycle_number IS NULL OR cycle_number = 0");
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS reset_reentry_requests');
        DB::statement('ALTER TABLE reset_profiles DROP FOREIGN KEY fk_reset_profiles_parent');
        DB::statement('ALTER TABLE reset_profiles DROP FOREIGN KEY reset_profiles_ibfk_1');
        DB::statement('ALTER TABLE reset_profiles DROP INDEX uq_reset_profiles_user_cycle');
        DB::statement('ALTER TABLE reset_profiles DROP INDEX idx_reset_profiles_parent');
        DB::statement('ALTER TABLE reset_profiles DROP INDEX idx_reset_profiles_user_cycle');
        DB::statement('ALTER TABLE reset_profiles DROP COLUMN parent_reset_profile_id, DROP COLUMN cycle_number');
        DB::statement('ALTER TABLE reset_profiles ADD UNIQUE KEY uq_reset_profiles_user_id (user_id)');
        DB::statement('ALTER TABLE reset_profiles ADD CONSTRAINT reset_profiles_ibfk_1 FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
