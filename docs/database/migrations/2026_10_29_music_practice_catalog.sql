-- Review and apply separately. Additive: legacy interval results remain untouched.
CREATE TABLE IF NOT EXISTS f_music_practice_nodes (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, parent_id BIGINT UNSIGNED NULL,
 slug VARCHAR(100) NOT NULL UNIQUE,
 depth TINYINT UNSIGNED NOT NULL, position INT NOT NULL DEFAULT 0, published TINYINT NOT NULL DEFAULT 0,
 version INT UNSIGNED NOT NULL DEFAULT 1, created_by BIGINT UNSIGNED NOT NULL, deleted_at DATETIME NULL, deleted_by BIGINT UNSIGNED NULL,
 updated_at DATETIME NOT NULL,
 KEY parent_order(parent_id,position,id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS f_music_practice_exams (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, node_id BIGINT UNSIGNED NOT NULL, owner_id BIGINT UNSIGNED NOT NULL,
 position INT NOT NULL DEFAULT 0,
 version INT UNSIGNED NOT NULL DEFAULT 1, published TINYINT NOT NULL DEFAULT 0,
 audience VARCHAR(20) NOT NULL DEFAULT 'public', legacy_key VARCHAR(100) NULL UNIQUE,
 deleted_at DATETIME NULL, deleted_by BIGINT UNSIGNED NULL,
 teacher_bps INT UNSIGNED NOT NULL DEFAULT 8000, share_version INT UNSIGNED NOT NULL DEFAULT 1,
 updated_at DATETIME NOT NULL, KEY node_order(node_id,position,id), KEY owner_audience(owner_id,audience)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS f_music_practice_versions (
 exam_id BIGINT UNSIGNED NOT NULL, version INT UNSIGNED NOT NULL, definition MEDIUMTEXT NOT NULL,
 was_published TINYINT NOT NULL DEFAULT 0, created_at DATETIME NOT NULL,
 PRIMARY KEY(exam_id,version), FOREIGN KEY(exam_id) REFERENCES f_music_practice_exams(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS f_music_practice_grants (
 exam_id BIGINT UNSIGNED NOT NULL, user_id BIGINT UNSIGNED NOT NULL, PRIMARY KEY(exam_id,user_id),
 FOREIGN KEY(exam_id) REFERENCES f_music_practice_exams(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS f_music_practice_runs (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, user_id BIGINT UNSIGNED NOT NULL, client_id CHAR(32) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
 exam_id BIGINT UNSIGNED NOT NULL, version INT UNSIGNED NOT NULL, stage INT UNSIGNED NOT NULL,
 status VARCHAR(20) NOT NULL DEFAULT 'in_progress', answer_count INT UNSIGNED NOT NULL DEFAULT 0,
 streak INT UNSIGNED NOT NULL DEFAULT 0, best_streak INT UNSIGNED NOT NULL DEFAULT 0,
 created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE KEY user_run(user_id,client_id),
 KEY user_exam(user_id,exam_id,version,status,stage),
 FOREIGN KEY(exam_id,version) REFERENCES f_music_practice_versions(exam_id,version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS f_music_practice_answers (
 run_id BIGINT UNSIGNED NOT NULL, sequence_number INT UNSIGNED NOT NULL, payload TEXT NOT NULL,
 payload_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL, is_correct TINYINT NOT NULL, received_at DATETIME NOT NULL,
 PRIMARY KEY(run_id,sequence_number), FOREIGN KEY(run_id) REFERENCES f_music_practice_runs(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS f_music_practice_plans (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, teacher_id BIGINT UNSIGNED NOT NULL, exam_id BIGINT UNSIGNED NULL, node_id BIGINT UNSIGNED NULL,
 amount BIGINT UNSIGNED NOT NULL, days INT UNSIGNED NOT NULL, teacher_bps INT UNSIGNED NULL,
 status VARCHAR(20) NOT NULL DEFAULT 'proposed', version INT UNSIGNED NOT NULL DEFAULT 1,
 approved_by BIGINT UNSIGNED NULL, updated_at DATETIME NOT NULL, KEY teacher_status(teacher_id,status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS f_music_practice_orders (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, plan_id BIGINT UNSIGNED NOT NULL, buyer_id BIGINT UNSIGNED NOT NULL,
 checkout_key CHAR(32) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
 teacher_id BIGINT UNSIGNED NOT NULL, exam_id BIGINT UNSIGNED NULL, node_id BIGINT UNSIGNED NULL, amount BIGINT UNSIGNED NOT NULL, days INT UNSIGNED NOT NULL,
 teacher_bps INT UNSIGNED NOT NULL, allocations MEDIUMTEXT NOT NULL, token CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL UNIQUE,
 authority VARCHAR(100) NULL UNIQUE, reference_id VARCHAR(100) NULL UNIQUE, status VARCHAR(20) NOT NULL DEFAULT 'created',
 created_at DATETIME NOT NULL, paid_at DATETIME NULL, starts_at DATETIME NULL, expires_at DATETIME NULL,
 UNIQUE KEY buyer_checkout(buyer_id,checkout_key), KEY buyer_teacher(buyer_id,teacher_id,status,expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS f_music_practice_revenue (
 order_id BIGINT UNSIGNED NOT NULL, exam_id BIGINT UNSIGNED NOT NULL, beneficiary VARCHAR(20) NOT NULL, user_id BIGINT UNSIGNED NOT NULL,
 amount BIGINT UNSIGNED NOT NULL, currency CHAR(3) NOT NULL DEFAULT 'IRT', created_at DATETIME NOT NULL,
 PRIMARY KEY(order_id,exam_id,beneficiary), FOREIGN KEY(order_id) REFERENCES f_music_practice_orders(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- No role receives these permissions. Founder (user_id=1) is the existing explicit bypass.
INSERT INTO f_access_system_permissions (name,resource,action,type,group_name,scope,risk_level,approval,created_at,created_by,updated_at,updated_by,approved_at,approved_by)
SELECT 'music_practice.create','music_practice','create','sidebar','website','platform',2,'confirm',NOW(),1,NOW(),1,NOW(),1
WHERE NOT EXISTS(SELECT 1 FROM f_access_system_permissions WHERE name='music_practice.create' AND deleted_at IS NULL);
INSERT INTO f_access_system_permissions (name,resource,action,type,group_name,scope,risk_level,approval,created_at,created_by,updated_at,updated_by,approved_at,approved_by)
SELECT 'music_practice.edit','music_practice','edit','sidebar','website','platform',2,'confirm',NOW(),1,NOW(),1,NOW(),1
WHERE NOT EXISTS(SELECT 1 FROM f_access_system_permissions WHERE name='music_practice.edit' AND deleted_at IS NULL);
INSERT INTO f_access_system_permissions (name,resource,action,type,group_name,scope,risk_level,approval,created_at,created_by,updated_at,updated_by,approved_at,approved_by)
SELECT 'music_practice.publish','music_practice','publish','sidebar','website','platform',3,'confirm',NOW(),1,NOW(),1,NOW(),1
WHERE NOT EXISTS(SELECT 1 FROM f_access_system_permissions WHERE name='music_practice.publish' AND deleted_at IS NULL);

INSERT INTO f_translations(table_name,table_id,locale,field,value,version,created_at,created_by,updated_at,updated_by,approved_at,approved_by)
SELECT 'access_system_permissions',p.permission_id,'fa','title',CASE p.name WHEN 'music_practice.create' THEN 'تعریف بخش و آزمون موسیقی' WHEN 'music_practice.edit' THEN 'ویرایش بخش و آزمون‌های موسیقی خود' ELSE 'انتشار عمومی آزمون‌های موسیقی خود' END,1,NOW(),1,NOW(),1,NOW(),1
FROM f_access_system_permissions p WHERE p.name IN ('music_practice.create','music_practice.edit','music_practice.publish') AND p.deleted_at IS NULL
AND NOT EXISTS(SELECT 1 FROM f_translations t WHERE t.table_name='access_system_permissions' AND t.table_id=p.permission_id AND t.locale='fa' AND t.field='title' AND t.deleted_at IS NULL);
INSERT INTO f_translations(table_name,table_id,locale,field,value,version,created_at,created_by,updated_at,updated_by,approved_at,approved_by)
SELECT 'access_system_permissions',p.permission_id,'en','title',CASE p.name WHEN 'music_practice.create' THEN 'Create music test sections and tests' WHEN 'music_practice.edit' THEN 'Edit own music test sections and tests' ELSE 'Publish own music tests' END,1,NOW(),1,NOW(),1,NOW(),1
FROM f_access_system_permissions p WHERE p.name IN ('music_practice.create','music_practice.edit','music_practice.publish') AND p.deleted_at IS NULL
AND NOT EXISTS(SELECT 1 FROM f_translations t WHERE t.table_name='access_system_permissions' AND t.table_id=p.permission_id AND t.locale='en' AND t.field='title' AND t.deleted_at IS NULL);

-- No role receives deletion automatically; founder user_id=1 is the existing bypass.
INSERT INTO f_access_system_permissions (name,resource,action,type,group_name,scope,risk_level,approval,created_at,created_by,updated_at,updated_by,approved_at,approved_by)
SELECT 'music_practice.delete','music_practice','delete','sidebar','website','platform',2,'confirm',NOW(),1,NOW(),1,NOW(),1
WHERE NOT EXISTS(SELECT 1 FROM f_access_system_permissions WHERE name='music_practice.delete' AND deleted_at IS NULL);

INSERT INTO f_translations(table_name,table_id,locale,field,value,version,created_at,created_by,updated_at,updated_by,approved_at,approved_by)
SELECT 'access_system_permissions',p.permission_id,s.locale,'title',s.label,COALESCE((SELECT MAX(h.version) FROM f_translations h WHERE h.table_name='access_system_permissions' AND h.table_id=p.permission_id AND h.locale=s.locale AND h.field='title'),0)+1,NOW(),1,NOW(),1,NOW(),1
FROM f_access_system_permissions p CROSS JOIN (SELECT 'fa' locale,'حذف سطح‌های آزمون موسیقی خود با حفظ سوابق' label UNION ALL SELECT 'en','Archive own music test levels while preserving history') s
WHERE p.name='music_practice.delete' AND p.deleted_at IS NULL
AND NOT EXISTS(SELECT 1 FROM f_translations t WHERE t.table_name='access_system_permissions' AND t.table_id=p.permission_id AND t.locale=s.locale AND t.field='title' AND t.deleted_at IS NULL);
