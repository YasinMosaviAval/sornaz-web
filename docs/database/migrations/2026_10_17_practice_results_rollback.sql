-- Destructive: only after exporting these new practice records and disabling
-- the new API. Never run automatically on a production database.
DROP TABLE IF EXISTS `f_practice_answers`;
DROP TABLE IF EXISTS `f_practice_sessions`;
DROP TABLE IF EXISTS `f_practice_legacy_progress`;
