-- Select the application database and execute this entire file in one session.
-- Supports either the prefixed schema or the legacy unprefixed schema.
-- Missing/ambiguous metadata produces NOT_APPLIED and never an ALTER statement.
SET @room_table_count = (SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME IN ('p_academy_branch_classrooms','academy_branch_classrooms') AND TABLE_TYPE='BASE TABLE');
SET @room_table = (SELECT MIN(TABLE_NAME) FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME IN ('p_academy_branch_classrooms','academy_branch_classrooms') AND TABLE_TYPE='BASE TABLE');
SET @room_academy_table = IF(@room_table='p_academy_branch_classrooms','p_academies','academies');
SET @room_branch_type = (SELECT COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=@room_table AND COLUMN_NAME='branch_id');
SET @room_branch_nullable = (SELECT IS_NULLABLE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=@room_table AND COLUMN_NAME='branch_id');
SET @room_academy_type = (SELECT COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=@room_academy_table AND COLUMN_NAME='academy_id');
SET @room_has_academy = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=@room_table AND COLUMN_NAME='academy_id');
SET @room_has_deleted_at = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=@room_table AND COLUMN_NAME='deleted_at');
SET @room_error = CASE
  WHEN DATABASE() IS NULL THEN 'Select the application database in phpMyAdmin before importing the entire file.'
  WHEN @room_table_count=0 THEN 'Classroom table not found in the selected database. Expected p_academy_branch_classrooms or academy_branch_classrooms.'
  WHEN @room_table_count<>1 THEN 'Both prefixed and unprefixed classroom tables exist. Verify the live table mapping before migrating.'
  WHEN @room_branch_type IS NULL THEN 'The classroom table has no branch_id column.'
  WHEN @room_academy_type IS NULL THEN 'Matching academy table or its academy_id column was not found.'
  WHEN @room_has_deleted_at=0 THEN 'The classroom table has no deleted_at column.'
  WHEN @room_has_academy=1 AND @room_branch_nullable<>'YES' THEN 'academy_id already exists but branch_id is not nullable. Inspect the partially migrated schema before proceeding.'
  ELSE NULL
END;
SET @room_sql = CASE
  WHEN @room_error IS NOT NULL THEN 'SELECT @room_error AS migration_error'
  WHEN @room_has_academy=1 THEN 'SELECT ''ALREADY_APPLIED'' AS migration_status'
  ELSE CONCAT('ALTER TABLE `',@room_table,'` ADD COLUMN academy_id ',@room_academy_type,' NULL DEFAULT NULL, ADD INDEX idx_classroom_academy (academy_id,deleted_at), ADD CONSTRAINT fk_classroom_academy FOREIGN KEY (academy_id) REFERENCES `',@room_academy_table,'`(academy_id), MODIFY COLUMN branch_id ',@room_branch_type,' NULL DEFAULT NULL')
END;
PREPARE room_migration FROM @room_sql;
EXECUTE room_migration;
DEALLOCATE PREPARE room_migration;
SELECT DATABASE() AS selected_database, @room_table AS classroom_table,
  IF(@room_error IS NOT NULL,'NOT_APPLIED',IF(@room_has_academy=1,'ALREADY_APPLIED','APPLIED')) AS migration_status,
  @room_error AS migration_error;
