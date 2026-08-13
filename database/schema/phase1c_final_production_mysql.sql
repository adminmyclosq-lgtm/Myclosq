-- PHASE 1B — CANONICAL MYSQL 8 DDL
-- 30-Day Gut Reset + Commerce + CMS + WhatsApp + Admin
-- Generated from the Phase 1 logical schema

CREATE DATABASE IF NOT EXISTS gut_reset CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gut_reset;
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 01 users
CREATE TABLE users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    uuid CHAR(36) NOT NULL,
    mobile VARCHAR(20),
    email VARCHAR(191),
    password_hash VARCHAR(255),
    status VARCHAR(30) NOT NULL DEFAULT 'active',
    mobile_verified_at DATETIME,
    email_verified_at DATETIME,
    last_login_at DATETIME,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_uuid (uuid),
    UNIQUE KEY uq_users_mobile (mobile),
    UNIQUE KEY uq_users_email (email),
    KEY idx_users_status (status),
    KEY idx_users_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 02 roles
CREATE TABLE roles (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(80) NOT NULL,
    code VARCHAR(50) NOT NULL,
    description VARCHAR(255),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_roles_name (name),
    UNIQUE KEY uq_roles_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 03 permissions
CREATE TABLE permissions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(80) NOT NULL,
    description VARCHAR(255),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_permissions_name (name),
    UNIQUE KEY uq_permissions_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 04 role_user
CREATE TABLE role_user (
    role_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (role_id,user_id),
    KEY idx_role_user_user_id (user_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 05 permission_role
CREATE TABLE permission_role (
    role_id BIGINT UNSIGNED NOT NULL,
    permission_id BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (role_id,permission_id),
    KEY idx_permission_role_permission_id (permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 06 customer_profiles
CREATE TABLE customer_profiles (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    display_name VARCHAR(150),
    gender VARCHAR(30),
    date_of_birth DATE,
    whatsapp_opt_in BOOLEAN NOT NULL DEFAULT FALSE,
    whatsapp_opt_in_at DATETIME,
    marketing_opt_in BOOLEAN NOT NULL DEFAULT FALSE,
    source_channel VARCHAR(50),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_customer_profiles_user_id (user_id),
    KEY idx_customer_profiles_whatsapp_opt_in (whatsapp_opt_in),
    KEY idx_customer_profiles_marketing_opt_in (marketing_opt_in),
    KEY idx_customer_profiles_source_channel (source_channel),
    FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 07 customer_addresses
CREATE TABLE customer_addresses (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    address_type VARCHAR(20) NOT NULL DEFAULT 'shipping',
    recipient_name VARCHAR(150) NOT NULL,
    phone VARCHAR(20),
    address_line1 VARCHAR(255) NOT NULL,
    address_line2 VARCHAR(255),
    landmark VARCHAR(150),
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    postal_code VARCHAR(10) NOT NULL,
    country VARCHAR(100) NOT NULL DEFAULT 'India',
    is_default BOOLEAN NOT NULL DEFAULT FALSE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_customer_addresses_user_id (user_id),
    KEY idx_customer_addresses_city (city),
    KEY idx_customer_addresses_state (state),
    KEY idx_customer_addresses_postal_code (postal_code),
    KEY idx_customer_addresses_default (user_id,is_default),
    FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 08 categories
CREATE TABLE categories (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    parent_id BIGINT UNSIGNED,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(180) NOT NULL,
    description TEXT,
    image_path VARCHAR(500),
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_categories_slug (slug),
    KEY idx_categories_parent_id (parent_id),
    KEY idx_categories_sort_order (sort_order),
    KEY idx_categories_is_active (is_active),
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 09 products
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10 product_variants
CREATE TABLE product_variants (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    product_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    sku VARCHAR(100) NOT NULL,
    barcode VARCHAR(100),
    unit_value DECIMAL(10,3),
    unit_label VARCHAR(30),
    weight_grams DECIMAL(10,3),
    length_cm DECIMAL(10,2),
    width_cm DECIMAL(10,2),
    height_cm DECIMAL(10,2),
    status VARCHAR(30) NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_product_variants_sku (sku),
    UNIQUE KEY uq_product_variants_barcode (barcode),
    KEY idx_product_variants_product_id (product_id),
    KEY idx_product_variants_status (status),
    FOREIGN KEY (product_id) REFERENCES products(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CHECK ((unit_value IS NULL OR unit_value >= 0) AND (weight_grams IS NULL OR weight_grams >= 0) AND (length_cm IS NULL OR length_cm >= 0) AND (width_cm IS NULL OR width_cm >= 0) AND (height_cm IS NULL OR height_cm >= 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11 media
CREATE TABLE media (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    uuid CHAR(36) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    storage_path VARCHAR(500) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    file_size BIGINT UNSIGNED NOT NULL,
    width INT UNSIGNED,
    height INT UNSIGNED,
    alt_text VARCHAR(255),
    uploaded_by BIGINT UNSIGNED,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_media_uuid (uuid),
    KEY idx_media_uploaded_by (uploaded_by),
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12 product_images
CREATE TABLE product_images (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    product_variant_id BIGINT UNSIGNED NOT NULL,
    media_id BIGINT UNSIGNED NOT NULL,
    image_type VARCHAR(30) NOT NULL DEFAULT 'gallery',
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    alt_text VARCHAR(255),
    is_primary BOOLEAN NOT NULL DEFAULT FALSE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_product_images_variant_id (product_variant_id),
    KEY idx_product_images_media_id (media_id),
    KEY idx_product_images_type (image_type),
    KEY idx_product_images_primary (product_variant_id,is_primary),
    FOREIGN KEY (product_variant_id) REFERENCES product_variants(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (media_id) REFERENCES media(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13 product_prices
CREATE TABLE product_prices (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    product_variant_id BIGINT UNSIGNED NOT NULL,
    mrp DECIMAL(12,2) NOT NULL,
    selling_price DECIMAL(12,2) NOT NULL,
    tax_percentage DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    customer_group VARCHAR(50) NOT NULL DEFAULT 'default',
    effective_from DATETIME NOT NULL,
    effective_to DATETIME,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_product_prices_variant_id (product_variant_id),
    KEY idx_product_prices_customer_group (customer_group),
    KEY idx_product_prices_effective_from (effective_from),
    KEY idx_product_prices_effective_to (effective_to),
    KEY idx_product_prices_active (product_variant_id,customer_group,is_active),
    FOREIGN KEY (product_variant_id) REFERENCES product_variants(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CHECK (mrp >= 0 AND selling_price >= 0 AND selling_price <= mrp AND tax_percentage BETWEEN 0 AND 100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14 product_batches
CREATE TABLE product_batches (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    product_variant_id BIGINT UNSIGNED NOT NULL,
    batch_number VARCHAR(100) NOT NULL,
    manufacture_date DATE,
    expiry_date DATE,
    quantity_received INT UNSIGNED NOT NULL DEFAULT 0,
    status VARCHAR(30) NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_product_batches_batch_number (batch_number),
    KEY idx_product_batches_variant_id (product_variant_id),
    KEY idx_product_batches_expiry_date (expiry_date),
    KEY idx_product_batches_status (status),
    FOREIGN KEY (product_variant_id) REFERENCES product_variants(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CHECK (expiry_date IS NULL OR manufacture_date IS NULL OR expiry_date >= manufacture_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15 inventory
CREATE TABLE inventory (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    product_variant_id BIGINT UNSIGNED NOT NULL,
    batch_id BIGINT UNSIGNED NOT NULL,
    quantity_on_hand INT NOT NULL DEFAULT 0,
    quantity_reserved INT NOT NULL DEFAULT 0,
    reorder_level INT UNSIGNED NOT NULL DEFAULT 0,
    warehouse_code VARCHAR(50) NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_inventory_variant_batch_warehouse (product_variant_id,batch_id,warehouse_code),
    KEY idx_inventory_variant_id (product_variant_id),
    KEY idx_inventory_batch_id (batch_id),
    KEY idx_inventory_warehouse_code (warehouse_code),
    KEY idx_inventory_status (status),
    FOREIGN KEY (product_variant_id) REFERENCES product_variants(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (batch_id) REFERENCES product_batches(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CHECK (quantity_on_hand >= 0 AND quantity_reserved >= 0 AND quantity_reserved <= quantity_on_hand)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 16 inventory_transactions
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 17 coupons
CREATE TABLE coupons (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    code VARCHAR(50) NOT NULL,
    name VARCHAR(150) NOT NULL,
    description VARCHAR(500),
    discount_type VARCHAR(30) NOT NULL,
    discount_value DECIMAL(12,2) NOT NULL,
    minimum_cart_value DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    maximum_discount DECIMAL(12,2),
    starts_at DATETIME,
    expires_at DATETIME,
    usage_limit INT UNSIGNED,
    per_customer_limit INT UNSIGNED,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_coupons_code (code),
    KEY idx_coupons_starts_at (starts_at),
    KEY idx_coupons_expires_at (expires_at),
    KEY idx_coupons_is_active (is_active),
    CHECK (discount_value >= 0 AND minimum_cart_value >= 0 AND (maximum_discount IS NULL OR maximum_discount >= 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 18 coupon_products
CREATE TABLE coupon_products (
    coupon_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (coupon_id,product_id),
    KEY idx_coupon_products_product_id (product_id),
    FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 19 carts
CREATE TABLE carts (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED,
    session_token CHAR(64) NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'active',
    currency CHAR(3) NOT NULL DEFAULT 'INR',
    expires_at DATETIME,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_carts_session_token (session_token),
    KEY idx_carts_user_id (user_id),
    KEY idx_carts_status (status),
    KEY idx_carts_expires_at (expires_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 20 cart_items
CREATE TABLE cart_items (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    cart_id BIGINT UNSIGNED NOT NULL,
    product_variant_id BIGINT UNSIGNED NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    unit_price_snapshot DECIMAL(12,2) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_cart_items_cart_variant (cart_id,product_variant_id),
    KEY idx_cart_items_variant_id (product_variant_id),
    FOREIGN KEY (cart_id) REFERENCES carts(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (product_variant_id) REFERENCES product_variants(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CHECK (quantity > 0 AND unit_price_snapshot >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 21 shipping_methods
CREATE TABLE shipping_methods (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(120) NOT NULL,
    code VARCHAR(50) NOT NULL,
    courier VARCHAR(120) NULL,
    description VARCHAR(500) NULL,
    estimated_min_days TINYINT UNSIGNED NULL,
    estimated_max_days TINYINT UNSIGNED NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_shipping_methods_code (code),
    KEY idx_shipping_methods_active (is_active),
    KEY idx_shipping_methods_sort_order (sort_order),
    CHECK (
        estimated_min_days IS NULL OR
        estimated_max_days IS NULL OR
        estimated_max_days >= estimated_min_days
    )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 22 shipping_rates
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 23 orders
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 22 order_items
CREATE TABLE order_items (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    order_id BIGINT UNSIGNED NOT NULL,
    product_variant_id BIGINT UNSIGNED NOT NULL,
    product_name_snapshot VARCHAR(200) NOT NULL,
    sku_snapshot VARCHAR(100) NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    mrp_snapshot DECIMAL(12,2) NOT NULL,
    unit_price DECIMAL(12,2) NOT NULL,
    discount_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    tax_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    line_total DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_order_items_order_id (order_id),
    KEY idx_order_items_variant_id (product_variant_id),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (product_variant_id) REFERENCES product_variants(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CHECK (quantity > 0 AND mrp_snapshot >= 0 AND unit_price >= 0 AND discount_amount >= 0 AND tax_amount >= 0 AND line_total >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 23 coupon_usages
CREATE TABLE coupon_usages (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    coupon_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    order_id BIGINT UNSIGNED NOT NULL,
    discount_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    used_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_coupon_usages_coupon_id (coupon_id),
    KEY idx_coupon_usages_user_id (user_id),
    UNIQUE KEY uq_coupon_usages_order_id (order_id),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    KEY idx_coupon_usages_used_at (used_at),
    FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CHECK (discount_amount >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 24 order_status_history
CREATE TABLE order_status_history (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    order_id BIGINT UNSIGNED NOT NULL,
    status_type VARCHAR(30) NOT NULL,
    old_status VARCHAR(40),
    new_status VARCHAR(40) NOT NULL,
    remarks VARCHAR(500),
    changed_by BIGINT UNSIGNED,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_order_status_history_order_id (order_id),
    KEY idx_order_status_history_status_type (status_type),
    KEY idx_order_status_history_changed_by (changed_by),
    KEY idx_order_status_history_created_at (created_at),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 25 payments
CREATE TABLE payments (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    order_id BIGINT UNSIGNED NOT NULL,
    gateway VARCHAR(50) NOT NULL,
    gateway_order_id VARCHAR(150),
    amount DECIMAL(12,2) NOT NULL,
    currency CHAR(3) NOT NULL DEFAULT 'INR',
    status VARCHAR(30) NOT NULL DEFAULT 'pending',
    idempotency_key VARCHAR(100) NOT NULL,
    paid_at DATETIME,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_payments_order_id (order_id),
    UNIQUE KEY uq_payments_gateway_order_id (gateway_order_id),
    UNIQUE KEY uq_payments_idempotency_key (idempotency_key),
    KEY idx_payments_status (status),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CHECK (amount >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 26 payment_transactions
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 27 shipments
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 28 shipment_tracking_events
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 29 cms_pages
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 30 cms_sections
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 31 reset_profiles
CREATE TABLE reset_profiles (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    source_channel VARCHAR(50),
    cohort VARCHAR(100),
    product_batch_id BIGINT UNSIGNED,
    current_reset_day SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    status VARCHAR(40) NOT NULL DEFAULT 'created',
    actual_start_date DATE,
    day0_completed_at DATETIME,
    day30_completed_at DATETIME,
    manual_review_required BOOLEAN NOT NULL DEFAULT FALSE,
    safety_flag_active BOOLEAN NOT NULL DEFAULT FALSE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_reset_profiles_user_id (user_id),
    KEY idx_reset_profiles_source_channel (source_channel),
    KEY idx_reset_profiles_cohort (cohort),
    KEY idx_reset_profiles_product_batch_id (product_batch_id),
    KEY idx_reset_profiles_current_day (current_reset_day),
    KEY idx_reset_profiles_status (status),
    KEY idx_reset_profiles_start_date (actual_start_date),
    KEY idx_reset_profiles_manual_review (manual_review_required),
    KEY idx_reset_profiles_safety (safety_flag_active),
    FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (product_batch_id) REFERENCES product_batches(id) ON UPDATE CASCADE ON DELETE SET NULL,
    CHECK (current_reset_day BETWEEN 0 AND 30)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 32 day0_baselines
CREATE TABLE day0_baselines (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    success_expectation_primary VARCHAR(100),
    success_expectation_open_text VARCHAR(1000),
    regularity_frequency VARCHAR(40),
    rescue_remedy_used VARCHAR(50),
    rescue_remedy_detail VARCHAR(255),
    recent_disruption_yes_no BOOLEAN,
    recent_disruption_detail VARCHAR(1000),
    safety_acknowledged BOOLEAN NOT NULL DEFAULT FALSE,
    medical_disclaimer_acknowledged BOOLEAN NOT NULL DEFAULT FALSE,
    whatsapp_opt_in BOOLEAN NOT NULL DEFAULT FALSE,
    completed_at DATETIME,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_day0_baselines_reset_profile_id (reset_profile_id),
    KEY idx_day0_baselines_completed_at (completed_at),
    KEY idx_day0_baselines_whatsapp_opt_in (whatsapp_opt_in),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 33 day0_priority_areas
CREATE TABLE day0_priority_areas (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    day0_baseline_id BIGINT UNSIGNED NOT NULL,
    priority_area VARCHAR(100) NOT NULL,
    custom_text VARCHAR(255),
    priority_rank TINYINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_day0_priority_rank (day0_baseline_id,priority_rank),
    KEY idx_day0_priority_area (priority_area),
    FOREIGN KEY (day0_baseline_id) REFERENCES day0_baselines(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CHECK (priority_rank BETWEEN 1 AND 3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 34 day0_triggers
CREATE TABLE day0_triggers (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    day0_baseline_id BIGINT UNSIGNED NOT NULL,
    trigger_name VARCHAR(100) NOT NULL,
    custom_text VARCHAR(255),
    trigger_rank TINYINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_day0_trigger_rank (day0_baseline_id,trigger_rank),
    KEY idx_day0_trigger_name (trigger_name),
    FOREIGN KEY (day0_baseline_id) REFERENCES day0_baselines(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CHECK (trigger_rank BETWEEN 1 AND 3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 35 gut_signal_checkpoints
CREATE TABLE gut_signal_checkpoints (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    checkpoint_day TINYINT UNSIGNED NOT NULL,
    bloating_score TINYINT UNSIGNED,
    gas_burping_score TINYINT UNSIGNED,
    heaviness_score TINYINT UNSIGNED,
    acidity_score TINYINT UNSIGNED,
    overall_comfort_score TINYINT UNSIGNED,
    recorded_via VARCHAR(30),
    recorded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_gut_signal_checkpoint (reset_profile_id,checkpoint_day),
    KEY idx_gut_signal_checkpoint_day (checkpoint_day),
    KEY idx_gut_signal_recorded_at (recorded_at),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CHECK (checkpoint_day IN (0,3,7,14,21,30)),
    CHECK ((bloating_score IS NULL OR bloating_score BETWEEN 0 AND 10) AND (gas_burping_score IS NULL OR gas_burping_score BETWEEN 0 AND 10) AND (heaviness_score IS NULL OR heaviness_score BETWEEN 0 AND 10) AND (acidity_score IS NULL OR acidity_score BETWEEN 0 AND 10)),
    CHECK (overall_comfort_score IS NULL OR overall_comfort_score BETWEEN 1 AND 10)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 36 start_plans
CREATE TABLE start_plans (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    selected_start_date DATE NOT NULL,
    actual_start_date DATE,
    capsule_timing VARCHAR(80),
    custom_capsule_time TIME,
    capsule_card_location VARCHAR(100),
    custom_location VARCHAR(255),
    reminder_style VARCHAR(40) NOT NULL DEFAULT 'milestone_only',
    reminder_time TIME,
    daily_reminder_enabled BOOLEAN NOT NULL DEFAULT FALSE,
    milestone_reminder_enabled BOOLEAN NOT NULL DEFAULT TRUE,
    missed_day_rule_confirmed BOOLEAN NOT NULL DEFAULT FALSE,
    unusual_day_rule_confirmed BOOLEAN NOT NULL DEFAULT FALSE,
    positive_shift_rule_confirmed BOOLEAN NOT NULL DEFAULT FALSE,
    completed_at DATETIME,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_start_plans_reset_profile_id (reset_profile_id),
    KEY idx_start_plans_selected_start_date (selected_start_date),
    KEY idx_start_plans_actual_start_date (actual_start_date),
    KEY idx_start_plans_daily_reminder (daily_reminder_enabled),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 37 daily_adherence
CREATE TABLE daily_adherence (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    reset_day TINYINT UNSIGNED NOT NULL,
    calendar_date DATE NOT NULL,
    reminder_sent BOOLEAN NOT NULL DEFAULT FALSE,
    reminder_sent_at DATETIME,
    response_status VARCHAR(30),
    responded_at DATETIME,
    followup_sent BOOLEAN NOT NULL DEFAULT FALSE,
    followup_sent_at DATETIME,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_daily_adherence_profile_day (reset_profile_id,reset_day),
    KEY idx_daily_adherence_calendar_date (calendar_date),
    KEY idx_daily_adherence_response_status (response_status),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CHECK (reset_day BETWEEN 1 AND 30)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 38 reset_card_usage
CREATE TABLE reset_card_usage (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    reset_day TINYINT UNSIGNED NOT NULL,
    usage_date DATE NOT NULL,
    marked_yes_no BOOLEAN NOT NULL,
    tracking_method VARCHAR(30),
    notes VARCHAR(500),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_reset_card_usage_profile_day (reset_profile_id,reset_day),
    KEY idx_reset_card_usage_date (usage_date),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CHECK (reset_day BETWEEN 1 AND 30)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 39 qr_codes
CREATE TABLE qr_codes (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    uuid CHAR(36) NOT NULL,
    code VARCHAR(100) NOT NULL,
    source_channel VARCHAR(50),
    qr_location VARCHAR(50),
    product_id BIGINT UNSIGNED,
    milestone_day TINYINT UNSIGNED,
    campaign_code VARCHAR(100),
    target_action VARCHAR(50) NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_qr_codes_uuid (uuid),
    UNIQUE KEY uq_qr_codes_code (code),
    KEY idx_qr_codes_source_channel (source_channel),
    KEY idx_qr_codes_location (qr_location),
    KEY idx_qr_codes_product_id (product_id),
    KEY idx_qr_codes_campaign_code (campaign_code),
    KEY idx_qr_codes_active (is_active),
    FOREIGN KEY (product_id) REFERENCES products(id) ON UPDATE CASCADE ON DELETE SET NULL,
    CHECK (milestone_day IS NULL OR milestone_day IN (0,1,3,7,14,21,30))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 40 qr_scan_events
CREATE TABLE qr_scan_events (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    qr_code_id BIGINT UNSIGNED NOT NULL,
    reset_profile_id BIGINT UNSIGNED,
    user_id BIGINT UNSIGNED,
    qr_source VARCHAR(50),
    qr_location VARCHAR(50),
    scan_timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    source_channel VARCHAR(50),
    milestone_context VARCHAR(50),
    action_taken VARCHAR(100),
    new_user_or_existing VARCHAR(20),
    device_type VARCHAR(30),
    ip_hash CHAR(64),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_qr_scan_events_qr_code_id (qr_code_id),
    KEY idx_qr_scan_events_reset_profile_id (reset_profile_id),
    KEY idx_qr_scan_events_user_id (user_id),
    KEY idx_qr_scan_events_scan_timestamp (scan_timestamp),
    KEY idx_qr_scan_events_source_channel (source_channel),
    FOREIGN KEY (qr_code_id) REFERENCES qr_codes(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 41 milestone_checkins
CREATE TABLE milestone_checkins (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    milestone_day TINYINT UNSIGNED NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'pending',
    started_at DATETIME,
    completed_at DATETIME,
    completion_channel VARCHAR(30),
    summary_generated BOOLEAN NOT NULL DEFAULT FALSE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_milestone_checkins_profile_day (reset_profile_id,milestone_day),
    KEY idx_milestone_checkins_day (milestone_day),
    KEY idx_milestone_checkins_status (status),
    KEY idx_milestone_checkins_completed_at (completed_at),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CHECK (milestone_day IN (3,7,14,21,30))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 42 milestone_answers
CREATE TABLE milestone_answers (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    milestone_checkin_id BIGINT UNSIGNED NOT NULL,
    question_code VARCHAR(100) NOT NULL,
    answer_type VARCHAR(30) NOT NULL,
    answer_text TEXT,
    answer_number DECIMAL(10,2),
    answer_boolean BOOLEAN,
    answer_json JSON,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_milestone_answers_question (milestone_checkin_id,question_code),
    KEY idx_milestone_answers_question_code (question_code),
    FOREIGN KEY (milestone_checkin_id) REFERENCES milestone_checkins(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 43 positive_events
CREATE TABLE positive_events (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    reset_day TINYINT UNSIGNED NOT NULL,
    event_type VARCHAR(100) NOT NULL,
    context_type VARCHAR(100),
    notes VARCHAR(1000),
    recorded_via VARCHAR(30),
    event_date DATE NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_positive_events_reset_profile_id (reset_profile_id),
    KEY idx_positive_events_reset_day (reset_day),
    KEY idx_positive_events_event_type (event_type),
    KEY idx_positive_events_event_date (event_date),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CHECK (reset_day BETWEEN 1 AND 30)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 44 unusual_events
CREATE TABLE unusual_events (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    reset_day TINYINT UNSIGNED NOT NULL,
    symptom_type VARCHAR(100) NOT NULL,
    severity VARCHAR(30) NOT NULL,
    description VARCHAR(2000),
    product_related_yes_no BOOLEAN,
    action_taken VARCHAR(50),
    recorded_via VARCHAR(30),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_unusual_events_reset_profile_id (reset_profile_id),
    KEY idx_unusual_events_reset_day (reset_day),
    KEY idx_unusual_events_symptom_type (symptom_type),
    KEY idx_unusual_events_severity (severity),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CHECK (reset_day BETWEEN 1 AND 30)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 45 safety_flags
CREATE TABLE safety_flags (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    unusual_event_id BIGINT UNSIGNED,
    flag_type VARCHAR(50) NOT NULL,
    trigger_source VARCHAR(50),
    detected_keyword VARCHAR(100),
    severity VARCHAR(30) NOT NULL,
    automation_paused BOOLEAN NOT NULL DEFAULT TRUE,
    manual_review_required BOOLEAN NOT NULL DEFAULT TRUE,
    reviewed_by BIGINT UNSIGNED,
    reviewed_at DATETIME,
    resolution VARCHAR(1000),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_safety_flags_reset_profile_id (reset_profile_id),
    KEY idx_safety_flags_unusual_event_id (unusual_event_id),
    KEY idx_safety_flags_flag_type (flag_type),
    KEY idx_safety_flags_severity (severity),
    KEY idx_safety_flags_manual_review (manual_review_required),
    KEY idx_safety_flags_created_at (created_at),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (unusual_event_id) REFERENCES unusual_events(id) ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 46 dropoff_events
CREATE TABLE dropoff_events (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    detected_day TINYINT UNSIGNED NOT NULL,
    last_activity_at DATETIME,
    dropoff_reason VARCHAR(100),
    reminder_fatigue BOOLEAN NOT NULL DEFAULT FALSE,
    product_discomfort BOOLEAN NOT NULL DEFAULT FALSE,
    detected_automatically BOOLEAN NOT NULL DEFAULT TRUE,
    reactivation_prompt_sent BOOLEAN NOT NULL DEFAULT FALSE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_dropoff_events_reset_profile_id (reset_profile_id),
    KEY idx_dropoff_events_detected_day (detected_day),
    KEY idx_dropoff_events_reason (dropoff_reason),
    KEY idx_dropoff_events_created_at (created_at),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CHECK (detected_day BETWEEN 1 AND 30)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 47 reactivation_events
CREATE TABLE reactivation_events (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    dropoff_event_id BIGINT UNSIGNED NOT NULL,
    prompt_sent_at DATETIME,
    response VARCHAR(50),
    response_at DATETIME,
    new_status VARCHAR(40),
    restart_requested BOOLEAN NOT NULL DEFAULT FALSE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_reactivation_events_reset_profile_id (reset_profile_id),
    KEY idx_reactivation_events_dropoff_event_id (dropoff_event_id),
    KEY idx_reactivation_events_response (response),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (dropoff_event_id) REFERENCES dropoff_events(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 48 final_reviews
CREATE TABLE final_reviews (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    capsule_consistency VARCHAR(50),
    final_tracking_usage VARCHAR(50),
    product_comfort VARCHAR(50),
    areas_improved_count TINYINT UNSIGNED NOT NULL DEFAULT 0,
    areas_unresolved_count TINYINT UNSIGNED NOT NULL DEFAULT 0,
    trigger_pattern VARCHAR(100),
    user_verdict VARCHAR(100),
    continue_repeat_intent VARCHAR(50),
    recommendation_intent VARCHAR(30),
    completed_at DATETIME,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_final_reviews_reset_profile_id (reset_profile_id),
    KEY idx_final_reviews_product_comfort (product_comfort),
    KEY idx_final_reviews_user_verdict (user_verdict),
    KEY idx_final_reviews_continue_repeat (continue_repeat_intent),
    KEY idx_final_reviews_recommendation (recommendation_intent),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 49 gri_scores
CREATE TABLE gri_scores (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    capsules_taken TINYINT UNSIGNED NOT NULL DEFAULT 0,
    day30_comfort_rating TINYINT UNSIGNED NOT NULL,
    gri_score DECIMAL(5,2) NOT NULL,
    gri_band VARCHAR(40) NOT NULL,
    formula_version VARCHAR(20) NOT NULL DEFAULT 'v1',
    calculated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_gri_scores_reset_profile_id (reset_profile_id),
    KEY idx_gri_scores_score (gri_score),
    KEY idx_gri_scores_band (gri_band),
    KEY idx_gri_scores_calculated_at (calculated_at),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CHECK (capsules_taken BETWEEN 0 AND 30),
    CHECK (day30_comfort_rating BETWEEN 1 AND 10),
    CHECK (gri_score BETWEEN 0 AND 100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 50 grs_scores
CREATE TABLE grs_scores (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    day0_symptom_burden DECIMAL(6,2) NOT NULL,
    day30_symptom_burden DECIMAL(6,2) NOT NULL,
    grs_movement DECIMAL(6,2) NOT NULL,
    day0_comfort TINYINT UNSIGNED NOT NULL,
    day30_comfort TINYINT UNSIGNED NOT NULL,
    comfort_delta DECIMAL(6,2) NOT NULL,
    movement_classification VARCHAR(50) NOT NULL,
    formula_version VARCHAR(20) NOT NULL DEFAULT 'v1',
    calculated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_grs_scores_reset_profile_id (reset_profile_id),
    KEY idx_grs_scores_movement (grs_movement),
    KEY idx_grs_scores_classification (movement_classification),
    KEY idx_grs_scores_calculated_at (calculated_at),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CHECK (day0_comfort BETWEEN 1 AND 10 AND day30_comfort BETWEEN 1 AND 10)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 51 final_classifications
CREATE TABLE final_classifications (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    classification_code VARCHAR(60) NOT NULL,
    gri_score_id BIGINT UNSIGNED,
    grs_score_id BIGINT UNSIGNED,
    adherence_assessment VARCHAR(50),
    product_comfort_assessment VARCHAR(50),
    priority_area_movement VARCHAR(50),
    user_verdict VARCHAR(100),
    trigger_pattern VARCHAR(100),
    safety_override BOOLEAN NOT NULL DEFAULT FALSE,
    rule_version VARCHAR(20) NOT NULL DEFAULT 'v1',
    classified_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_final_classifications_reset_profile_id (reset_profile_id),
    KEY idx_final_classifications_code (classification_code),
    KEY idx_final_classifications_gri_id (gri_score_id),
    KEY idx_final_classifications_grs_id (grs_score_id),
    KEY idx_final_classifications_safety_override (safety_override),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (gri_score_id) REFERENCES gri_scores(id) ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (grs_score_id) REFERENCES grs_scores(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 52 day30_decisions
CREATE TABLE day30_decisions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    classification_id BIGINT UNSIGNED NOT NULL,
    user_recommendation VARCHAR(1000),
    commercial_action VARCHAR(500),
    next_step_code VARCHAR(80) NOT NULL,
    continuation_allowed BOOLEAN NOT NULL DEFAULT FALSE,
    upsell_allowed BOOLEAN NOT NULL DEFAULT FALSE,
    testimonial_request_allowed BOOLEAN NOT NULL DEFAULT FALSE,
    restart_allowed BOOLEAN NOT NULL DEFAULT FALSE,
    doctor_guidance_required BOOLEAN NOT NULL DEFAULT FALSE,
    generated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_day30_decisions_reset_profile_id (reset_profile_id),
    KEY idx_day30_decisions_classification_id (classification_id),
    KEY idx_day30_decisions_next_step_code (next_step_code),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (classification_id) REFERENCES final_classifications(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 53 commercial_intents
CREATE TABLE commercial_intents (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    paid_repeat_intent VARCHAR(60),
    price_band VARCHAR(60),
    recommendation_intent VARCHAR(30),
    phase2_interest BOOLEAN,
    continue_repeat_intent VARCHAR(50),
    referral_intent VARCHAR(30),
    captured_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_commercial_intents_reset_profile_id (reset_profile_id),
    KEY idx_commercial_intents_paid_repeat (paid_repeat_intent),
    KEY idx_commercial_intents_price_band (price_band),
    KEY idx_commercial_intents_recommendation (recommendation_intent),
    KEY idx_commercial_intents_phase2 (phase2_interest),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 54 testimonials
CREATE TABLE testimonials (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    consent VARCHAR(30) NOT NULL,
    display_name VARCHAR(150),
    testimonial_text TEXT,
    usage_permission VARCHAR(30),
    moderation_status VARCHAR(30) NOT NULL DEFAULT 'pending',
    published_at DATETIME,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_testimonials_reset_profile_id (reset_profile_id),
    KEY idx_testimonials_consent (consent),
    KEY idx_testimonials_moderation_status (moderation_status),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 55 feedback
CREATE TABLE feedback (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    usefulness_rating VARCHAR(30),
    most_valuable_element_1 VARCHAR(80),
    most_valuable_element_2 VARCHAR(80),
    least_useful_element VARCHAR(80),
    final_feedback_text TEXT,
    submitted_at DATETIME,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_feedback_reset_profile_id (reset_profile_id),
    KEY idx_feedback_usefulness_rating (usefulness_rating),
    KEY idx_feedback_submitted_at (submitted_at),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 56 admin_notes
CREATE TABLE admin_notes (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reset_profile_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    note_type VARCHAR(50) NOT NULL,
    note_text TEXT NOT NULL,
    is_internal BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_admin_notes_reset_profile_id (reset_profile_id),
    KEY idx_admin_notes_user_id (user_id),
    KEY idx_admin_notes_note_type (note_type),
    KEY idx_admin_notes_created_at (created_at),
    FOREIGN KEY (reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 57 whatsapp_contacts
CREATE TABLE whatsapp_contacts (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    whatsapp_contact_id VARCHAR(150),
    profile_name VARCHAR(150),
    opt_in BOOLEAN NOT NULL DEFAULT FALSE,
    opt_in_at DATETIME,
    status VARCHAR(30) NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_whatsapp_contacts_user_id (user_id),
    UNIQUE KEY uq_whatsapp_contacts_phone (phone_number),
    UNIQUE KEY uq_whatsapp_contacts_provider_id (whatsapp_contact_id),
    KEY idx_whatsapp_contacts_opt_in (opt_in),
    KEY idx_whatsapp_contacts_status (status),
    FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 58 whatsapp_messages
CREATE TABLE whatsapp_messages (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    whatsapp_contact_id BIGINT UNSIGNED NOT NULL,
    direction VARCHAR(10) NOT NULL,
    message_type VARCHAR(40) NOT NULL,
    template_name VARCHAR(100),
    message_body TEXT,
    provider_message_id VARCHAR(150),
    status VARCHAR(30) NOT NULL DEFAULT 'queued',
    sent_at DATETIME,
    delivered_at DATETIME,
    read_at DATETIME,
    failed_at DATETIME,
    error_code VARCHAR(50),
    raw_payload JSON,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_whatsapp_messages_provider_message_id (provider_message_id),
    KEY idx_whatsapp_messages_contact_id (whatsapp_contact_id),
    KEY idx_whatsapp_messages_direction (direction),
    KEY idx_whatsapp_messages_status (status),
    KEY idx_whatsapp_messages_created_at (created_at),
    FOREIGN KEY (whatsapp_contact_id) REFERENCES whatsapp_contacts(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 59 whatsapp_webhook_logs
CREATE TABLE whatsapp_webhook_logs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    provider_event_id VARCHAR(150) NOT NULL,
    event_type VARCHAR(100) NOT NULL,
    phone_number VARCHAR(20),
    payload JSON NOT NULL,
    signature_verified BOOLEAN NOT NULL DEFAULT FALSE,
    processing_status VARCHAR(30) NOT NULL DEFAULT 'received',
    processed_at DATETIME,
    error_message VARCHAR(1000),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_whatsapp_webhook_logs_provider_event_id (provider_event_id),
    KEY idx_whatsapp_webhook_logs_event_type (event_type),
    KEY idx_whatsapp_webhook_logs_phone (phone_number),
    KEY idx_whatsapp_webhook_logs_processing_status (processing_status),
    KEY idx_whatsapp_webhook_logs_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 60 notifications
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 61 audit_logs
CREATE TABLE audit_logs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED,
    action VARCHAR(100) NOT NULL,
    auditable_type VARCHAR(100) NOT NULL,
    auditable_id BIGINT UNSIGNED,
    old_values JSON,
    new_values JSON,
    ip_hash CHAR(64),
    user_agent VARCHAR(500),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_audit_logs_user_id (user_id),
    KEY idx_audit_logs_action (action),
    KEY idx_audit_logs_auditable (auditable_type,auditable_id),
    KEY idx_audit_logs_created_at (created_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 62 system_settings
CREATE TABLE system_settings (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    setting_group VARCHAR(80) NOT NULL,
    setting_key VARCHAR(120) NOT NULL,
    setting_value TEXT NULL,
    value_type VARCHAR(20) NOT NULL DEFAULT 'string',
    is_public BOOLEAN NOT NULL DEFAULT FALSE,
    is_encrypted BOOLEAN NOT NULL DEFAULT FALSE,
    description VARCHAR(500) NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_system_settings_key (setting_group, setting_key),
    KEY idx_system_settings_group (setting_group),
    KEY idx_system_settings_public (is_public),
    KEY idx_system_settings_updated_by (updated_by),
    FOREIGN KEY (updated_by) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 63 whatsapp_templates
CREATE TABLE whatsapp_templates (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(120) NOT NULL,
    meta_template_name VARCHAR(150) NOT NULL,
    language_code VARCHAR(20) NOT NULL DEFAULT 'en',
    category VARCHAR(50) NOT NULL,
    body TEXT NULL,
    variables_json JSON NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'draft',
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_whatsapp_templates_meta_name_language (meta_template_name, language_code),
    KEY idx_whatsapp_templates_status (status),
    KEY idx_whatsapp_templates_active (is_active),
    KEY idx_whatsapp_templates_created_by (created_by),
    KEY idx_whatsapp_templates_updated_by (updated_by),
    FOREIGN KEY (created_by) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 64 notification_templates
CREATE TABLE notification_templates (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(120) NOT NULL,
    code VARCHAR(80) NOT NULL,
    channel VARCHAR(30) NOT NULL,
    subject VARCHAR(255) NULL,
    body TEXT NOT NULL,
    variables_json JSON NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'draft',
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_notification_templates_code_channel (code, channel),
    KEY idx_notification_templates_channel (channel),
    KEY idx_notification_templates_status (status),
    KEY idx_notification_templates_active (is_active),
    KEY idx_notification_templates_created_by (created_by),
    KEY idx_notification_templates_updated_by (updated_by),
    FOREIGN KEY (created_by) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Base RBAC seed data
INSERT INTO roles (name, code, description) VALUES
('Super Admin','SUPER_ADMIN','Full system administration'),
('Content Admin','CONTENT_ADMIN','CMS and content administration'),
('Product Admin','PRODUCT_ADMIN','Product and catalogue administration'),
('Order Admin','ORDER_ADMIN','Order and payment administration'),
('Fulfilment Admin','FULFILMENT_ADMIN','Shipment and fulfilment administration'),
('Customer Support','CUSTOMER_SUPPORT','Customer support operations'),
('Marketing Admin','MARKETING_ADMIN','Marketing and commercial operations'),
('Reset Admin','RESET_ADMIN','30-day reset operations'),
('Analyst','ANALYST','Reporting and analytics');

INSERT INTO permissions (name, code, description) VALUES
('View Users','USER_VIEW','View users and customer profiles'),
('Manage Users','USER_MANAGE','Create and update users'),
('View Products','PRODUCT_VIEW','View products and catalogue'),
('Manage Products','PRODUCT_MANAGE','Create and update products'),
('View Orders','ORDER_VIEW','View orders'),
('Manage Orders','ORDER_MANAGE','Manage order lifecycle'),
('View Payments','PAYMENT_VIEW','View payment transactions'),
('Manage Inventory','INVENTORY_MANAGE','Manage inventory'),
('Manage CMS','CMS_MANAGE','Manage CMS pages and sections'),
('View Reset Profiles','RESET_VIEW','View reset journeys'),
('Manage Reset Profiles','RESET_MANAGE','Manage reset journeys'),
('Review Safety Flags','SAFETY_REVIEW','Review safety flags'),
('View Analytics','ANALYTICS_VIEW','View operational and response analytics'),
('Manage WhatsApp','WHATSAPP_MANAGE','Manage WhatsApp messaging'),
('View Audit Logs','AUDIT_VIEW','View audit logs');

-- Production reference data
INSERT INTO shipping_methods
(name, code, courier, description, estimated_min_days, estimated_max_days, is_active, sort_order)
VALUES
('Standard Delivery', 'STANDARD', NULL, 'Standard ecommerce delivery', 3, 7, TRUE, 1),
('Express Delivery', 'EXPRESS', NULL, 'Express ecommerce delivery', 1, 3, TRUE, 2);

INSERT INTO shipping_rates
(shipping_method_id, country, state, postal_code_prefix, min_order_value, max_order_value,
 min_weight_grams, max_weight_grams, shipping_charge, free_shipping, effective_from, is_active)
SELECT id, 'India', NULL, NULL, 0.00, NULL, 0.000, NULL, 0.00, TRUE, CURRENT_TIMESTAMP, TRUE
FROM shipping_methods WHERE code = 'STANDARD';

INSERT INTO shipping_rates
(shipping_method_id, country, state, postal_code_prefix, min_order_value, max_order_value,
 min_weight_grams, max_weight_grams, shipping_charge, free_shipping, effective_from, is_active)
SELECT id, 'India', NULL, NULL, 0.00, NULL, 0.000, NULL, 99.00, FALSE, CURRENT_TIMESTAMP, TRUE
FROM shipping_methods WHERE code = 'EXPRESS';

INSERT INTO system_settings
(setting_group, setting_key, setting_value, value_type, is_public, is_encrypted, description)
VALUES
('site', 'site_name', 'Gut Reset', 'string', TRUE, FALSE, 'Public site name'),
('site', 'default_currency', 'INR', 'string', TRUE, FALSE, 'Default ecommerce currency'),
('commerce', 'free_shipping_threshold', '0.00', 'decimal', TRUE, FALSE, 'Default free shipping threshold'),
('reset', 'reset_duration_days', '30', 'integer', FALSE, FALSE, 'Duration of the guided reset'),
('whatsapp', 'default_language', 'en', 'string', FALSE, FALSE, 'Default WhatsApp template language');

INSERT INTO whatsapp_templates
(name, meta_template_name, language_code, category, body, variables_json, status, is_active)
VALUES
('Reset Welcome', 'gut_reset_welcome', 'en', 'UTILITY',
 'Welcome to your Gut Reset journey.', JSON_ARRAY(), 'draft', TRUE),
('Daily Check-in', 'gut_reset_daily_checkin', 'en', 'UTILITY',
 'Your Day {{day}} check-in is ready.', JSON_ARRAY('day'), 'draft', TRUE),
('Milestone Check-in', 'gut_reset_milestone_checkin', 'en', 'UTILITY',
 'Your Day {{day}} milestone check-in is ready.', JSON_ARRAY('day'), 'draft', TRUE);

INSERT INTO notification_templates
(name, code, channel, subject, body, variables_json, status, is_active)
VALUES
('Order Confirmation', 'ORDER_CONFIRMATION', 'email', 'Order confirmation',
 'Your order {{order_number}} has been confirmed.', JSON_ARRAY('order_number'), 'draft', TRUE),
('Shipment Dispatched', 'SHIPMENT_DISPATCHED', 'email', 'Shipment dispatched',
 'Your order {{order_number}} has been dispatched. Tracking: {{tracking_number}}.',
 JSON_ARRAY('order_number', 'tracking_number'), 'draft', TRUE);

SET FOREIGN_KEY_CHECKS = 1;
