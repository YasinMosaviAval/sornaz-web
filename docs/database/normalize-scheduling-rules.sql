START TRANSACTION;

-- Move translatable Persian content to the shared translations table.
INSERT INTO translations (table_name,table_id,locale,field,value,version,created_by)
SELECT 'academy_branch_scheduling_rules',r.scheduling_rule_id,'fa','title',r.title,1,r.created_by
FROM academy_branch_scheduling_rules r
WHERE NOT EXISTS (SELECT 1 FROM translations t WHERE t.table_name='academy_branch_scheduling_rules' AND t.table_id=r.scheduling_rule_id AND t.locale='fa' AND t.field='title' AND t.version=1);

INSERT INTO translations (table_name,table_id,locale,field,value,version,created_by)
SELECT 'academy_branch_scheduling_rules',r.scheduling_rule_id,'fa','summary',r.summary,1,r.created_by
FROM academy_branch_scheduling_rules r WHERE r.summary IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM translations t WHERE t.table_name='academy_branch_scheduling_rules' AND t.table_id=r.scheduling_rule_id AND t.locale='fa' AND t.field='summary' AND t.version=1);

INSERT INTO translations (table_name,table_id,locale,field,value,version,created_by)
SELECT 'academy_branch_scheduling_rules',r.scheduling_rule_id,'fa','description',r.description,1,r.created_by
FROM academy_branch_scheduling_rules r WHERE r.description IS NOT NULL
AND NOT EXISTS (SELECT 1 FROM translations t WHERE t.table_name='academy_branch_scheduling_rules' AND t.table_id=r.scheduling_rule_id AND t.locale='fa' AND t.field='description' AND t.version=1);

-- Normalize constrained string values before converting them to ENUM.
UPDATE academy_branch_scheduling_rules SET rule_type=CASE rule_type
    WHEN 'لغو' THEN 'cancellation' WHEN 'جبرانی' THEN 'makeup'
    WHEN 'رزرو' THEN 'reservation' WHEN 'زمان‌بندی' THEN 'scheduling' ELSE rule_type END;
UPDATE academy_branch_scheduling_rules SET status=CASE status
    WHEN 'فعال' THEN 'active' WHEN 'غیرفعال' THEN 'inactive'
    WHEN 'در انتظار تأیید' THEN 'pending' WHEN 'حذف‌شده' THEN 'deleted' ELSE status END;

ALTER TABLE academy_branch_scheduling_rules
    ADD COLUMN rule_value_numeric DECIMAL(12,2) UNSIGNED NULL AFTER rule_value,
    ADD COLUMN rule_value_unit ENUM('hour','minute','day','session','absence','person','year','boolean','percent','currency') NULL AFTER rule_value_numeric;

-- Convert Persian digits first; MySQL numeric casting ignores the trailing unit text.
UPDATE academy_branch_scheduling_rules
SET rule_value_numeric=CASE WHEN rule_value IN ('بله','yes','true') THEN 1 WHEN rule_value IN ('خیر','no','false') THEN 0 ELSE
    CAST(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(rule_value,'۰','0'),'۱','1'),'۲','2'),'۳','3'),'۴','4'),'۵','5'),'۶','6'),'۷','7'),'۸','8'),'۹','9') AS DECIMAL(12,2)) END,
    rule_value_unit=CASE
      WHEN rule_value LIKE '%دقیقه%' THEN 'minute' WHEN rule_value LIKE '%ساعت%' OR rule_value LIKE '%:%' THEN 'hour'
      WHEN rule_value LIKE '%روز%' THEN 'day' WHEN rule_value LIKE '%جلسه%' THEN 'session'
      WHEN rule_value LIKE '%غیبت%' THEN 'absence' WHEN rule_value LIKE '%نفر%' THEN 'person'
      WHEN rule_value LIKE '%سال%' THEN 'year' WHEN rule_value IN ('بله','خیر','yes','no','true','false') THEN 'boolean'
      WHEN rule_value LIKE '%درصد%' OR rule_value LIKE '%\%%' THEN 'percent'
      WHEN rule_value LIKE '%تومان%' OR rule_value LIKE '%ریال%' THEN 'currency' ELSE 'hour' END;

ALTER TABLE academy_branch_scheduling_rules
    DROP COLUMN title, DROP COLUMN summary, DROP COLUMN description, DROP COLUMN rule_value,
    DROP COLUMN max_sessions_per_day, DROP COLUMN min_break_minutes,
    DROP COLUMN session_duration, DROP COLUMN allow_overlap,
    CHANGE COLUMN rule_value_numeric rule_value DECIMAL(12,2) UNSIGNED NOT NULL,
    MODIFY COLUMN rule_value_unit ENUM('hour','minute','day','session','absence','person','year','boolean','percent','currency') NOT NULL,
    MODIFY COLUMN rule_type ENUM('cancellation','makeup','reservation','scheduling') NOT NULL,
    MODIFY COLUMN status ENUM('active','inactive','pending','deleted') NOT NULL DEFAULT 'active';

COMMIT;
