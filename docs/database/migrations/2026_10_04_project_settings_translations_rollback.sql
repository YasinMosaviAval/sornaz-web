-- Restore the initial release with its matching runtime mapping.
RENAME TABLE
  `p_settings` TO `f_content_settings`,
  `p_translations` TO `f_entity_translations`;
