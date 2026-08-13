<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE orders (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    uuid CHAR(36) NOT NULL,
    order_number VARCHAR(40) NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    coupon_id BIGINT UNSIGNED,
    currency CHAR(3) NOT NULL DEFAULT 'INR',
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    discount_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    tax_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    shipping_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    shipping_method_id BIGINT UNSIGNED NULL,
    shipping_method_name_snapshot VARCHAR(120) NULL,
    grand_total DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    payment_status VARCHAR(30) NOT NULL DEFAULT 'pending',
    fulfilment_status VARCHAR(30) NOT NULL DEFAULT 'pending',
    shipment_status VARCHAR(30) NOT NULL DEFAULT 'pending',
    billing_address_json JSON,
    shipping_address_json JSON,
    customer_note VARCHAR(1000),
    placed_at DATETIME,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_orders_uuid (uuid),
    UNIQUE KEY uq_orders_order_number (order_number),
    KEY idx_orders_user_id (user_id),
    KEY idx_orders_coupon_id (coupon_id),
    KEY idx_orders_shipping_method_id (shipping_method_id),
    KEY idx_orders_payment_status (payment_status),
    KEY idx_orders_fulfilment_status (fulfilment_status),
    KEY idx_orders_shipment_status (shipment_status),
    KEY idx_orders_placed_at (placed_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (shipping_method_id) REFERENCES shipping_methods(id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CHECK (subtotal >= 0 AND discount_amount >= 0 AND tax_amount >= 0 AND shipping_amount >= 0 AND grand_total >= 0)
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `orders`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
