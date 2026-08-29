<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE shipping_methods (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(120) NOT NULL,
    code VARCHAR(50) NOT NULL,
    courier VARCHAR(120) NULL,
    description VARCHAR(500) NULL,
    estimated_min_days TINYINT UNSIGNED NULL,
    estimated_max_days TINYINT UNSIGNED NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_shipping_methods_code (code),
    KEY idx_shipping_methods_active (is_active),
    KEY idx_shipping_methods_sort_order (sort_order),
    CHECK (
        estimated_min_days IS NULL OR
        estimated_max_days IS NULL OR
        estimated_max_days >= estimated_min_days
    )
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `shipping_methods`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
