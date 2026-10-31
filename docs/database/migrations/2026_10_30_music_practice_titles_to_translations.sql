-- Sornaz: music section/test titles belong exclusively to f_translations.
-- Execute the COMPLETE file in ONE database connection, never individual PREPARE lines.
-- For MariaDB/phpMyAdmin use the companion .mariadb.sql file via Import instead.
-- Run manually on the intended database AFTER deploying the compatible PHP services.
-- Back up the database first. MySQL DDL implicitly commits and is not transactional.
-- Drain concurrent music-authoring/seed requests during final schema cleanup.
-- Existing active translations win over stale cached columns.
-- No routines, runtime DDL, new tables, result changes or payment changes.
-- Safe to rerun, including a partially upgraded schema and a fresh schema.
-- Success: all final result rows have missing_translations=0 and remaining_legacy_columns=0.

-- f_music_practice_nodes: preserve existing active translations; backfill missing locales only.
SET @sornaz_mp_has_fa = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='f_music_practice_nodes' AND COLUMN_NAME='title_fa');
SET @sornaz_mp_has_en = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='f_music_practice_nodes' AND COLUMN_NAME='title_en');
SET @sornaz_mp_source = CASE
 WHEN @sornaz_mp_has_fa>0 AND @sornaz_mp_has_en>0 THEN 'SELECT id AS table_id,''fa'' AS locale,title_fa AS source_text,created_by AS actor FROM f_music_practice_nodes UNION ALL SELECT id AS table_id,''en'' AS locale,title_en AS source_text,created_by AS actor FROM f_music_practice_nodes'
 WHEN @sornaz_mp_has_fa>0 THEN 'SELECT id AS table_id,''fa'' AS locale,title_fa AS source_text,created_by AS actor FROM f_music_practice_nodes'
 WHEN @sornaz_mp_has_en>0 THEN 'SELECT id AS table_id,''en'' AS locale,title_en AS source_text,created_by AS actor FROM f_music_practice_nodes'
 ELSE 'SELECT NULL AS table_id,'''' AS locale,'''' AS source_text,1 AS actor WHERE 1=0' END;

SET @sornaz_mp_sql = CONCAT('INSERT INTO f_translations (table_name,table_id,field,locale,value,version,created_at,created_by,updated_at,updated_by) SELECT ''music_practice_nodes'',s.table_id,''title'',s.locale,s.source_text,COALESCE((SELECT MAX(h.version) FROM f_translations h WHERE h.table_name=''music_practice_nodes'' AND h.table_id=s.table_id AND h.field=''title'' AND h.locale=s.locale),0)+1,CURRENT_TIMESTAMP,COALESCE(NULLIF(s.actor,0),1),CURRENT_TIMESTAMP,COALESCE(NULLIF(s.actor,0),1) FROM (',@sornaz_mp_source,') s WHERE s.source_text IS NOT NULL AND TRIM(s.source_text)<>'''' AND NOT EXISTS (SELECT 1 FROM f_translations t WHERE t.table_name=''music_practice_nodes'' AND t.table_id=s.table_id AND t.field=''title'' AND t.locale=s.locale AND t.deleted_at IS NULL)');
PREPARE sornaz_mp_title_stmt FROM @sornaz_mp_sql;
EXECUTE sornaz_mp_title_stmt;
DEALLOCATE PREPARE sornaz_mp_title_stmt;

-- Do not drop any source column if a nonempty legacy title is still unrepresented.
SET @sornaz_mp_sql = CONCAT('SELECT COUNT(*) INTO @sornaz_mp_missing FROM (',@sornaz_mp_source,') s WHERE s.source_text IS NOT NULL AND TRIM(s.source_text)<>'''' AND NOT EXISTS (SELECT 1 FROM f_translations t WHERE t.table_name=''music_practice_nodes'' AND t.table_id=s.table_id AND t.field=''title'' AND t.locale=s.locale AND t.deleted_at IS NULL)');
PREPARE sornaz_mp_title_stmt FROM @sornaz_mp_sql;
EXECUTE sornaz_mp_title_stmt;
DEALLOCATE PREPARE sornaz_mp_title_stmt;
SET @sornaz_mp_drop = (SELECT GROUP_CONCAT(CONCAT('DROP COLUMN `',COLUMN_NAME,'`') ORDER BY ORDINAL_POSITION SEPARATOR ', ') FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='f_music_practice_nodes' AND COLUMN_NAME IN ('title_fa','title_en'));
SET @sornaz_mp_sql = IF(@sornaz_mp_missing=0 AND @sornaz_mp_drop IS NOT NULL,CONCAT('ALTER TABLE f_music_practice_nodes ',@sornaz_mp_drop),'SELECT ''f_music_practice_nodes: no drop needed or missing translations blocked cleanup'' AS migration_status');
PREPARE sornaz_mp_title_stmt FROM @sornaz_mp_sql;
EXECUTE sornaz_mp_title_stmt;
DEALLOCATE PREPARE sornaz_mp_title_stmt;
SELECT 'f_music_practice_nodes' AS migrated_table,@sornaz_mp_missing AS missing_translations,
 (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='f_music_practice_nodes' AND COLUMN_NAME IN ('title_fa','title_en')) AS remaining_legacy_columns;

