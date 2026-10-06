-- For databases where the FIRST table-prefix migration was already applied.
-- The corrected main migration directly creates these names; do not run both.
RENAME TABLE
  `f_z_settings` TO `f_legacy_settings`,
  `f_z_user_profiles` TO `f_user_profiles`,
  `f_z_user_settings` TO `f_user_settings`;
