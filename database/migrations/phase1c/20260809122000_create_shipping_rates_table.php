<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE shipping_rates (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    shipping_method_id BIGINT UNSIGNED NOT NULL,
    country VARCHAR(100) NOT NULL DEFAULT 'India',
    state VARCHAR(100) NULL,
    postal_code_prefix VARCHAR(10) NULL,
    min_order_value DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    max_order_value DECIMAL(12,2) NULL,
    min_weight_grams DECIMAL(12,3) NOT NULL DEFAULT 0.000,
    max_weight_grams DECIMAL(12,3) NULL,
    shipping_charge DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    free_shipping BOOLEAN NOT NULL DEFAULT FALSE,
    effective_from DATETIME NOT NULL,
    effective_to DATETIME NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_shipping_rates_method_id (shipping_method_id),
    KEY idx_shipping_rates_country_state (country, state),
    KEY idx_shipping_rates_postal_prefix (postal_code_prefix),
    KEY idx_shipping_rates_order_value (min_order_value, max_order_value),
    KEY idx_shipping_rates_weight (min_weight_grams, max_weight_grams),
    KEY idx_shipping_rates_effective (effective_from, effective_to),
    KEY idx_shipping_rates_active (is_active),
    FOREIGN KEY (shipping_method_id) REFERENCES shipping_methods(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CHECK (
        min_order_value >= 0 AND
        (max_order_value IS NULL OR max_order_value >= min_order_value) AND
        min_weight_grams >= 0 AND
        (max_weight_grams IS NULL OR max_weight_grams >= min_weight_grams) AND
        shipping_charge >= 0
    )
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `shipping_rates`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