-- f_music_practice_exams: preserve existing active translations; backfill missing locales only.
SET @sornaz_mp_has_fa = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='f_music_practice_exams' AND COLUMN_NAME='title_fa');
SET @sornaz_mp_has_en = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='f_music_practice_exams' AND COLUMN_NAME='title_en');
SET @sornaz_mp_source = CASE
 WHEN @sornaz_mp_has_fa>0 AND @sornaz_mp_has_en>0 THEN 'SELECT id AS table_id,''fa'' AS locale,title_fa AS source_text,owner_id AS actor FROM f_music_practice_exams UNION ALL SELECT id AS table_id,''en'' AS locale,title_en AS source_text,owner_id AS actor FROM f_music_practice_exams'
 WHEN @sornaz_mp_has_fa>0 THEN 'SELECT id AS table_id,''fa'' AS locale,title_fa AS source_text,owner_id AS actor FROM f_music_practice_exams'
 WHEN @sornaz_mp_has_en>0 THEN 'SELECT id AS table_id,''en'' AS locale,title_en AS source_text,owner_id AS actor FROM f_music_practice_exams'
 ELSE 'SELECT NULL AS table_id,'''' AS locale,'''' AS source_text,1 AS actor WHERE 1=0' END;

SET @sornaz_mp_sql = CONCAT('INSERT INTO f_translations (table_name,table_id,field,locale,value,version,created_at,created_by,updated_at,updated_by) SELECT ''music_practice_exams'',s.table_id,''title'',s.locale,s.source_text,COALESCE((SELECT MAX(h.version) FROM f_translations h WHERE h.table_name=''music_practice_exams'' AND h.table_id=s.table_id AND h.field=''title'' AND h.locale=s.locale),0)+1,CURRENT_TIMESTAMP,COALESCE(NULLIF(s.actor,0),1),CURRENT_TIMESTAMP,COALESCE(NULLIF(s.actor,0),1) FROM (',@sornaz_mp_source,') s WHERE s.source_text IS NOT NULL AND TRIM(s.source_text)<>'''' AND NOT EXISTS (SELECT 1 FROM f_translations t WHERE t.table_name=''music_practice_exams'' AND t.table_id=s.table_id AND t.field=''title'' AND t.locale=s.locale AND t.deleted_at IS NULL)');
PREPARE sornaz_mp_title_stmt FROM @sornaz_mp_sql;
EXECUTE sornaz_mp_title_stmt;
DEALLOCATE PREPARE sornaz_mp_title_stmt;

-- Do not drop any source column if a nonempty legacy title is still unrepresented.
SET @sornaz_mp_sql = CONCAT('SELECT COUNT(*) INTO @sornaz_mp_missing FROM (',@sornaz_mp_source,') s WHERE s.source_text IS NOT NULL AND TRIM(s.source_text)<>'''' AND NOT EXISTS (SELECT 1 FROM f_translations t WHERE t.table_name=''music_practice_exams'' AND t.table_id=s.table_id AND t.field=''title'' AND t.locale=s.locale AND t.deleted_at IS NULL)');
PREPARE sornaz_mp_title_stmt FROM @sornaz_mp_sql;
EXECUTE sornaz_mp_title_stmt;
DEALLOCATE PREPARE sornaz_mp_title_stmt;
SET @sornaz_mp_drop = (SELECT GROUP_CONCAT(CONCAT('DROP COLUMN `',COLUMN_NAME,'`') ORDER BY ORDINAL_POSITION SEPARATOR ', ') FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='f_music_practice_exams' AND COLUMN_NAME IN ('title_fa','title_en'));
SET @sornaz_mp_sql = IF(@sornaz_mp_missing=0 AND @sornaz_mp_drop IS NOT NULL,CONCAT('ALTER TABLE f_music_practice_exams ',@sornaz_mp_drop),'SELECT ''f_music_practice_exams: no drop needed or missing translations blocked cleanup'' AS migration_status');
PREPARE sornaz_mp_title_stmt FROM @sornaz_mp_sql;
EXECUTE sornaz_mp_title_stmt;
DEALLOCATE PREPARE sornaz_mp_title_stmt;
SELECT 'f_music_practice_exams' AS migrated_table,@sornaz_mp_missing AS missing_translations,
 (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='f_music_practice_exams' AND COLUMN_NAME IN ('title_fa','title_en')) AS remaining_legacy_columns;



