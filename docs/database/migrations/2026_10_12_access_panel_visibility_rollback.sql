-- Run only after reverting the PHP and JavaScript files that use these columns.
ALTER TABLE access_system_roles DROP COLUMN is_panel_visible;
ALTER TABLE access_system_permissions DROP COLUMN is_panel_visible;
