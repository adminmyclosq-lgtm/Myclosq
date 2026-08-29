<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE payment_transactions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    payment_id BIGINT UNSIGNED NOT NULL,
    transaction_type VARCHAR(30) NOT NULL,
    gateway_transaction_id VARCHAR(150),
    gateway_status VARCHAR(50),
    amount DECIMAL(12,2) NOT NULL,
    currency CHAR(3) NOT NULL DEFAULT 'INR',
    signature_verified BOOLEAN NOT NULL DEFAULT FALSE,
    raw_response JSON,
    processed_at DATETIME,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_payment_transactions_payment_id (payment_id),
    KEY idx_payment_transactions_type (transaction_type),
    KEY idx_payment_transactions_gateway_id (gateway_transaction_id),
    KEY idx_payment_transactions_created_at (created_at),
    FOREIGN KEY (payment_id) REFERENCES payments(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CHECK (amount >= 0)
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `payment_transactions`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
