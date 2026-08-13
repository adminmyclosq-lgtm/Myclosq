<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE notification_templates (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(120) NOT NULL,
    code VARCHAR(80) NOT NULL,
    channel VARCHAR(30) NOT NULL,
    subject VARCHAR(255) NULL,
    body TEXT NOT NULL,
    variables_json JSON NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'draft',
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_notification_templates_code_channel (code, channel),
    KEY idx_notification_templates_channel (channel),
    KEY idx_notification_templates_status (status),
    KEY idx_notification_templates_active (is_active),
    KEY idx_notification_templates_created_by (created_by),
    KEY idx_notification_templates_updated_by (updated_by),
    FOREIGN KEY (created_by) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `notification_templates`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
