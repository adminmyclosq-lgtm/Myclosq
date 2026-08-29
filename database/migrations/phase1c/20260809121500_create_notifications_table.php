<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE notifications (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    channel VARCHAR(30) NOT NULL,
    notification_type VARCHAR(60) NOT NULL,
    reference_type VARCHAR(60),
    reference_id BIGINT UNSIGNED,
    subject VARCHAR(255),
    body TEXT,
    status VARCHAR(30) NOT NULL DEFAULT 'queued',
    scheduled_at DATETIME,
    sent_at DATETIME,
    failed_at DATETIME,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_notifications_user_id (user_id),
    KEY idx_notifications_channel (channel),
    KEY idx_notifications_type (notification_type),
    KEY idx_notifications_reference (reference_type,reference_id),
    KEY idx_notifications_status (status),
    KEY idx_notifications_scheduled_at (scheduled_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `notifications`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
