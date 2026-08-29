<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE shipments (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    order_id BIGINT UNSIGNED NOT NULL,
    shipment_number VARCHAR(60) NOT NULL,
    courier VARCHAR(100),
    tracking_number VARCHAR(150),
    status VARCHAR(40) NOT NULL DEFAULT 'pending',
    pickup_date DATETIME,
    dispatch_date DATETIME,
    expected_delivery DATETIME,
    delivered_at DATETIME,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_shipments_shipment_number (shipment_number),
    KEY idx_shipments_order_id (order_id),
    KEY idx_shipments_tracking_number (tracking_number),
    KEY idx_shipments_status (status),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `shipments`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
