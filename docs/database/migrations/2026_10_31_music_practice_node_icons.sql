-- MySQL: run the entire file in one connection after selecting the database.
-- Additive and safe to rerun; no titles, results or purchases are modified.
SET @sornaz_icon_sql = IF(
  EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='f_music_practice_nodes' AND COLUMN_NAME='icon_key'),
  'SELECT 1',
  'ALTER TABLE f_music_practice_nodes ADD COLUMN icon_key VARCHAR(32) CHARACTER SET ascii COLLATE ascii_bin NULL'
);
PREPARE sornaz_icon_stmt FROM @sornaz_icon_sql;
EXECUTE sornaz_icon_stmt;
DEALLOCATE PREPARE sornaz_icon_stmt;
