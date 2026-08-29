<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE qr_codes (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    uuid CHAR(36) NOT NULL,
    code VARCHAR(100) NOT NULL,
    source_channel VARCHAR(50),
    qr_location VARCHAR(50),
    product_id BIGINT UNSIGNED,
    milestone_day TINYINT UNSIGNED,
    campaign_code VARCHAR(100),
    target_action VARCHAR(50) NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_qr_codes_uuid (uuid),
    UNIQUE KEY uq_qr_codes_code (code),
    KEY idx_qr_codes_source_channel (source_channel),
    KEY idx_qr_codes_location (qr_location),
    KEY idx_qr_codes_product_id (product_id),
    KEY idx_qr_codes_campaign_code (campaign_code),
    KEY idx_qr_codes_active (is_active),
    FOREIGN KEY (product_id) REFERENCES products(id) ON UPDATE CASCADE ON DELETE SET NULL,
    CHECK (milestone_day IS NULL OR milestone_day IN (0,1,3,7,14,21,30))
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `qr_codes`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
