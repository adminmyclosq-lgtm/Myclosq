<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE product_prices (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    product_variant_id BIGINT UNSIGNED NOT NULL,
    mrp DECIMAL(12,2) NOT NULL,
    selling_price DECIMAL(12,2) NOT NULL,
    tax_percentage DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    customer_group VARCHAR(50) NOT NULL DEFAULT 'default',
    effective_from DATETIME NOT NULL,
    effective_to DATETIME,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_product_prices_variant_id (product_variant_id),
    KEY idx_product_prices_customer_group (customer_group),
    KEY idx_product_prices_effective_from (effective_from),
    KEY idx_product_prices_effective_to (effective_to),
    KEY idx_product_prices_active (product_variant_id,customer_group,is_active),
    FOREIGN KEY (product_variant_id) REFERENCES product_variants(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CHECK (mrp >= 0 AND selling_price >= 0 AND selling_price <= mrp AND tax_percentage BETWEEN 0 AND 100)
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `product_prices`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
