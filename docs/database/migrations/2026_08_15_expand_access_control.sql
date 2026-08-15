ALTER TABLE access_system_roles
  ADD COLUMN parent_role_id BIGINT UNSIGNED NULL AFTER role_id,
  ADD COLUMN level TINYINT UNSIGNED NOT NULL DEFAULT 1 AFTER type,
  ADD COLUMN scope ENUM('platform','website','academy','branch','self') NOT NULL DEFAULT 'website' AFTER level,
  ADD INDEX idx_access_roles_parent (parent_role_id),
  ADD INDEX idx_access_roles_scope_level (scope, level);

ALTER TABLE access_system_permissions
  ADD COLUMN resource VARCHAR(100) NULL AFTER name,
  ADD COLUMN action VARCHAR(50) NULL AFTER resource,
  ADD COLUMN scope ENUM('platform','website','academy','branch','self') NOT NULL DEFAULT 'website' AFTER group_name,
  ADD COLUMN risk_level TINYINT UNSIGNED NOT NULL DEFAULT 1 AFTER scope,
  ADD INDEX idx_access_permissions_resource_action (resource, action),
  ADD INDEX idx_access_permissions_scope_risk (scope, risk_level);
