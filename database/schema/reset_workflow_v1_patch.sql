-- Gut Reset Day 0–30 workflow v1 supplemental schema patch
-- Apply this AFTER importing database/schema/phase1c_final_production_mysql.sql
-- when you are managing schema changes directly in MySQL instead of Laravel migrations.

ALTER TABLE reset_profiles
    DROP INDEX uq_reset_profiles_user_id,
    ADD cycle_number INT UNSIGNED NOT NULL DEFAULT 1 AFTER user_id,
    ADD parent_reset_profile_id BIGINT UNSIGNED NULL AFTER product_batch_id,
    ADD KEY idx_reset_profiles_user_cycle (user_id, cycle_number),
    ADD KEY idx_reset_profiles_parent (parent_reset_profile_id),
    ADD CONSTRAINT fk_reset_profiles_parent FOREIGN KEY (parent_reset_profile_id)
        REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE SET NULL,
    ADD UNIQUE KEY uq_reset_profiles_user_cycle (user_id, cycle_number);

CREATE TABLE reset_reentry_requests (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    previous_reset_profile_id BIGINT UNSIGNED NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'pending',
    request_reason VARCHAR(1000),
    requested_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    reviewed_by BIGINT UNSIGNED,
    reviewed_at DATETIME,
    review_notes VARCHAR(1000),
    new_reset_profile_id BIGINT UNSIGNED,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_reset_reentry_requests_user_id (user_id),
    KEY idx_reset_reentry_requests_status (status),
    KEY idx_reset_reentry_requests_previous (previous_reset_profile_id),
    KEY idx_reset_reentry_requests_new_profile (new_reset_profile_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (previous_reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (new_reset_profile_id) REFERENCES reset_profiles(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
