-- MariaDB: select the site's database and back up before importing.
-- Additive and safe to rerun; existing icons remain unchanged.
ALTER TABLE f_music_practice_nodes ADD COLUMN IF NOT EXISTS icon_key VARCHAR(32) CHARACTER SET ascii COLLATE ascii_bin NULL;
