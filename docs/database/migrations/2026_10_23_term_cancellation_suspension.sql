-- Run the entire file in the selected application database before deploying term status controls.
-- Additive and repeatable: existing ENUM values and term records are preserved.
SET @term_count=(SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME IN ('p_academy_branch_course_terms','academy_branch_course_terms') AND TABLE_TYPE='BASE TABLE');
SET @term_table=(SELECT MIN(TABLE_NAME) FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME IN ('p_academy_branch_course_terms','academy_branch_course_terms') AND TABLE_TYPE='BASE TABLE');
SET @term_type=(SELECT COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=@term_table AND COLUMN_NAME='status');
SET @term_nullable=(SELECT IS_NULLABLE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=@term_table AND COLUMN_NAME='status');
SET @term_default=(SELECT COLUMN_DEFAULT FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=@term_table AND COLUMN_NAME='status');
SET @term_comment=(SELECT COLUMN_COMMENT FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=@term_table AND COLUMN_NAME='status');
SET @term_length=(SELECT CHARACTER_MAXIMUM_LENGTH FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=@term_table AND COLUMN_NAME='status');
SET @term_error=CASE WHEN DATABASE() IS NULL THEN 'Select the application database first.' WHEN @term_count<>1 THEN 'Expected exactly one prefixed or legacy term table.' WHEN @term_type IS NULL THEN 'The term status column was not found.' WHEN @term_type NOT LIKE 'enum(%' AND @term_type NOT LIKE 'varchar(%' AND @term_type NOT LIKE 'char(%' THEN 'Unsupported status column type. Inspect the schema.' ELSE NULL END;
SET @term_new_type=@term_type;
SET @term_new_type=IF(@term_type LIKE 'enum(%' AND LOCATE('''canceled''',@term_new_type)=0,CONCAT(LEFT(@term_new_type,CHAR_LENGTH(@term_new_type)-1),',''canceled'')'),@term_new_type);
SET @term_new_type=IF(@term_type LIKE 'enum(%' AND LOCATE('''suspended''',@term_new_type)=0,CONCAT(LEFT(@term_new_type,CHAR_LENGTH(@term_new_type)-1),',''suspended'')'),@term_new_type);
SET @term_new_type=IF(@term_type NOT LIKE 'enum(%' AND @term_length<9,'varchar(32)',@term_new_type);
SET @term_sql=CASE WHEN @term_error IS NOT NULL THEN 'SELECT @term_error AS migration_error' WHEN @term_new_type=@term_type THEN 'SELECT ''ALREADY_APPLIED'' AS migration_status' ELSE CONCAT('ALTER TABLE `',@term_table,'` MODIFY COLUMN `status` ',@term_new_type,IF(@term_nullable='YES',' NULL',' NOT NULL'),IF(@term_default IS NULL,IF(@term_nullable='YES',' DEFAULT NULL',''),CONCAT(' DEFAULT ',QUOTE(TRIM(BOTH '''' FROM @term_default)))),' COMMENT ',QUOTE(COALESCE(@term_comment,''))) END;
PREPARE term_status_migration FROM @term_sql;
EXECUTE term_status_migration;
DEALLOCATE PREPARE term_status_migration;
SELECT DATABASE() AS selected_database,@term_table AS term_table,IF(@term_error IS NOT NULL,'NOT_APPLIED',IF(@term_new_type=@term_type,'ALREADY_APPLIED','APPLIED')) AS migration_status,@term_error AS migration_error;
-- Rollback: restore the previous application files first. Keep the additional ENUM values;
-- do not shrink the ENUM while canceled or suspended records exist.