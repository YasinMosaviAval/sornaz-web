-- Add the grantable website permission without granting any existing role/user.
-- The founder (user_id=1) is authorized by AccessControl independently.
INSERT INTO f_access_system_permissions
    (name,resource,action,type,group_name,scope,risk_level,approval,created_at,created_by,updated_at,updated_by,approved_at,approved_by)
SELECT 'notation.access','notation','access','sidebar','notation','website',1,'confirm',NOW(),1,NOW(),1,NOW(),1
WHERE NOT EXISTS (SELECT 1 FROM f_access_system_permissions WHERE name='notation.access' AND deleted_at IS NULL);

SET @notation_access_id := (SELECT permission_id FROM f_access_system_permissions WHERE name='notation.access' AND deleted_at IS NULL ORDER BY permission_id DESC LIMIT 1);

INSERT INTO f_translations
    (table_name,table_id,locale,field,value,version,created_at,created_by,updated_at,updated_by,approved_at,approved_by)
SELECT 'access_system_permissions',@notation_access_id,'fa','title','دسترسی به نت‌نویسی وبسایت',1,NOW(),1,NOW(),1,NOW(),1
WHERE NOT EXISTS (SELECT 1 FROM f_translations WHERE table_name='access_system_permissions' AND table_id=@notation_access_id AND locale='fa' AND field='title' AND deleted_at IS NULL);

INSERT INTO f_translations
    (table_name,table_id,locale,field,value,version,created_at,created_by,updated_at,updated_by,approved_at,approved_by)
SELECT 'access_system_permissions',@notation_access_id,'en','title','Access website music notation',1,NOW(),1,NOW(),1,NOW(),1
WHERE NOT EXISTS (SELECT 1 FROM f_translations WHERE table_name='access_system_permissions' AND table_id=@notation_access_id AND locale='en' AND field='title' AND deleted_at IS NULL);
