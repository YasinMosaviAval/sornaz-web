-- Prefer application rollback while keeping this additive schema.
-- Never remove the ownership column if any academy-owned room exists.
DELIMITER $$
CREATE PROCEDURE rollback_classroom_organizations()
BEGIN
  SET @room_table_count = (SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME IN ('p_academy_branch_classrooms','academy_branch_classrooms') AND TABLE_TYPE='BASE TABLE');
  IF DATABASE() IS NULL OR @room_table_count<>1 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Select the application database with exactly one matching classroom table.';
  END IF;
  SET @room_table = (SELECT MIN(TABLE_NAME) FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME IN ('p_academy_branch_classrooms','academy_branch_classrooms') AND TABLE_TYPE='BASE TABLE');
  SET @room_sql = CONCAT('SELECT COUNT(*) INTO @room_owned FROM `',@room_table,'` WHERE branch_id IS NULL OR academy_id IS NOT NULL');
  PREPARE room_ownership_check FROM @room_sql;
  EXECUTE room_ownership_check;
  DEALLOCATE PREPARE room_ownership_check;
  IF @room_owned>0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Academy-owned classrooms exist; retain the additive schema and data.';
  ELSE
    SET @room_branch_type = (SELECT COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=@room_table AND COLUMN_NAME='branch_id');
    SET @room_sql = CONCAT('ALTER TABLE `',@room_table,'` DROP FOREIGN KEY fk_classroom_academy, DROP INDEX idx_classroom_academy, DROP COLUMN academy_id, MODIFY COLUMN branch_id ',@room_branch_type,' NOT NULL');
    PREPARE room_rollback FROM @room_sql;
    EXECUTE room_rollback;
    DEALLOCATE PREPARE room_rollback;
  END IF;
END$$
DELIMITER ;
CALL rollback_classroom_organizations();
DROP PROCEDURE rollback_classroom_organizations;
