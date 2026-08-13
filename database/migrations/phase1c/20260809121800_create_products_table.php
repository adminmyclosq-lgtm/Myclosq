<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE TABLE products (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    category_id BIGINT UNSIGNED,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(220) NOT NULL,
    base_sku VARCHAR(80) NOT NULL,
    short_description VARCHAR(500),
    description LONGTEXT,
    ingredients LONGTEXT,
    benefits LONGTEXT,
    usage_instructions LONGTEXT,
    warnings LONGTEXT,
    status VARCHAR(30) NOT NULL DEFAULT 'draft',
    is_featured BOOLEAN NOT NULL DEFAULT FALSE,
    seo_title VARCHAR(255),
    seo_description VARCHAR(500),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME,
    PRIMARY KEY (id),
    UNIQUE KEY uq_products_slug (slug),
    UNIQUE KEY uq_products_base_sku (base_sku),
    KEY idx_products_category_id (category_id),
    KEY idx_products_status (status),
    KEY idx_products_is_featured (is_featured),
    KEY idx_products_deleted_at (deleted_at),
    FOREIGN KEY (category_id) REFERENCES categories(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;
SQL);
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS `products`');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
