-- Required for full HTML post bodies; TEXT is limited to 65,535 bytes.
ALTER TABLE translations MODIFY COLUMN value LONGTEXT NULL;
