-- Select the application database before importing. Additive and rerunnable.
-- No existing discount is assigned to an organization automatically.
SET @discount_base = CASE
 WHEN EXISTS(SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='f_financial_system_discounts') THEN 'f_financial_system_discounts'
 WHEN EXISTS(SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='financial_system_discounts') THEN 'financial_system_discounts'
 ELSE NULL END;
SET @discount_scope = 'f_financial_system_discount_organizations';
SET @discount_sql = IF(@discount_base IS NULL,
 'SELECT ''Select the application database containing financial_system_discounts before migrating.'' AS migration_error',
 CONCAT('CREATE TABLE IF NOT EXISTS `',@discount_scope,'` (discount_id BIGINT UNSIGNED NOT NULL PRIMARY KEY, organization_user_id BIGINT UNSIGNED NOT NULL, created_by BIGINT UNSIGNED NULL, INDEX idx_discount_organization (organization_user_id,discount_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'));
PREPARE discount_migration FROM @discount_sql;
EXECUTE discount_migration;
DEALLOCATE PREPARE discount_migration;
