<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE whatsapp_templates (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(120) NOT NULL,
    meta_template_name VARCHAR(150) NOT NULL,
    language_code VARCHAR(20) NOT NULL DEFAULT 'en',
    category VARCHAR(50) NOT NULL,
    body TEXT NULL,
    variables_json JSON NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'draft',
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_whatsapp_templates_meta_name_language (meta_template_name, language_code),
    KEY idx_whatsapp_templates_status (status),
    KEY idx_whatsapp_templates_active (is_active),
    KEY idx_whatsapp_templates_created_by (created_by),
    KEY idx_whatsapp_templates_updated_by (updated_by),
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
        DB::statement('DROP TABLE IF EXISTS `whatsapp_templates`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
