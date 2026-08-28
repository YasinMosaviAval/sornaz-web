INSERT INTO `access_system_permissions`
    (`name`,`resource`,`action`,`type`,`group_name`,`scope`,`risk_level`,`approval`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`)
SELECT
    'academy_records.auto_approve','academy_records','auto_approve','sidebar','academy','academy',3,'confirm',NOW(),1,NOW(),1,NOW(),1
WHERE NOT EXISTS (
    SELECT 1 FROM `access_system_permissions`
    WHERE `name`='academy_records.auto_approve' AND `deleted_at` IS NULL
);

SET @academy_auto_approve_permission_id := (
    SELECT `permission_id` FROM `access_system_permissions`
    WHERE `name`='academy_records.auto_approve' AND `deleted_at` IS NULL
    ORDER BY `permission_id` DESC LIMIT 1
);

INSERT INTO `f_translations`
    (`table_name`,`table_id`,`locale`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`)
SELECT 'access_system_permissions',@academy_auto_approve_permission_id,'fa','title','تأیید خودکار اطلاعات آموزشگاه و شعبه',1,NOW(),1,NOW(),1,NOW(),1
WHERE NOT EXISTS (
    SELECT 1 FROM `f_translations`
    WHERE `table_name`='access_system_permissions'
      AND `table_id`=@academy_auto_approve_permission_id
      AND `locale`='fa' AND `field`='title' AND `deleted_at` IS NULL
);

INSERT INTO `f_translations`
    (`table_name`,`table_id`,`locale`,`field`,`value`,`version`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`)
SELECT 'access_system_permissions',@academy_auto_approve_permission_id,'en','title','Auto-approve academy and branch records',1,NOW(),1,NOW(),1,NOW(),1
WHERE NOT EXISTS (
    SELECT 1 FROM `f_translations`
    WHERE `table_name`='access_system_permissions'
      AND `table_id`=@academy_auto_approve_permission_id
      AND `locale`='en' AND `field`='title' AND `deleted_at` IS NULL
);

INSERT INTO `access_system_role_permissions`
    (`role_id`,`permission_id`,`created_at`,`created_by`,`updated_at`,`updated_by`,`approved_at`,`approved_by`)
SELECT r.`role_id`,@academy_auto_approve_permission_id,NOW(),1,NOW(),1,NOW(),1
FROM `access_system_roles` r
WHERE r.`deleted_at` IS NULL
  AND r.`name` IN ('superadmin','admin','academy_owner','academy_manager','branch_owner','branch_manager')
  AND NOT EXISTS (
      SELECT 1 FROM `access_system_role_permissions` rp
      WHERE rp.`role_id`=r.`role_id`
        AND rp.`permission_id`=@academy_auto_approve_permission_id
        AND rp.`deleted_at` IS NULL
  );
