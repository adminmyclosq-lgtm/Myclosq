<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE whatsapp_messages (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    whatsapp_contact_id BIGINT UNSIGNED NOT NULL,
    direction VARCHAR(10) NOT NULL,
    message_type VARCHAR(40) NOT NULL,
    template_name VARCHAR(100),
    message_body TEXT,
    provider_message_id VARCHAR(150),
    status VARCHAR(30) NOT NULL DEFAULT 'queued',
    sent_at DATETIME,
    delivered_at DATETIME,
    read_at DATETIME,
    failed_at DATETIME,
    error_code VARCHAR(50),
    raw_payload JSON,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_whatsapp_messages_provider_message_id (provider_message_id),
    KEY idx_whatsapp_messages_contact_id (whatsapp_contact_id),
    KEY idx_whatsapp_messages_direction (direction),
    KEY idx_whatsapp_messages_status (status),
    KEY idx_whatsapp_messages_created_at (created_at),
    FOREIGN KEY (whatsapp_contact_id) REFERENCES whatsapp_contacts(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `whatsapp_messages`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
