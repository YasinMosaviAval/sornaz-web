-- Additive catalog; apply manually on the intended MySQL/MariaDB database.
-- Preserve existing order, visibility and translations. Runtime requests never create tables.
CREATE TABLE IF NOT EXISTS f_notation_scales (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 code VARCHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL UNIQUE,
 position INT NOT NULL DEFAULT 0, active TINYINT NOT NULL DEFAULT 1,
 updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 KEY active_position(active,position,id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- Localized text is inserted directly into f_translations; no language columns or staging tables.
START TRANSACTION;
INSERT INTO f_notation_scales(code,position,active) VALUES
('major',1,1),
('minor',2,1),
('melodic_minor',3,1),
('harmonic_minor',4,1),
('iran_shur',5,1),
('iran_mahur',6,1),
('iran_homayun',7,1),
('iran_nava',8,1),
('iran_segah',9,1),
('iran_chahargah',10,1),
('iran_rastpanjgah',11,1),
('iran_abuata',12,1),
('iran_bayat_tork',13,1),
('iran_afshari',14,1),
('iran_dashti',15,1),
('iran_bayat_esfahan',16,1)
ON DUPLICATE KEY UPDATE code=VALUES(code);
INSERT INTO f_translations(table_name,table_id,field,locale,value,version,created_at,created_by,updated_at,updated_by)
 SELECT 'notation_scales',s.id,'title',seed.locale,seed.value,
 COALESCE((SELECT MAX(h.version) FROM f_translations h WHERE h.table_name='notation_scales' AND h.table_id=s.id AND h.field='title' AND h.locale=seed.locale),0)+1,
 CURRENT_TIMESTAMP,1,CURRENT_TIMESTAMP,1
 FROM (
 SELECT 'major' AS code, 'fa' AS locale, 'ماژور' AS value
 UNION ALL SELECT 'major', 'en', 'Major'
 UNION ALL SELECT 'minor', 'fa', 'مینور'
 UNION ALL SELECT 'minor', 'en', 'Minor'
 UNION ALL SELECT 'melodic_minor', 'fa', 'مینور ملودیک'
 UNION ALL SELECT 'melodic_minor', 'en', 'Melodic minor'
 UNION ALL SELECT 'harmonic_minor', 'fa', 'مینور هارمونیک'
 UNION ALL SELECT 'harmonic_minor', 'en', 'Harmonic minor'
 UNION ALL SELECT 'iran_shur', 'fa', 'دستگاه شور'
 UNION ALL SELECT 'iran_shur', 'en', 'Dastgah Shur'
 UNION ALL SELECT 'iran_mahur', 'fa', 'دستگاه ماهور'
 UNION ALL SELECT 'iran_mahur', 'en', 'Dastgah Mahur'
 UNION ALL SELECT 'iran_homayun', 'fa', 'دستگاه همایون'
 UNION ALL SELECT 'iran_homayun', 'en', 'Dastgah Homayun'
 UNION ALL SELECT 'iran_nava', 'fa', 'دستگاه نوا'
 UNION ALL SELECT 'iran_nava', 'en', 'Dastgah Nava'
 UNION ALL SELECT 'iran_segah', 'fa', 'دستگاه سه گاه'
 UNION ALL SELECT 'iran_segah', 'en', 'Dastgah Segah'
 UNION ALL SELECT 'iran_chahargah', 'fa', 'دستگاه چهارگاه'
 UNION ALL SELECT 'iran_chahargah', 'en', 'Dastgah Chahargah'
 UNION ALL SELECT 'iran_rastpanjgah', 'fa', 'دستگاه راست پنجگاه'
 UNION ALL SELECT 'iran_rastpanjgah', 'en', 'Dastgah Rast Panjgah'
 UNION ALL SELECT 'iran_abuata', 'fa', 'آواز ابوعطا'
 UNION ALL SELECT 'iran_abuata', 'en', 'Avaz Abu Ata'
 UNION ALL SELECT 'iran_bayat_tork', 'fa', 'آواز بیات ترک'
 UNION ALL SELECT 'iran_bayat_tork', 'en', 'Avaz Bayat Tork'
 UNION ALL SELECT 'iran_afshari', 'fa', 'آواز افشاری'
 UNION ALL SELECT 'iran_afshari', 'en', 'Avaz Afshari'
 UNION ALL SELECT 'iran_dashti', 'fa', 'آواز دشتی'
 UNION ALL SELECT 'iran_dashti', 'en', 'Avaz Dashti'
 UNION ALL SELECT 'iran_bayat_esfahan', 'fa', 'آواز بیات اصفهان'
 UNION ALL SELECT 'iran_bayat_esfahan', 'en', 'Avaz Bayat Esfahan'
 ) seed JOIN f_notation_scales s ON s.code=seed.code
 WHERE NOT EXISTS (SELECT 1 FROM f_translations t WHERE t.table_name='notation_scales' AND t.table_id=s.id AND t.field='title' AND t.locale=seed.locale AND t.deleted_at IS NULL);
COMMIT;
