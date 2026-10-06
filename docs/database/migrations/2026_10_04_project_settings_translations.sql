-- Only for a database already using the initial f_ names below.
-- The corrected main table-prefix migration directly uses p_settings/p_translations.
RENAME TABLE
  `f_content_settings` TO `p_settings`,
  `f_entity_translations` TO `p_translations`;
