<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE cms_pages (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(220) NOT NULL,
    page_type VARCHAR(50) NOT NULL,
    meta_title VARCHAR(255),
    meta_description VARCHAR(500),
    status VARCHAR(30) NOT NULL DEFAULT 'draft',
    published_at DATETIME,
    created_by BIGINT UNSIGNED,
    updated_by BIGINT UNSIGNED,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_cms_pages_slug (slug),
    KEY idx_cms_pages_page_type (page_type),
    KEY idx_cms_pages_status (status),
    KEY idx_cms_pages_created_by (created_by),
    KEY idx_cms_pages_updated_by (updated_by),
    FOREIGN KEY (created_by) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `cms_pages`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
