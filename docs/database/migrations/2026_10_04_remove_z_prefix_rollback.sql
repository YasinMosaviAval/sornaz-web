-- Restore the first prefix release, with its matching runtime mapping.
RENAME TABLE
  `f_legacy_settings` TO `f_z_settings`,
  `f_user_profiles` TO `f_z_user_profiles`,
  `f_user_settings` TO `f_z_user_settings`;
