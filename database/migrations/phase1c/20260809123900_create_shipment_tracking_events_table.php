<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE shipment_tracking_events (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    shipment_id BIGINT UNSIGNED NOT NULL,
    status VARCHAR(50) NOT NULL,
    location VARCHAR(200),
    event_time DATETIME NOT NULL,
    description VARCHAR(500),
    raw_payload JSON,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_shipment_tracking_events_shipment_id (shipment_id),
    KEY idx_shipment_tracking_events_status (status),
    KEY idx_shipment_tracking_events_event_time (event_time),
    FOREIGN KEY (shipment_id) REFERENCES shipments(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `shipment_tracking_events`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