-- f_music_practice_plans: remove the same obsolete translated-title cache.
SET @sornaz_mp_has_title = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='f_music_practice_plans' AND COLUMN_NAME='title');
SET @sornaz_mp_source = IF(@sornaz_mp_has_title>0,'SELECT id AS table_id,''fa'' AS locale,title AS source_text,teacher_id AS actor FROM f_music_practice_plans UNION ALL SELECT id AS table_id,''en'' AS locale,title AS source_text,teacher_id AS actor FROM f_music_practice_plans','SELECT NULL AS table_id,'''' AS locale,'''' AS source_text,1 AS actor WHERE 1=0');
SET @sornaz_mp_sql = CONCAT('INSERT INTO f_translations (table_name,table_id,field,locale,value,version,created_at,created_by,updated_at,updated_by) SELECT ''music_practice_plans'',s.table_id,''title'',s.locale,s.source_text,COALESCE((SELECT MAX(h.version) FROM f_translations h WHERE h.table_name=''music_practice_plans'' AND h.table_id=s.table_id AND h.field=''title'' AND h.locale=s.locale),0)+1,CURRENT_TIMESTAMP,COALESCE(NULLIF(s.actor,0),1),CURRENT_TIMESTAMP,COALESCE(NULLIF(s.actor,0),1) FROM (',@sornaz_mp_source,') s WHERE s.source_text IS NOT NULL AND TRIM(s.source_text)<>'''' AND NOT EXISTS (SELECT 1 FROM f_translations t WHERE t.table_name=''music_practice_plans'' AND t.table_id=s.table_id AND t.field=''title'' AND t.locale=s.locale AND t.deleted_at IS NULL)');
PREPARE sornaz_mp_title_stmt FROM @sornaz_mp_sql;
EXECUTE sornaz_mp_title_stmt;
DEALLOCATE PREPARE sornaz_mp_title_stmt;
SET @sornaz_mp_sql = CONCAT('SELECT COUNT(*) INTO @sornaz_mp_missing FROM (',@sornaz_mp_source,') s WHERE s.source_text IS NOT NULL AND TRIM(s.source_text)<>'''' AND NOT EXISTS (SELECT 1 FROM f_translations t WHERE t.table_name=''music_practice_plans'' AND t.table_id=s.table_id AND t.field=''title'' AND t.locale=s.locale AND t.deleted_at IS NULL)');
PREPARE sornaz_mp_title_stmt FROM @sornaz_mp_sql;
EXECUTE sornaz_mp_title_stmt;
DEALLOCATE PREPARE sornaz_mp_title_stmt;
SET @sornaz_mp_sql = IF(@sornaz_mp_missing=0 AND @sornaz_mp_has_title>0,'ALTER TABLE f_music_practice_plans DROP COLUMN title','SELECT ''f_music_practice_plans: no drop needed or missing translations blocked cleanup'' AS migration_status');
PREPARE sornaz_mp_title_stmt FROM @sornaz_mp_sql;
EXECUTE sornaz_mp_title_stmt;
DEALLOCATE PREPARE sornaz_mp_title_stmt;
SELECT 'f_music_practice_plans' AS migrated_table,@sornaz_mp_missing AS missing_translations,
 (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='f_music_practice_plans' AND COLUMN_NAME='title') AS remaining_legacy_columns;
