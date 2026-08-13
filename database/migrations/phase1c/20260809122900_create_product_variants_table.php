<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE product_variants (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    product_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    sku VARCHAR(100) NOT NULL,
    barcode VARCHAR(100),
    unit_value DECIMAL(10,3),
    unit_label VARCHAR(30),
    weight_grams DECIMAL(10,3),
    length_cm DECIMAL(10,2),
    width_cm DECIMAL(10,2),
    height_cm DECIMAL(10,2),
    status VARCHAR(30) NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_product_variants_sku (sku),
    UNIQUE KEY uq_product_variants_barcode (barcode),
    KEY idx_product_variants_product_id (product_id),
    KEY idx_product_variants_status (status),
    FOREIGN KEY (product_id) REFERENCES products(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CHECK ((unit_value IS NULL OR unit_value >= 0) AND (weight_grams IS NULL OR weight_grams >= 0) AND (length_cm IS NULL OR length_cm >= 0) AND (width_cm IS NULL OR width_cm >= 0) AND (height_cm IS NULL OR height_cm >= 0))
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `product_variants`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
