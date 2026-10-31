-- MariaDB 10.1.1+, import the complete file into the intended database.
-- In phpMyAdmin FIRST select the application's database in the left sidebar,
-- then open that database's Import tab. Do not import from the server home page.
-- SELECT DATABASE() must return the intended database name, not NULL.
-- Back up first. DDL implicitly commits. No payment or translation rows are changed.
-- Deploy compatible access/payment services before enabling scoped proposals.
-- No anonymous block, prepared statements, result sets or foreign-key toggles.
ALTER TABLE f_music_practice_plans ADD COLUMN IF NOT EXISTS node_id BIGINT UNSIGNED NULL;
ALTER TABLE f_music_practice_orders ADD COLUMN IF NOT EXISTS node_id BIGINT UNSIGNED NULL;
