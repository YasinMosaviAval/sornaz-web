-- Apply before uploading the PHP files that read is_panel_visible.
-- Existing catalog entries stay visible until the site administrator changes them.
ALTER TABLE access_system_roles
  ADD COLUMN is_panel_visible TINYINT(1) NOT NULL DEFAULT 1;

ALTER TABLE access_system_permissions
  ADD COLUMN is_panel_visible TINYINT(1) NOT NULL DEFAULT 1;
