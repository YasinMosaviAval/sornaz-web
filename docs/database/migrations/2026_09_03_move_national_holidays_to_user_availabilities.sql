START TRANSACTION;

INSERT INTO user_availabilities
    (user_id,member_id,date,day_of_week,start_time,end_time,timezone_id,status,unavailable_type,is_repeating,repeat_period,is_closed,priority,created_at,created_by,updated_at,updated_by,deleted_at,deleted_by)
SELECT NULL,NULL,holiday_date,NULL,NULL,NULL,1,
       IF(status='active','available','unavailable'),'national_holiday',0,'none',1,0,
       created_at,created_by,updated_at,updated_by,deleted_at,deleted_by
FROM national_holidays;

INSERT INTO translations (table_name,table_id,field,locale,value,version,created_at,created_by,updated_at,updated_by)
SELECT 'user_availabilities',ua.user_availability_id,'summary','fa',h.title,1,h.created_at,h.created_by,h.updated_at,h.updated_by
FROM national_holidays h
JOIN user_availabilities ua ON ua.user_id IS NULL AND ua.unavailable_type='national_holiday' AND ua.date=h.holiday_date;

INSERT INTO translations (table_name,table_id,field,locale,value,version,created_at,created_by,updated_at,updated_by)
SELECT 'user_availabilities',ua.user_availability_id,'description','fa',COALESCE(h.description,''),1,h.created_at,h.created_by,h.updated_at,h.updated_by
FROM national_holidays h
JOIN user_availabilities ua ON ua.user_id IS NULL AND ua.unavailable_type='national_holiday' AND ua.date=h.holiday_date;

UPDATE user_messages m
JOIN national_holidays h ON h.national_holiday_id=m.related_entity_id
JOIN user_availabilities ua ON ua.user_id IS NULL AND ua.unavailable_type='national_holiday' AND ua.date=h.holiday_date
SET m.related_entity_id=ua.user_availability_id
WHERE m.related_entity_type='national_holiday_conflict';

COMMIT;
DROP TABLE national_holidays;
