<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE cms_sections (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    page_id BIGINT UNSIGNED NOT NULL,
    section_type VARCHAR(50) NOT NULL,
    title VARCHAR(255),
    subtitle VARCHAR(500),
    content LONGTEXT,
    media_id BIGINT UNSIGNED,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_cms_sections_page_id (page_id),
    KEY idx_cms_sections_section_type (section_type),
    KEY idx_cms_sections_media_id (media_id),
    KEY idx_cms_sections_sort_order (page_id,sort_order),
    FOREIGN KEY (page_id) REFERENCES cms_pages(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (media_id) REFERENCES media(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `cms_sections`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
