<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE inventory_transactions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    inventory_id BIGINT UNSIGNED NOT NULL,
    transaction_type VARCHAR(40) NOT NULL,
    quantity INT NOT NULL,
    reference_type VARCHAR(50),
    reference_id BIGINT UNSIGNED,
    balance_after INT NOT NULL,
    remarks VARCHAR(500),
    created_by BIGINT UNSIGNED,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_inventory_transactions_inventory_id (inventory_id),
    KEY idx_inventory_transactions_type (transaction_type),
    KEY idx_inventory_transactions_reference (reference_type,reference_id),
    KEY idx_inventory_transactions_created_by (created_by),
    KEY idx_inventory_transactions_created_at (created_at),
    FOREIGN KEY (inventory_id) REFERENCES inventory(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL,
    CHECK (balance_after >= 0)
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `inventory_transactions`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
