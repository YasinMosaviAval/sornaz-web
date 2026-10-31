-- MariaDB: select the site's database, then import this complete file.
-- Back up first. No results, answers, translations or orders are deleted.
ALTER TABLE f_music_practice_exams ADD COLUMN IF NOT EXISTS deleted_at DATETIME NULL;
ALTER TABLE f_music_practice_exams ADD COLUMN IF NOT EXISTS deleted_by BIGINT UNSIGNED NULL;
ALTER TABLE f_music_practice_nodes ADD COLUMN IF NOT EXISTS deleted_at DATETIME NULL;
ALTER TABLE f_music_practice_nodes ADD COLUMN IF NOT EXISTS deleted_by BIGINT UNSIGNED NULL;

-- No role receives deletion automatically; founder user_id=1 is the existing bypass.
INSERT INTO f_access_system_permissions (name,resource,action,type,group_name,scope,risk_level,approval,created_at,created_by,updated_at,updated_by,approved_at,approved_by)
SELECT 'music_practice.delete','music_practice','delete','sidebar','website','platform',2,'confirm',NOW(),1,NOW(),1,NOW(),1
WHERE NOT EXISTS(SELECT 1 FROM f_access_system_permissions WHERE name='music_practice.delete' AND deleted_at IS NULL);

INSERT INTO f_translations(table_name,table_id,locale,field,value,version,created_at,created_by,updated_at,updated_by,approved_at,approved_by)
SELECT 'access_system_permissions',p.permission_id,s.locale,'title',s.label,COALESCE((SELECT MAX(h.version) FROM f_translations h WHERE h.table_name='access_system_permissions' AND h.table_id=p.permission_id AND h.locale=s.locale AND h.field='title'),0)+1,NOW(),1,NOW(),1,NOW(),1
FROM f_access_system_permissions p CROSS JOIN (SELECT 'fa' locale,'حذف سطح‌های آزمون موسیقی خود با حفظ سوابق' label UNION ALL SELECT 'en','Archive own music test levels while preserving history') s
WHERE p.name='music_practice.delete' AND p.deleted_at IS NULL
AND NOT EXISTS(SELECT 1 FROM f_translations t WHERE t.table_name='access_system_permissions' AND t.table_id=p.permission_id AND t.locale=s.locale AND t.field='title' AND t.deleted_at IS NULL);
