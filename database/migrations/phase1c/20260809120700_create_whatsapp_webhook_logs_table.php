<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE whatsapp_webhook_logs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    provider_event_id VARCHAR(150) NOT NULL,
    event_type VARCHAR(100) NOT NULL,
    phone_number VARCHAR(20),
    payload JSON NOT NULL,
    signature_verified BOOLEAN NOT NULL DEFAULT FALSE,
    processing_status VARCHAR(30) NOT NULL DEFAULT 'received',
    processed_at DATETIME,
    error_message VARCHAR(1000),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_whatsapp_webhook_logs_provider_event_id (provider_event_id),
    KEY idx_whatsapp_webhook_logs_event_type (event_type),
    KEY idx_whatsapp_webhook_logs_phone (phone_number),
    KEY idx_whatsapp_webhook_logs_processing_status (processing_status),
    KEY idx_whatsapp_webhook_logs_created_at (created_at)
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `whatsapp_webhook_logs`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
