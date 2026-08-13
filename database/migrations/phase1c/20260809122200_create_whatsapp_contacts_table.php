<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE whatsapp_contacts (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    whatsapp_contact_id VARCHAR(150),
    profile_name VARCHAR(150),
    opt_in BOOLEAN NOT NULL DEFAULT FALSE,
    opt_in_at DATETIME,
    status VARCHAR(30) NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_whatsapp_contacts_user_id (user_id),
    UNIQUE KEY uq_whatsapp_contacts_phone (phone_number),
    UNIQUE KEY uq_whatsapp_contacts_provider_id (whatsapp_contact_id),
    KEY idx_whatsapp_contacts_opt_in (opt_in),
    KEY idx_whatsapp_contacts_status (status),
    FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `whatsapp_contacts`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
