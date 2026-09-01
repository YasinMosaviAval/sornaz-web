CREATE TABLE IF NOT EXISTS national_holidays (
    national_holiday_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    holiday_date DATE NOT NULL,
    title VARCHAR(190) NOT NULL,
    description TEXT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    updated_at DATETIME NULL,
    updated_by BIGINT UNSIGNED NULL,
    deleted_at DATETIME NULL,
    deleted_by BIGINT UNSIGNED NULL,
    PRIMARY KEY (national_holiday_id),
    UNIQUE KEY uq_national_holidays_date (holiday_date),
    KEY idx_national_holidays_active_date (status, holiday_date, deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS academy_national_holiday_settings (
    academy_id BIGINT UNSIGNED NOT NULL,
    allow_classes_on_national_holidays TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    updated_at DATETIME NULL,
    updated_by BIGINT UNSIGNED NULL,
    PRIMARY KEY (academy_id),
    CONSTRAINT fk_academy_national_holiday_settings_academy
        FOREIGN KEY (academy_id) REFERENCES academies (academy_id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
