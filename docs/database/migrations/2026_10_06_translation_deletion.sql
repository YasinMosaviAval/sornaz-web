-- Generated translation deletion synchronization. Review before applying.
DELIMITER $$
DROP TRIGGER IF EXISTS `tr_delete_ad402c6b74e0ef48a6b03a04`$$
CREATE TRIGGER `tr_delete_ad402c6b74e0ef48a6b03a04` AFTER UPDATE ON `f_access_system_permissions` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('access_system_permissions','f_access_system_permissions') AND table_id=OLD.`permission_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('access_system_permissions','f_access_system_permissions') AND table_id=OLD.`permission_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_30a6b8b7491674e152af893d`$$
CREATE TRIGGER `tr_delete_30a6b8b7491674e152af893d` AFTER DELETE ON `f_access_system_permissions` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('access_system_permissions','f_access_system_permissions') AND table_id=OLD.`permission_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('access_system_permissions','f_access_system_permissions') AND table_id=OLD.`permission_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_1c9dc6467c762f69115565b4`$$
CREATE TRIGGER `tr_delete_1c9dc6467c762f69115565b4` AFTER UPDATE ON `f_access_system_roles` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('access_system_roles','f_access_system_roles') AND table_id=OLD.`role_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('access_system_roles','f_access_system_roles') AND table_id=OLD.`role_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_7a107d76a1291de8a84f5398`$$
CREATE TRIGGER `tr_delete_7a107d76a1291de8a84f5398` AFTER DELETE ON `f_access_system_roles` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('access_system_roles','f_access_system_roles') AND table_id=OLD.`role_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('access_system_roles','f_access_system_roles') AND table_id=OLD.`role_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_08e18695f8b6a23b70eef8e1`$$
CREATE TRIGGER `tr_delete_08e18695f8b6a23b70eef8e1` AFTER UPDATE ON `f_access_system_role_permissions` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('access_system_role_permissions','f_access_system_role_permissions') AND table_id=OLD.`role_permission_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('access_system_role_permissions','f_access_system_role_permissions') AND table_id=OLD.`role_permission_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_3b25a2b91f517b2a73ef488e`$$
CREATE TRIGGER `tr_delete_3b25a2b91f517b2a73ef488e` AFTER DELETE ON `f_access_system_role_permissions` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('access_system_role_permissions','f_access_system_role_permissions') AND table_id=OLD.`role_permission_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('access_system_role_permissions','f_access_system_role_permissions') AND table_id=OLD.`role_permission_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_b5dbabbc80fe6a0ea3765c74`$$
CREATE TRIGGER `tr_delete_b5dbabbc80fe6a0ea3765c74` AFTER UPDATE ON `f_access_system_setting_permissions` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('access_system_setting_permissions','f_access_system_setting_permissions') AND table_id=OLD.`setting_permission_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('access_system_setting_permissions','f_access_system_setting_permissions') AND table_id=OLD.`setting_permission_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_8b7108ee3022aa9089af5934`$$
CREATE TRIGGER `tr_delete_8b7108ee3022aa9089af5934` AFTER DELETE ON `f_access_system_setting_permissions` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('access_system_setting_permissions','f_access_system_setting_permissions') AND table_id=OLD.`setting_permission_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('access_system_setting_permissions','f_access_system_setting_permissions') AND table_id=OLD.`setting_permission_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_534655bc459e39f4651bc660`$$
CREATE TRIGGER `tr_delete_534655bc459e39f4651bc660` AFTER DELETE ON `f_article_comment_receipts` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('article_comment_receipts','f_article_comment_receipts') AND table_id=OLD.`comment_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('article_comment_receipts','f_article_comment_receipts') AND table_id=OLD.`comment_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_ec0f1eba45980e07fce74e99`$$
CREATE TRIGGER `tr_delete_ec0f1eba45980e07fce74e99` AFTER DELETE ON `f_auth_rate_limits` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('auth_rate_limits','f_auth_rate_limits') AND table_id=OLD.`bucket_key` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('auth_rate_limits','f_auth_rate_limits') AND table_id=OLD.`bucket_key` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_8c86bfb47fd10247a2b195a5`$$
CREATE TRIGGER `tr_delete_8c86bfb47fd10247a2b195a5` AFTER DELETE ON `f_auth_remember_tokens` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('auth_remember_tokens','f_auth_remember_tokens') AND table_id=OLD.`token_hash` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('auth_remember_tokens','f_auth_remember_tokens') AND table_id=OLD.`token_hash` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_c519aebf27c64c58ff0922c3`$$
CREATE TRIGGER `tr_delete_c519aebf27c64c58ff0922c3` AFTER UPDATE ON `f_categories` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('categories','f_categories') AND table_id=OLD.`category_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('categories','f_categories') AND table_id=OLD.`category_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_5f5a80e0bfcbeae7d6de8f77`$$
CREATE TRIGGER `tr_delete_5f5a80e0bfcbeae7d6de8f77` AFTER DELETE ON `f_categories` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('categories','f_categories') AND table_id=OLD.`category_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('categories','f_categories') AND table_id=OLD.`category_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_a8472050ba31261ca7d4dd75`$$
CREATE TRIGGER `tr_delete_a8472050ba31261ca7d4dd75` AFTER UPDATE ON `f_comments` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('comments','f_comments') AND table_id=OLD.`comment_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('comments','f_comments') AND table_id=OLD.`comment_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_e48b607cadf2c6a35fb65cf5`$$
CREATE TRIGGER `tr_delete_e48b607cadf2c6a35fb65cf5` AFTER DELETE ON `f_comments` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('comments','f_comments') AND table_id=OLD.`comment_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('comments','f_comments') AND table_id=OLD.`comment_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_3251a279d80c267f4e3d69ce`$$
CREATE TRIGGER `tr_delete_3251a279d80c267f4e3d69ce` AFTER UPDATE ON `f_conversations` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('conversations','f_conversations') AND table_id=OLD.`conversation_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('conversations','f_conversations') AND table_id=OLD.`conversation_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_4ff301a08cde34e56bcae12b`$$
CREATE TRIGGER `tr_delete_4ff301a08cde34e56bcae12b` AFTER DELETE ON `f_conversations` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('conversations','f_conversations') AND table_id=OLD.`conversation_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('conversations','f_conversations') AND table_id=OLD.`conversation_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_65e915adb3db455446b8f3db`$$
CREATE TRIGGER `tr_delete_65e915adb3db455446b8f3db` AFTER UPDATE ON `f_conversation_members` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('conversation_members','f_conversation_members') AND table_id=OLD.`conversation_member_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('conversation_members','f_conversation_members') AND table_id=OLD.`conversation_member_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_5f263a505ff4178acf6c27ff`$$
CREATE TRIGGER `tr_delete_5f263a505ff4178acf6c27ff` AFTER DELETE ON `f_conversation_members` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('conversation_members','f_conversation_members') AND table_id=OLD.`conversation_member_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('conversation_members','f_conversation_members') AND table_id=OLD.`conversation_member_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_8e9a9b22a6d45c928bafb049`$$
CREATE TRIGGER `tr_delete_8e9a9b22a6d45c928bafb049` AFTER UPDATE ON `f_conversation_messages` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('conversation_messages','f_conversation_messages') AND table_id=OLD.`conversation_message_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('conversation_messages','f_conversation_messages') AND table_id=OLD.`conversation_message_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_12ab4b6d9730bcd11ba8e2c7`$$
CREATE TRIGGER `tr_delete_12ab4b6d9730bcd11ba8e2c7` AFTER DELETE ON `f_conversation_messages` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('conversation_messages','f_conversation_messages') AND table_id=OLD.`conversation_message_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('conversation_messages','f_conversation_messages') AND table_id=OLD.`conversation_message_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_1bf2aedda2f280f50dcf6f86`$$
CREATE TRIGGER `tr_delete_1bf2aedda2f280f50dcf6f86` AFTER DELETE ON `f_conversation_message_reactions` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('conversation_message_reactions','f_conversation_message_reactions') AND table_id=OLD.`conversation_message_reaction_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('conversation_message_reactions','f_conversation_message_reactions') AND table_id=OLD.`conversation_message_reaction_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_98c8c37b5ea12b167be009bb`$$
CREATE TRIGGER `tr_delete_98c8c37b5ea12b167be009bb` AFTER UPDATE ON `f_financial_system_accounts` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('financial_system_accounts','f_financial_system_accounts') AND table_id=OLD.`account_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('financial_system_accounts','f_financial_system_accounts') AND table_id=OLD.`account_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_be8776841c4f3b490ca1006f`$$
CREATE TRIGGER `tr_delete_be8776841c4f3b490ca1006f` AFTER DELETE ON `f_financial_system_accounts` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('financial_system_accounts','f_financial_system_accounts') AND table_id=OLD.`account_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('financial_system_accounts','f_financial_system_accounts') AND table_id=OLD.`account_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_96672708ec8125defd9ffda8`$$
CREATE TRIGGER `tr_delete_96672708ec8125defd9ffda8` AFTER DELETE ON `f_financial_system_currency` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('financial_system_currency','f_financial_system_currency') AND table_id=OLD.`currency_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('financial_system_currency','f_financial_system_currency') AND table_id=OLD.`currency_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_4289e2a5568883174d7b8fa3`$$
CREATE TRIGGER `tr_delete_4289e2a5568883174d7b8fa3` AFTER UPDATE ON `f_financial_system_discounts` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('financial_system_discounts','f_financial_system_discounts') AND table_id=OLD.`discount_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('financial_system_discounts','f_financial_system_discounts') AND table_id=OLD.`discount_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_47ff5e7a143f9dffe458d80d`$$
CREATE TRIGGER `tr_delete_47ff5e7a143f9dffe458d80d` AFTER DELETE ON `f_financial_system_discounts` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('financial_system_discounts','f_financial_system_discounts') AND table_id=OLD.`discount_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('financial_system_discounts','f_financial_system_discounts') AND table_id=OLD.`discount_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_ab2fab4aeca67ca2149d7ab1`$$
CREATE TRIGGER `tr_delete_ab2fab4aeca67ca2149d7ab1` AFTER UPDATE ON `f_financial_system_ledger_entries` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('financial_system_ledger_entries','f_financial_system_ledger_entries') AND table_id=OLD.`ledger_entry_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('financial_system_ledger_entries','f_financial_system_ledger_entries') AND table_id=OLD.`ledger_entry_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_e9a961ffa30c47fdbf3bb68b`$$
CREATE TRIGGER `tr_delete_e9a961ffa30c47fdbf3bb68b` AFTER DELETE ON `f_financial_system_ledger_entries` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('financial_system_ledger_entries','f_financial_system_ledger_entries') AND table_id=OLD.`ledger_entry_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('financial_system_ledger_entries','f_financial_system_ledger_entries') AND table_id=OLD.`ledger_entry_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_a4b27bfef1a043bfa036960c`$$
CREATE TRIGGER `tr_delete_a4b27bfef1a043bfa036960c` AFTER UPDATE ON `f_financial_system_payments` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('financial_system_payments','f_financial_system_payments') AND table_id=OLD.`payment_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('financial_system_payments','f_financial_system_payments') AND table_id=OLD.`payment_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_b71821f260c695b986a37821`$$
CREATE TRIGGER `tr_delete_b71821f260c695b986a37821` AFTER DELETE ON `f_financial_system_payments` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('financial_system_payments','f_financial_system_payments') AND table_id=OLD.`payment_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('financial_system_payments','f_financial_system_payments') AND table_id=OLD.`payment_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_b308b14449934ca40d2dd935`$$
CREATE TRIGGER `tr_delete_b308b14449934ca40d2dd935` AFTER UPDATE ON `f_financial_system_refunds` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('financial_system_refunds','f_financial_system_refunds') AND table_id=OLD.`refund_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('financial_system_refunds','f_financial_system_refunds') AND table_id=OLD.`refund_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_05261c34d279f92fda30382e`$$
CREATE TRIGGER `tr_delete_05261c34d279f92fda30382e` AFTER DELETE ON `f_financial_system_refunds` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('financial_system_refunds','f_financial_system_refunds') AND table_id=OLD.`refund_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('financial_system_refunds','f_financial_system_refunds') AND table_id=OLD.`refund_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_b104acf7aa351e43f7ebf95b`$$
CREATE TRIGGER `tr_delete_b104acf7aa351e43f7ebf95b` AFTER UPDATE ON `f_financial_system_transactions` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('financial_system_transactions','f_financial_system_transactions') AND table_id=OLD.`transaction_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('financial_system_transactions','f_financial_system_transactions') AND table_id=OLD.`transaction_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_57f157c5612e8b272b4b4610`$$
CREATE TRIGGER `tr_delete_57f157c5612e8b272b4b4610` AFTER DELETE ON `f_financial_system_transactions` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('financial_system_transactions','f_financial_system_transactions') AND table_id=OLD.`transaction_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('financial_system_transactions','f_financial_system_transactions') AND table_id=OLD.`transaction_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_aa9d18336dff62779b0feba0`$$
CREATE TRIGGER `tr_delete_aa9d18336dff62779b0feba0` AFTER UPDATE ON `f_legacy_settings` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('z_settings','f_legacy_settings') AND table_id=OLD.`setting_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('z_settings','f_legacy_settings') AND table_id=OLD.`setting_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_57120beec57597092996d02d`$$
CREATE TRIGGER `tr_delete_57120beec57597092996d02d` AFTER DELETE ON `f_legacy_settings` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('z_settings','f_legacy_settings') AND table_id=OLD.`setting_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('z_settings','f_legacy_settings') AND table_id=OLD.`setting_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_c252c724640ec28207fa16f7`$$
CREATE TRIGGER `tr_delete_c252c724640ec28207fa16f7` AFTER UPDATE ON `f_media_files` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('media_files','f_media_files') AND table_id=OLD.`media_file_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('media_files','f_media_files') AND table_id=OLD.`media_file_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_586e3232e6ec219cb97b7f1c`$$
CREATE TRIGGER `tr_delete_586e3232e6ec219cb97b7f1c` AFTER DELETE ON `f_media_files` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('media_files','f_media_files') AND table_id=OLD.`media_file_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('media_files','f_media_files') AND table_id=OLD.`media_file_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_a203863909dc7f412a173a84`$$
CREATE TRIGGER `tr_delete_a203863909dc7f412a173a84` AFTER UPDATE ON `f_posts` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('posts','f_posts') AND table_id=OLD.`post_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('posts','f_posts') AND table_id=OLD.`post_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_5d7ce5cb54009547a9900143`$$
CREATE TRIGGER `tr_delete_5d7ce5cb54009547a9900143` AFTER DELETE ON `f_posts` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('posts','f_posts') AND table_id=OLD.`post_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('posts','f_posts') AND table_id=OLD.`post_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_4fd4138d7d7a271edaf9eb1f`$$
CREATE TRIGGER `tr_delete_4fd4138d7d7a271edaf9eb1f` AFTER UPDATE ON `f_public_ratings` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('public_ratings','f_public_ratings') AND table_id=OLD.`public_rating_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('public_ratings','f_public_ratings') AND table_id=OLD.`public_rating_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_02cffa21ba22a8462baae55a`$$
CREATE TRIGGER `tr_delete_02cffa21ba22a8462baae55a` AFTER DELETE ON `f_public_ratings` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('public_ratings','f_public_ratings') AND table_id=OLD.`public_rating_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('public_ratings','f_public_ratings') AND table_id=OLD.`public_rating_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_572348e5b6774abc5bbd5277`$$
CREATE TRIGGER `tr_delete_572348e5b6774abc5bbd5277` AFTER UPDATE ON `f_settings` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('f_settings','f_settings') AND table_id=OLD.`setting_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('f_settings','f_settings') AND table_id=OLD.`setting_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_ec977cbeae44ced31bebfe88`$$
CREATE TRIGGER `tr_delete_ec977cbeae44ced31bebfe88` AFTER DELETE ON `f_settings` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('f_settings','f_settings') AND table_id=OLD.`setting_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('f_settings','f_settings') AND table_id=OLD.`setting_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_577f1cb7ba2beba931e2700f`$$
CREATE TRIGGER `tr_delete_577f1cb7ba2beba931e2700f` AFTER DELETE ON `f_social_account_settings` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('social_account_settings','f_social_account_settings') AND table_id=OLD.`user_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('social_account_settings','f_social_account_settings') AND table_id=OLD.`user_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_abeaacb60999b336cbd46d80`$$
CREATE TRIGGER `tr_delete_abeaacb60999b336cbd46d80` AFTER UPDATE ON `f_social_comments` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=@sornaz_deleted_by WHERE table_name IN ('social_comments','f_social_comments') AND table_id=OLD.`id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=@sornaz_deleted_by WHERE table_name IN ('social_comments','f_social_comments') AND table_id=OLD.`id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_85acb0494881c5a7c263fe85`$$
CREATE TRIGGER `tr_delete_85acb0494881c5a7c263fe85` AFTER DELETE ON `f_social_comments` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('social_comments','f_social_comments') AND table_id=OLD.`id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('social_comments','f_social_comments') AND table_id=OLD.`id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_28df8d20ad51100f1020efbb`$$
CREATE TRIGGER `tr_delete_28df8d20ad51100f1020efbb` AFTER DELETE ON `f_social_highlights` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('social_highlights','f_social_highlights') AND table_id=OLD.`id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('social_highlights','f_social_highlights') AND table_id=OLD.`id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_2aad8c0a0f46208d24fe20bf`$$
CREATE TRIGGER `tr_delete_2aad8c0a0f46208d24fe20bf` AFTER DELETE ON `f_social_media` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('social_media','f_social_media') AND table_id=OLD.`id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('social_media','f_social_media') AND table_id=OLD.`id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_f23230b5cdcbc5662a2c7309`$$
CREATE TRIGGER `tr_delete_f23230b5cdcbc5662a2c7309` AFTER DELETE ON `f_social_notifications` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('social_notifications','f_social_notifications') AND table_id=OLD.`id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('social_notifications','f_social_notifications') AND table_id=OLD.`id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_7be259b64f3d97f7933dfd40`$$
CREATE TRIGGER `tr_delete_7be259b64f3d97f7933dfd40` AFTER UPDATE ON `f_social_posts` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=@sornaz_deleted_by WHERE table_name IN ('social_posts','f_social_posts') AND table_id=OLD.`id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=@sornaz_deleted_by WHERE table_name IN ('social_posts','f_social_posts') AND table_id=OLD.`id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_24d48f224bfe3b9e184bd09d`$$
CREATE TRIGGER `tr_delete_24d48f224bfe3b9e184bd09d` AFTER DELETE ON `f_social_posts` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('social_posts','f_social_posts') AND table_id=OLD.`id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('social_posts','f_social_posts') AND table_id=OLD.`id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_ee9251e6ee6a43fc295419d9`$$
CREATE TRIGGER `tr_delete_ee9251e6ee6a43fc295419d9` AFTER DELETE ON `f_social_profiles` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('social_profiles','f_social_profiles') AND table_id=OLD.`user_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('social_profiles','f_social_profiles') AND table_id=OLD.`user_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_800b67cfa7760700b2e5802d`$$
CREATE TRIGGER `tr_delete_800b67cfa7760700b2e5802d` AFTER UPDATE ON `f_timezone` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('f_timezone','f_timezone') AND table_id=OLD.`timezone_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('f_timezone','f_timezone') AND table_id=OLD.`timezone_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_ec4e1d32d0f6d9b141e95e43`$$
CREATE TRIGGER `tr_delete_ec4e1d32d0f6d9b141e95e43` AFTER DELETE ON `f_timezone` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('f_timezone','f_timezone') AND table_id=OLD.`timezone_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('f_timezone','f_timezone') AND table_id=OLD.`timezone_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_9ce9a280590d4ab4a5102753`$$
CREATE TRIGGER `tr_delete_9ce9a280590d4ab4a5102753` AFTER DELETE ON `f_tracking_ingestion_batches` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('tracking_ingestion_batches','f_tracking_ingestion_batches') AND table_id=OLD.`tracking_ingestion_batch_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('tracking_ingestion_batches','f_tracking_ingestion_batches') AND table_id=OLD.`tracking_ingestion_batch_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_2d880f0f6691560f65896368`$$
CREATE TRIGGER `tr_delete_2d880f0f6691560f65896368` AFTER DELETE ON `f_tracking_user_activity_intervals` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('tracking_user_activity_intervals','f_tracking_user_activity_intervals') AND table_id=OLD.`tracking_user_activity_interval_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('tracking_user_activity_intervals','f_tracking_user_activity_intervals') AND table_id=OLD.`tracking_user_activity_interval_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_3f2fd5c171572c17234da0f6`$$
CREATE TRIGGER `tr_delete_3f2fd5c171572c17234da0f6` AFTER DELETE ON `f_tracking_user_consents` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('tracking_user_consents','f_tracking_user_consents') AND table_id=OLD.`tracking_user_consent_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('tracking_user_consents','f_tracking_user_consents') AND table_id=OLD.`tracking_user_consent_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_6488bf4b55c1750facf8e154`$$
CREATE TRIGGER `tr_delete_6488bf4b55c1750facf8e154` AFTER DELETE ON `f_tracking_user_content_engagements` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('tracking_user_content_engagements','f_tracking_user_content_engagements') AND table_id=OLD.`tracking_user_content_engagement_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('tracking_user_content_engagements','f_tracking_user_content_engagements') AND table_id=OLD.`tracking_user_content_engagement_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_dc39454cfe6275d6f2c60fd2`$$
CREATE TRIGGER `tr_delete_dc39454cfe6275d6f2c60fd2` AFTER DELETE ON `f_tracking_user_events` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('tracking_user_events','f_tracking_user_events') AND table_id=OLD.`tracking_user_event_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('tracking_user_events','f_tracking_user_events') AND table_id=OLD.`tracking_user_event_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_128f9d53751e7ffef781f683`$$
CREATE TRIGGER `tr_delete_128f9d53751e7ffef781f683` AFTER DELETE ON `f_tracking_user_page_views` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('tracking_user_page_views','f_tracking_user_page_views') AND table_id=OLD.`tracking_user_page_view_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('tracking_user_page_views','f_tracking_user_page_views') AND table_id=OLD.`tracking_user_page_view_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_50f697a2131aff31c98c6c1a`$$
CREATE TRIGGER `tr_delete_50f697a2131aff31c98c6c1a` AFTER DELETE ON `f_tracking_user_sessions` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('tracking_user_sessions','f_tracking_user_sessions') AND table_id=OLD.`tracking_user_session_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('tracking_user_sessions','f_tracking_user_sessions') AND table_id=OLD.`tracking_user_session_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_108a56fea1487cefca63e4f6`$$
CREATE TRIGGER `tr_delete_108a56fea1487cefca63e4f6` AFTER UPDATE ON `f_users` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('users','f_users') AND table_id=OLD.`user_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('users','f_users') AND table_id=OLD.`user_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_748f07056e4041144dcc61b6`$$
CREATE TRIGGER `tr_delete_748f07056e4041144dcc61b6` AFTER DELETE ON `f_users` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('users','f_users') AND table_id=OLD.`user_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('users','f_users') AND table_id=OLD.`user_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_65922efbf23893b2a9a4e881`$$
CREATE TRIGGER `tr_delete_65922efbf23893b2a9a4e881` AFTER UPDATE ON `f_user_addresses` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_addresses','f_user_addresses') AND table_id=OLD.`address_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_addresses','f_user_addresses') AND table_id=OLD.`address_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_7c6caf5eb9327e60f2b25033`$$
CREATE TRIGGER `tr_delete_7c6caf5eb9327e60f2b25033` AFTER DELETE ON `f_user_addresses` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_addresses','f_user_addresses') AND table_id=OLD.`address_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_addresses','f_user_addresses') AND table_id=OLD.`address_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_d00155f2ead8078a8a6bbb48`$$
CREATE TRIGGER `tr_delete_d00155f2ead8078a8a6bbb48` AFTER UPDATE ON `f_user_availabilities` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_availabilities','f_user_availabilities') AND table_id=OLD.`user_availability_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_availabilities','f_user_availabilities') AND table_id=OLD.`user_availability_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_685a20ee9ea3c90cec324065`$$
CREATE TRIGGER `tr_delete_685a20ee9ea3c90cec324065` AFTER DELETE ON `f_user_availabilities` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_availabilities','f_user_availabilities') AND table_id=OLD.`user_availability_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_availabilities','f_user_availabilities') AND table_id=OLD.`user_availability_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_6a5b6375a2fac001235cbaf1`$$
CREATE TRIGGER `tr_delete_6a5b6375a2fac001235cbaf1` AFTER UPDATE ON `f_user_awards` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_awards','f_user_awards') AND table_id=OLD.`user_award_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_awards','f_user_awards') AND table_id=OLD.`user_award_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_259cd75c683cc00c2e9b323c`$$
CREATE TRIGGER `tr_delete_259cd75c683cc00c2e9b323c` AFTER DELETE ON `f_user_awards` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_awards','f_user_awards') AND table_id=OLD.`user_award_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_awards','f_user_awards') AND table_id=OLD.`user_award_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_5c4054c89c4ea6f64807dc08`$$
CREATE TRIGGER `tr_delete_5c4054c89c4ea6f64807dc08` AFTER UPDATE ON `f_user_badges` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_badges','f_user_badges') AND table_id=OLD.`user_badge_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_badges','f_user_badges') AND table_id=OLD.`user_badge_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_f33bc5802e8cb7b536fa088d`$$
CREATE TRIGGER `tr_delete_f33bc5802e8cb7b536fa088d` AFTER DELETE ON `f_user_badges` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_badges','f_user_badges') AND table_id=OLD.`user_badge_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_badges','f_user_badges') AND table_id=OLD.`user_badge_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_9db15443c1d1b42ad28aec3b`$$
CREATE TRIGGER `tr_delete_9db15443c1d1b42ad28aec3b` AFTER UPDATE ON `f_user_certificates` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_certificates','f_user_certificates') AND table_id=OLD.`user_certificate_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_certificates','f_user_certificates') AND table_id=OLD.`user_certificate_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_b66436a622b098b9ebaa65b3`$$
CREATE TRIGGER `tr_delete_b66436a622b098b9ebaa65b3` AFTER DELETE ON `f_user_certificates` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_certificates','f_user_certificates') AND table_id=OLD.`user_certificate_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_certificates','f_user_certificates') AND table_id=OLD.`user_certificate_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_2812c439471392d433fd8668`$$
CREATE TRIGGER `tr_delete_2812c439471392d433fd8668` AFTER UPDATE ON `f_user_contacts` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_contacts','f_user_contacts') AND table_id=OLD.`user_contact_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_contacts','f_user_contacts') AND table_id=OLD.`user_contact_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_2c0662728884fe78c0d774cc`$$
CREATE TRIGGER `tr_delete_2c0662728884fe78c0d774cc` AFTER DELETE ON `f_user_contacts` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_contacts','f_user_contacts') AND table_id=OLD.`user_contact_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_contacts','f_user_contacts') AND table_id=OLD.`user_contact_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_31d7a79bfec71a3d0952472d`$$
CREATE TRIGGER `tr_delete_31d7a79bfec71a3d0952472d` AFTER UPDATE ON `f_user_educations` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_educations','f_user_educations') AND table_id=OLD.`user_education_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_educations','f_user_educations') AND table_id=OLD.`user_education_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_c4fb37dda8f39a32cf2c6761`$$
CREATE TRIGGER `tr_delete_c4fb37dda8f39a32cf2c6761` AFTER DELETE ON `f_user_educations` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_educations','f_user_educations') AND table_id=OLD.`user_education_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_educations','f_user_educations') AND table_id=OLD.`user_education_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_7950fc3863c9087cd5bb4a97`$$
CREATE TRIGGER `tr_delete_7950fc3863c9087cd5bb4a97` AFTER UPDATE ON `f_user_events` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_events','f_user_events') AND table_id=OLD.`user_event_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_events','f_user_events') AND table_id=OLD.`user_event_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_49246e2163a2c6ed8a6091a3`$$
CREATE TRIGGER `tr_delete_49246e2163a2c6ed8a6091a3` AFTER DELETE ON `f_user_events` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_events','f_user_events') AND table_id=OLD.`user_event_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_events','f_user_events') AND table_id=OLD.`user_event_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_8915dc5483c0f3a5bc321831`$$
CREATE TRIGGER `tr_delete_8915dc5483c0f3a5bc321831` AFTER UPDATE ON `f_user_experiences` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_experiences','f_user_experiences') AND table_id=OLD.`user_experience_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_experiences','f_user_experiences') AND table_id=OLD.`user_experience_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_d2ebe76527217a63a6d9af2c`$$
CREATE TRIGGER `tr_delete_d2ebe76527217a63a6d9af2c` AFTER DELETE ON `f_user_experiences` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_experiences','f_user_experiences') AND table_id=OLD.`user_experience_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_experiences','f_user_experiences') AND table_id=OLD.`user_experience_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_4de4cd90bd311778f5ee98c8`$$
CREATE TRIGGER `tr_delete_4de4cd90bd311778f5ee98c8` AFTER UPDATE ON `f_user_merges` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_merges','f_user_merges') AND table_id=OLD.`user_merge_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_merges','f_user_merges') AND table_id=OLD.`user_merge_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_7d8da7101042f7f0bd64f292`$$
CREATE TRIGGER `tr_delete_7d8da7101042f7f0bd64f292` AFTER DELETE ON `f_user_merges` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_merges','f_user_merges') AND table_id=OLD.`user_merge_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_merges','f_user_merges') AND table_id=OLD.`user_merge_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_c572a70f65f7154da5c93923`$$
CREATE TRIGGER `tr_delete_c572a70f65f7154da5c93923` AFTER UPDATE ON `f_user_messages` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_messages','f_user_messages') AND table_id=OLD.`user_message_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_messages','f_user_messages') AND table_id=OLD.`user_message_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_d8f6b658969ffa2b238b6b95`$$
CREATE TRIGGER `tr_delete_d8f6b658969ffa2b238b6b95` AFTER DELETE ON `f_user_messages` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_messages','f_user_messages') AND table_id=OLD.`user_message_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_messages','f_user_messages') AND table_id=OLD.`user_message_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_7b8d627be09a8aa92e11b1f5`$$
CREATE TRIGGER `tr_delete_7b8d627be09a8aa92e11b1f5` AFTER UPDATE ON `f_user_permissions` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_permissions','f_user_permissions') AND table_id=OLD.`user_permission_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_permissions','f_user_permissions') AND table_id=OLD.`user_permission_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_a20609ccc40d8ad9506ed4a8`$$
CREATE TRIGGER `tr_delete_a20609ccc40d8ad9506ed4a8` AFTER DELETE ON `f_user_permissions` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_permissions','f_user_permissions') AND table_id=OLD.`user_permission_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_permissions','f_user_permissions') AND table_id=OLD.`user_permission_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_89168067e95a9460bf0ec82d`$$
CREATE TRIGGER `tr_delete_89168067e95a9460bf0ec82d` AFTER UPDATE ON `f_user_points` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_points','f_user_points') AND table_id=OLD.`user_point_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_points','f_user_points') AND table_id=OLD.`user_point_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_93112b5f32895c1c5021d0d9`$$
CREATE TRIGGER `tr_delete_93112b5f32895c1c5021d0d9` AFTER DELETE ON `f_user_points` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_points','f_user_points') AND table_id=OLD.`user_point_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_points','f_user_points') AND table_id=OLD.`user_point_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_c69bd41729610a2ac48dd205`$$
CREATE TRIGGER `tr_delete_c69bd41729610a2ac48dd205` AFTER UPDATE ON `f_user_point_rules` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_point_rules','f_user_point_rules') AND table_id=OLD.`user_point_rule_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_point_rules','f_user_point_rules') AND table_id=OLD.`user_point_rule_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_50317c4d1c8a0720765533af`$$
CREATE TRIGGER `tr_delete_50317c4d1c8a0720765533af` AFTER DELETE ON `f_user_point_rules` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_point_rules','f_user_point_rules') AND table_id=OLD.`user_point_rule_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_point_rules','f_user_point_rules') AND table_id=OLD.`user_point_rule_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_0b7d9c7c6108135c77bd7806`$$
CREATE TRIGGER `tr_delete_0b7d9c7c6108135c77bd7806` AFTER UPDATE ON `f_user_polls` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_polls','f_user_polls') AND table_id=OLD.`user_poll_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_polls','f_user_polls') AND table_id=OLD.`user_poll_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_4698e3e2e1ef92c087352191`$$
CREATE TRIGGER `tr_delete_4698e3e2e1ef92c087352191` AFTER DELETE ON `f_user_polls` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_polls','f_user_polls') AND table_id=OLD.`user_poll_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_polls','f_user_polls') AND table_id=OLD.`user_poll_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_fce4882b9e875e71e3fbb9a1`$$
CREATE TRIGGER `tr_delete_fce4882b9e875e71e3fbb9a1` AFTER UPDATE ON `f_user_poll_options` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_poll_options','f_user_poll_options') AND table_id=OLD.`user_poll_option_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_poll_options','f_user_poll_options') AND table_id=OLD.`user_poll_option_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_6ea04a372c54ad882e926ea0`$$
CREATE TRIGGER `tr_delete_6ea04a372c54ad882e926ea0` AFTER DELETE ON `f_user_poll_options` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_poll_options','f_user_poll_options') AND table_id=OLD.`user_poll_option_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_poll_options','f_user_poll_options') AND table_id=OLD.`user_poll_option_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_1e43244fc8d7394da6a0f8dc`$$
CREATE TRIGGER `tr_delete_1e43244fc8d7394da6a0f8dc` AFTER UPDATE ON `f_user_poll_votes` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_poll_votes','f_user_poll_votes') AND table_id=OLD.`user_poll_vote_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_poll_votes','f_user_poll_votes') AND table_id=OLD.`user_poll_vote_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_4387bc3805af10df48085837`$$
CREATE TRIGGER `tr_delete_4387bc3805af10df48085837` AFTER DELETE ON `f_user_poll_votes` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_poll_votes','f_user_poll_votes') AND table_id=OLD.`user_poll_vote_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_poll_votes','f_user_poll_votes') AND table_id=OLD.`user_poll_vote_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_5367a3f0629ac9679311e2ac`$$
CREATE TRIGGER `tr_delete_5367a3f0629ac9679311e2ac` AFTER UPDATE ON `f_user_profiles` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('z_user_profiles','f_user_profiles') AND table_id=OLD.`user_profile_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('z_user_profiles','f_user_profiles') AND table_id=OLD.`user_profile_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_24074551be5c780f93835887`$$
CREATE TRIGGER `tr_delete_24074551be5c780f93835887` AFTER DELETE ON `f_user_profiles` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('z_user_profiles','f_user_profiles') AND table_id=OLD.`user_profile_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('z_user_profiles','f_user_profiles') AND table_id=OLD.`user_profile_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_020a9ca19ec305ce302321c7`$$
CREATE TRIGGER `tr_delete_020a9ca19ec305ce302321c7` AFTER UPDATE ON `f_user_publications` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_publications','f_user_publications') AND table_id=OLD.`user_publication_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_publications','f_user_publications') AND table_id=OLD.`user_publication_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_d1da00b3e85986e83752776f`$$
CREATE TRIGGER `tr_delete_d1da00b3e85986e83752776f` AFTER DELETE ON `f_user_publications` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_publications','f_user_publications') AND table_id=OLD.`user_publication_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_publications','f_user_publications') AND table_id=OLD.`user_publication_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_3dd7d8ed94baa032400f7b88`$$
CREATE TRIGGER `tr_delete_3dd7d8ed94baa032400f7b88` AFTER UPDATE ON `f_user_referrals` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_referrals','f_user_referrals') AND table_id=OLD.`user_referral_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_referrals','f_user_referrals') AND table_id=OLD.`user_referral_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_7c637a690b33a5bc3085af13`$$
CREATE TRIGGER `tr_delete_7c637a690b33a5bc3085af13` AFTER DELETE ON `f_user_referrals` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_referrals','f_user_referrals') AND table_id=OLD.`user_referral_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_referrals','f_user_referrals') AND table_id=OLD.`user_referral_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_9e9df11666c96fd317326b65`$$
CREATE TRIGGER `tr_delete_9e9df11666c96fd317326b65` AFTER UPDATE ON `f_user_roles` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_roles','f_user_roles') AND table_id=OLD.`user_role_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_roles','f_user_roles') AND table_id=OLD.`user_role_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_fb6ef82e1ebc47015ad23514`$$
CREATE TRIGGER `tr_delete_fb6ef82e1ebc47015ad23514` AFTER DELETE ON `f_user_roles` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_roles','f_user_roles') AND table_id=OLD.`user_role_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_roles','f_user_roles') AND table_id=OLD.`user_role_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_a4af9479b1ef543321489c6d`$$
CREATE TRIGGER `tr_delete_a4af9479b1ef543321489c6d` AFTER UPDATE ON `f_user_sessions` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_sessions','f_user_sessions') AND table_id=OLD.`user_session_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_sessions','f_user_sessions') AND table_id=OLD.`user_session_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_b47cbaa1d7bca30453ff1c9a`$$
CREATE TRIGGER `tr_delete_b47cbaa1d7bca30453ff1c9a` AFTER DELETE ON `f_user_sessions` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_sessions','f_user_sessions') AND table_id=OLD.`user_session_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_sessions','f_user_sessions') AND table_id=OLD.`user_session_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_2afc5e290bd11b43f03d1227`$$
CREATE TRIGGER `tr_delete_2afc5e290bd11b43f03d1227` AFTER UPDATE ON `f_user_settings` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('z_user_settings','f_user_settings') AND table_id=OLD.`user_setting_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('z_user_settings','f_user_settings') AND table_id=OLD.`user_setting_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_e19dbcfdecf2407f2f214875`$$
CREATE TRIGGER `tr_delete_e19dbcfdecf2407f2f214875` AFTER DELETE ON `f_user_settings` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('z_user_settings','f_user_settings') AND table_id=OLD.`user_setting_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('z_user_settings','f_user_settings') AND table_id=OLD.`user_setting_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_579882c45100fc4073a8ad4a`$$
CREATE TRIGGER `tr_delete_579882c45100fc4073a8ad4a` AFTER UPDATE ON `f_verification_levels` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('verification_levels','f_verification_levels') AND table_id=OLD.`verification_level_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('verification_levels','f_verification_levels') AND table_id=OLD.`verification_level_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_b43d8c6d9106abbc9e9ef7c8`$$
CREATE TRIGGER `tr_delete_b43d8c6d9106abbc9e9ef7c8` AFTER DELETE ON `f_verification_levels` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('verification_levels','f_verification_levels') AND table_id=OLD.`verification_level_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('verification_levels','f_verification_levels') AND table_id=OLD.`verification_level_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_089dae37dde198ab82ec16c6`$$
CREATE TRIGGER `tr_delete_089dae37dde198ab82ec16c6` AFTER DELETE ON `f_world_iran_counties` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('world_iran_counties','f_world_iran_counties') AND table_id=OLD.`county_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('world_iran_counties','f_world_iran_counties') AND table_id=OLD.`county_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_99c942c173f21f254db812ef`$$
CREATE TRIGGER `tr_delete_99c942c173f21f254db812ef` AFTER DELETE ON `f_world_iran_provinces` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('world_iran_provinces','f_world_iran_provinces') AND table_id=OLD.`province_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('world_iran_provinces','f_world_iran_provinces') AND table_id=OLD.`province_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_900e44d8b0776732b6a8a8fc`$$
CREATE TRIGGER `tr_delete_900e44d8b0776732b6a8a8fc` AFTER UPDATE ON `p_academies` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academies','p_academies') AND table_id=OLD.`academy_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academies','p_academies') AND table_id=OLD.`academy_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_59be5ea68ca8ce16815cefea`$$
CREATE TRIGGER `tr_delete_59be5ea68ca8ce16815cefea` AFTER DELETE ON `p_academies` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academies','p_academies') AND table_id=OLD.`academy_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academies','p_academies') AND table_id=OLD.`academy_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_8b4eeb4ee197674104d1d01b`$$
CREATE TRIGGER `tr_delete_8b4eeb4ee197674104d1d01b` AFTER UPDATE ON `p_academy_branches` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branches','p_academy_branches') AND table_id=OLD.`branch_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branches','p_academy_branches') AND table_id=OLD.`branch_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_87f87522f42470fcff3bc569`$$
CREATE TRIGGER `tr_delete_87f87522f42470fcff3bc569` AFTER DELETE ON `p_academy_branches` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branches','p_academy_branches') AND table_id=OLD.`branch_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branches','p_academy_branches') AND table_id=OLD.`branch_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_86055924509ed4e0b45a87a1`$$
CREATE TRIGGER `tr_delete_86055924509ed4e0b45a87a1` AFTER UPDATE ON `p_academy_branch_bookings` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_bookings','p_academy_branch_bookings') AND table_id=OLD.`booking_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_bookings','p_academy_branch_bookings') AND table_id=OLD.`booking_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_71dbffd616a052597512709a`$$
CREATE TRIGGER `tr_delete_71dbffd616a052597512709a` AFTER DELETE ON `p_academy_branch_bookings` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_bookings','p_academy_branch_bookings') AND table_id=OLD.`booking_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_bookings','p_academy_branch_bookings') AND table_id=OLD.`booking_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_ebd02af46693f8d0eda02157`$$
CREATE TRIGGER `tr_delete_ebd02af46693f8d0eda02157` AFTER UPDATE ON `p_academy_branch_classrooms` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_classrooms','p_academy_branch_classrooms') AND table_id=OLD.`classroom_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_classrooms','p_academy_branch_classrooms') AND table_id=OLD.`classroom_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_d44090a56db3294c08438b11`$$
CREATE TRIGGER `tr_delete_d44090a56db3294c08438b11` AFTER DELETE ON `p_academy_branch_classrooms` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_classrooms','p_academy_branch_classrooms') AND table_id=OLD.`classroom_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_classrooms','p_academy_branch_classrooms') AND table_id=OLD.`classroom_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_92e3d9747e16257df7490665`$$
CREATE TRIGGER `tr_delete_92e3d9747e16257df7490665` AFTER UPDATE ON `p_academy_branch_classroom_assets` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_classroom_assets','p_academy_branch_classroom_assets') AND table_id=OLD.`classroom_asset_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_classroom_assets','p_academy_branch_classroom_assets') AND table_id=OLD.`classroom_asset_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_9f399d185b089b10717c4360`$$
CREATE TRIGGER `tr_delete_9f399d185b089b10717c4360` AFTER DELETE ON `p_academy_branch_classroom_assets` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_classroom_assets','p_academy_branch_classroom_assets') AND table_id=OLD.`classroom_asset_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_classroom_assets','p_academy_branch_classroom_assets') AND table_id=OLD.`classroom_asset_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_fa0c2021cb6f5861258988fe`$$
CREATE TRIGGER `tr_delete_fa0c2021cb6f5861258988fe` AFTER UPDATE ON `p_academy_branch_courses` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_courses','p_academy_branch_courses') AND table_id=OLD.`course_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_courses','p_academy_branch_courses') AND table_id=OLD.`course_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_40b455aa176f40ba94ce5501`$$
CREATE TRIGGER `tr_delete_40b455aa176f40ba94ce5501` AFTER DELETE ON `p_academy_branch_courses` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_courses','p_academy_branch_courses') AND table_id=OLD.`course_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_courses','p_academy_branch_courses') AND table_id=OLD.`course_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_4af3507f10542551ae752c30`$$
CREATE TRIGGER `tr_delete_4af3507f10542551ae752c30` AFTER UPDATE ON `p_academy_branch_course_terms` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_course_terms','p_academy_branch_course_terms') AND table_id=OLD.`term_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_course_terms','p_academy_branch_course_terms') AND table_id=OLD.`term_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_94f9e17f132c594631619fdf`$$
CREATE TRIGGER `tr_delete_94f9e17f132c594631619fdf` AFTER DELETE ON `p_academy_branch_course_terms` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_course_terms','p_academy_branch_course_terms') AND table_id=OLD.`term_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_course_terms','p_academy_branch_course_terms') AND table_id=OLD.`term_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_7960971c0a78f9499ac889a8`$$
CREATE TRIGGER `tr_delete_7960971c0a78f9499ac889a8` AFTER UPDATE ON `p_academy_branch_course_term_enrollments` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_course_term_enrollments','p_academy_branch_course_term_enrollments') AND table_id=OLD.`term_enrollment_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_course_term_enrollments','p_academy_branch_course_term_enrollments') AND table_id=OLD.`term_enrollment_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_185894e86514d63c183a7199`$$
CREATE TRIGGER `tr_delete_185894e86514d63c183a7199` AFTER DELETE ON `p_academy_branch_course_term_enrollments` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_course_term_enrollments','p_academy_branch_course_term_enrollments') AND table_id=OLD.`term_enrollment_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_course_term_enrollments','p_academy_branch_course_term_enrollments') AND table_id=OLD.`term_enrollment_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_685854e464713cf931eae5d8`$$
CREATE TRIGGER `tr_delete_685854e464713cf931eae5d8` AFTER UPDATE ON `p_academy_branch_course_term_invoices` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_course_term_invoices','p_academy_branch_course_term_invoices') AND table_id=OLD.`term_invoice_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_course_term_invoices','p_academy_branch_course_term_invoices') AND table_id=OLD.`term_invoice_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_cfc6608bff82c93e148d97be`$$
CREATE TRIGGER `tr_delete_cfc6608bff82c93e148d97be` AFTER DELETE ON `p_academy_branch_course_term_invoices` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_course_term_invoices','p_academy_branch_course_term_invoices') AND table_id=OLD.`term_invoice_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_course_term_invoices','p_academy_branch_course_term_invoices') AND table_id=OLD.`term_invoice_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_85d8bfd09cba53a7b882d14b`$$
CREATE TRIGGER `tr_delete_85d8bfd09cba53a7b882d14b` AFTER UPDATE ON `p_academy_branch_course_term_invoice_installments` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_course_term_invoice_installments','p_academy_branch_course_term_invoice_installments') AND table_id=OLD.`term_invoice_installment_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_course_term_invoice_installments','p_academy_branch_course_term_invoice_installments') AND table_id=OLD.`term_invoice_installment_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_b894225dd844b5d284a15b23`$$
CREATE TRIGGER `tr_delete_b894225dd844b5d284a15b23` AFTER DELETE ON `p_academy_branch_course_term_invoice_installments` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_course_term_invoice_installments','p_academy_branch_course_term_invoice_installments') AND table_id=OLD.`term_invoice_installment_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_course_term_invoice_installments','p_academy_branch_course_term_invoice_installments') AND table_id=OLD.`term_invoice_installment_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_ec94a290b57d197887bf1646`$$
CREATE TRIGGER `tr_delete_ec94a290b57d197887bf1646` AFTER UPDATE ON `p_academy_branch_course_term_schedule_skips` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_course_term_schedule_skips','p_academy_branch_course_term_schedule_skips') AND table_id=OLD.`term_schedule_skip_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_course_term_schedule_skips','p_academy_branch_course_term_schedule_skips') AND table_id=OLD.`term_schedule_skip_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_b112bff653538a6a869cea47`$$
CREATE TRIGGER `tr_delete_b112bff653538a6a869cea47` AFTER DELETE ON `p_academy_branch_course_term_schedule_skips` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_course_term_schedule_skips','p_academy_branch_course_term_schedule_skips') AND table_id=OLD.`term_schedule_skip_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_course_term_schedule_skips','p_academy_branch_course_term_schedule_skips') AND table_id=OLD.`term_schedule_skip_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_ff56110e9b1977dfde479ce0`$$
CREATE TRIGGER `tr_delete_ff56110e9b1977dfde479ce0` AFTER UPDATE ON `p_academy_branch_course_term_sessions` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_course_term_sessions','p_academy_branch_course_term_sessions') AND table_id=OLD.`term_session_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_course_term_sessions','p_academy_branch_course_term_sessions') AND table_id=OLD.`term_session_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_3befca5d3533241c8ae15b1c`$$
CREATE TRIGGER `tr_delete_3befca5d3533241c8ae15b1c` AFTER DELETE ON `p_academy_branch_course_term_sessions` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_course_term_sessions','p_academy_branch_course_term_sessions') AND table_id=OLD.`term_session_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_course_term_sessions','p_academy_branch_course_term_sessions') AND table_id=OLD.`term_session_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_849b2ab51ba9c665ee66163a`$$
CREATE TRIGGER `tr_delete_849b2ab51ba9c665ee66163a` AFTER UPDATE ON `p_academy_branch_course_term_session_attendances` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_course_term_session_attendances','p_academy_branch_course_term_session_attendances') AND table_id=OLD.`session_attendance_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_course_term_session_attendances','p_academy_branch_course_term_session_attendances') AND table_id=OLD.`session_attendance_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_71551502b4a03d9d146c21e6`$$
CREATE TRIGGER `tr_delete_71551502b4a03d9d146c21e6` AFTER DELETE ON `p_academy_branch_course_term_session_attendances` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_course_term_session_attendances','p_academy_branch_course_term_session_attendances') AND table_id=OLD.`session_attendance_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_course_term_session_attendances','p_academy_branch_course_term_session_attendances') AND table_id=OLD.`session_attendance_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_b7bfe7c02d1a4b0fd266d248`$$
CREATE TRIGGER `tr_delete_b7bfe7c02d1a4b0fd266d248` AFTER UPDATE ON `p_academy_branch_course_term_waiting_list` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_course_term_waiting_list','p_academy_branch_course_term_waiting_list') AND table_id=OLD.`term_waiting_list_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_course_term_waiting_list','p_academy_branch_course_term_waiting_list') AND table_id=OLD.`term_waiting_list_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_dbf986178741a5b67a418f38`$$
CREATE TRIGGER `tr_delete_dbf986178741a5b67a418f38` AFTER DELETE ON `p_academy_branch_course_term_waiting_list` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_course_term_waiting_list','p_academy_branch_course_term_waiting_list') AND table_id=OLD.`term_waiting_list_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_course_term_waiting_list','p_academy_branch_course_term_waiting_list') AND table_id=OLD.`term_waiting_list_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_f8baca8c51627754fdf4c686`$$
CREATE TRIGGER `tr_delete_f8baca8c51627754fdf4c686` AFTER UPDATE ON `p_academy_branch_members` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_members','p_academy_branch_members') AND table_id=OLD.`member_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_members','p_academy_branch_members') AND table_id=OLD.`member_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_6652155830303e314d7bf828`$$
CREATE TRIGGER `tr_delete_6652155830303e314d7bf828` AFTER DELETE ON `p_academy_branch_members` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_members','p_academy_branch_members') AND table_id=OLD.`member_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_members','p_academy_branch_members') AND table_id=OLD.`member_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_901efee5530cb653bf16aeb4`$$
CREATE TRIGGER `tr_delete_901efee5530cb653bf16aeb4` AFTER UPDATE ON `p_academy_branch_member_contracts` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_member_contracts','p_academy_branch_member_contracts') AND table_id=OLD.`member_contract_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_member_contracts','p_academy_branch_member_contracts') AND table_id=OLD.`member_contract_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_53450572dd659713aed65bf8`$$
CREATE TRIGGER `tr_delete_53450572dd659713aed65bf8` AFTER DELETE ON `p_academy_branch_member_contracts` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_member_contracts','p_academy_branch_member_contracts') AND table_id=OLD.`member_contract_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_member_contracts','p_academy_branch_member_contracts') AND table_id=OLD.`member_contract_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_e85b6c7ad081f7855a9df128`$$
CREATE TRIGGER `tr_delete_e85b6c7ad081f7855a9df128` AFTER UPDATE ON `p_academy_branch_member_permissions` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_member_permissions','p_academy_branch_member_permissions') AND table_id=OLD.`academy_branch_member_permission_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_member_permissions','p_academy_branch_member_permissions') AND table_id=OLD.`academy_branch_member_permission_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_28d6d1d076efd598fda64711`$$
CREATE TRIGGER `tr_delete_28d6d1d076efd598fda64711` AFTER DELETE ON `p_academy_branch_member_permissions` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_member_permissions','p_academy_branch_member_permissions') AND table_id=OLD.`academy_branch_member_permission_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_member_permissions','p_academy_branch_member_permissions') AND table_id=OLD.`academy_branch_member_permission_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_a70d47b4d4ad45a860dd35fd`$$
CREATE TRIGGER `tr_delete_a70d47b4d4ad45a860dd35fd` AFTER UPDATE ON `p_academy_branch_member_roles` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_member_roles','p_academy_branch_member_roles') AND table_id=OLD.`member_role_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_member_roles','p_academy_branch_member_roles') AND table_id=OLD.`member_role_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_a3d70153986845a737c752de`$$
CREATE TRIGGER `tr_delete_a3d70153986845a737c752de` AFTER DELETE ON `p_academy_branch_member_roles` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_member_roles','p_academy_branch_member_roles') AND table_id=OLD.`member_role_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_member_roles','p_academy_branch_member_roles') AND table_id=OLD.`member_role_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_7318ba1cd51b91575d9d4f06`$$
CREATE TRIGGER `tr_delete_7318ba1cd51b91575d9d4f06` AFTER UPDATE ON `p_academy_branch_scheduling_rules` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_scheduling_rules','p_academy_branch_scheduling_rules') AND table_id=OLD.`scheduling_rule_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_scheduling_rules','p_academy_branch_scheduling_rules') AND table_id=OLD.`scheduling_rule_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_7daff6e4a1dbb3e7eeb592d9`$$
CREATE TRIGGER `tr_delete_7daff6e4a1dbb3e7eeb592d9` AFTER DELETE ON `p_academy_branch_scheduling_rules` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_scheduling_rules','p_academy_branch_scheduling_rules') AND table_id=OLD.`scheduling_rule_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_scheduling_rules','p_academy_branch_scheduling_rules') AND table_id=OLD.`scheduling_rule_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_fa7a86fbd9c424ec70dc652c`$$
CREATE TRIGGER `tr_delete_fa7a86fbd9c424ec70dc652c` AFTER UPDATE ON `p_academy_branch_types` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_types','p_academy_branch_types') AND table_id=OLD.`academy_branch_type_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_branch_types','p_academy_branch_types') AND table_id=OLD.`academy_branch_type_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_000de715e6a7b54fd0cb636b`$$
CREATE TRIGGER `tr_delete_000de715e6a7b54fd0cb636b` AFTER DELETE ON `p_academy_branch_types` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_types','p_academy_branch_types') AND table_id=OLD.`academy_branch_type_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_branch_types','p_academy_branch_types') AND table_id=OLD.`academy_branch_type_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_ab5d120d4bf79ecd5016fcfb`$$
CREATE TRIGGER `tr_delete_ab5d120d4bf79ecd5016fcfb` AFTER UPDATE ON `p_academy_documents` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_documents','p_academy_documents') AND table_id=OLD.`academy_document_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_documents','p_academy_documents') AND table_id=OLD.`academy_document_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_39e34f69d132da1910aa3347`$$
CREATE TRIGGER `tr_delete_39e34f69d132da1910aa3347` AFTER DELETE ON `p_academy_documents` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_documents','p_academy_documents') AND table_id=OLD.`academy_document_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_documents','p_academy_documents') AND table_id=OLD.`academy_document_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_a52f63e1745b7d94503641f9`$$
CREATE TRIGGER `tr_delete_a52f63e1745b7d94503641f9` AFTER DELETE ON `p_academy_national_holiday_settings` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('academy_national_holiday_settings','p_academy_national_holiday_settings') AND table_id=OLD.`academy_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('academy_national_holiday_settings','p_academy_national_holiday_settings') AND table_id=OLD.`academy_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_d9fc2130b25d85272127baf3`$$
CREATE TRIGGER `tr_delete_d9fc2130b25d85272127baf3` AFTER UPDATE ON `p_academy_subscription_payments` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_subscription_payments','p_academy_subscription_payments') AND table_id=OLD.`subscription_payment_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_subscription_payments','p_academy_subscription_payments') AND table_id=OLD.`subscription_payment_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_9d4e0294bd90453252585121`$$
CREATE TRIGGER `tr_delete_9d4e0294bd90453252585121` AFTER DELETE ON `p_academy_subscription_payments` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_subscription_payments','p_academy_subscription_payments') AND table_id=OLD.`subscription_payment_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_subscription_payments','p_academy_subscription_payments') AND table_id=OLD.`subscription_payment_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_6db1252e115d1dc89fc23afc`$$
CREATE TRIGGER `tr_delete_6db1252e115d1dc89fc23afc` AFTER UPDATE ON `p_academy_subscription_periods` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_subscription_periods','p_academy_subscription_periods') AND table_id=OLD.`subscription_period_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('academy_subscription_periods','p_academy_subscription_periods') AND table_id=OLD.`subscription_period_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_8a0542c44919dec393d9c631`$$
CREATE TRIGGER `tr_delete_8a0542c44919dec393d9c631` AFTER DELETE ON `p_academy_subscription_periods` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_subscription_periods','p_academy_subscription_periods') AND table_id=OLD.`subscription_period_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('academy_subscription_periods','p_academy_subscription_periods') AND table_id=OLD.`subscription_period_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_5f2bb32b4e1a2715a977e056`$$
CREATE TRIGGER `tr_delete_5f2bb32b4e1a2715a977e056` AFTER UPDATE ON `p_classroom_types` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('classroom_types','p_classroom_types') AND table_id=OLD.`classroom_type_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('classroom_types','p_classroom_types') AND table_id=OLD.`classroom_type_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_7ac9a813572e59ceedbaf343`$$
CREATE TRIGGER `tr_delete_7ac9a813572e59ceedbaf343` AFTER DELETE ON `p_classroom_types` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('classroom_types','p_classroom_types') AND table_id=OLD.`classroom_type_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('classroom_types','p_classroom_types') AND table_id=OLD.`classroom_type_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_1189bc526e8eb3048cf5dd00`$$
CREATE TRIGGER `tr_delete_1189bc526e8eb3048cf5dd00` AFTER DELETE ON `p_creator_courses` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('creator_courses','p_creator_courses') AND table_id=OLD.`id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('creator_courses','p_creator_courses') AND table_id=OLD.`id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_29391031fe1427d138c7149f`$$
CREATE TRIGGER `tr_delete_29391031fe1427d138c7149f` AFTER DELETE ON `p_creator_course_details` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('creator_course_details','p_creator_course_details') AND table_id=OLD.`course_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('creator_course_details','p_creator_course_details') AND table_id=OLD.`course_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_8a86eb44698d767fc7558c36`$$
CREATE TRIGGER `tr_delete_8a86eb44698d767fc7558c36` AFTER UPDATE ON `p_creator_course_lessons` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=@sornaz_deleted_by WHERE table_name IN ('creator_course_lessons','p_creator_course_lessons') AND table_id=OLD.`post_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=@sornaz_deleted_by WHERE table_name IN ('creator_course_lessons','p_creator_course_lessons') AND table_id=OLD.`post_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_31e6576f66933ea75b12f91f`$$
CREATE TRIGGER `tr_delete_31e6576f66933ea75b12f91f` AFTER DELETE ON `p_creator_course_lessons` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('creator_course_lessons','p_creator_course_lessons') AND table_id=OLD.`post_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('creator_course_lessons','p_creator_course_lessons') AND table_id=OLD.`post_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_aa5972c98710799d0dfad5a9`$$
CREATE TRIGGER `tr_delete_aa5972c98710799d0dfad5a9` AFTER DELETE ON `p_creator_course_media` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('creator_course_media','p_creator_course_media') AND table_id=OLD.`id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('creator_course_media','p_creator_course_media') AND table_id=OLD.`id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_c7a6c8df626d7331a9989c36`$$
CREATE TRIGGER `tr_delete_c7a6c8df626d7331a9989c36` AFTER DELETE ON `p_creator_course_orders` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('creator_course_orders','p_creator_course_orders') AND table_id=OLD.`id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('creator_course_orders','p_creator_course_orders') AND table_id=OLD.`id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_131fe0ad9e0ebecd5ff4c48f`$$
CREATE TRIGGER `tr_delete_131fe0ad9e0ebecd5ff4c48f` AFTER DELETE ON `p_creator_course_questions` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('creator_course_questions','p_creator_course_questions') AND table_id=OLD.`id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('creator_course_questions','p_creator_course_questions') AND table_id=OLD.`id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_9c5be08b8b5bb7eb8437df5d`$$
CREATE TRIGGER `tr_delete_9c5be08b8b5bb7eb8437df5d` AFTER DELETE ON `p_creator_course_reports` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('creator_course_reports','p_creator_course_reports') AND table_id=OLD.`id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('creator_course_reports','p_creator_course_reports') AND table_id=OLD.`id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_2e495ed076f5e7e4ab20f8fb`$$
CREATE TRIGGER `tr_delete_2e495ed076f5e7e4ab20f8fb` AFTER DELETE ON `p_creator_course_reviews` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('creator_course_reviews','p_creator_course_reviews') AND table_id=OLD.`id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('creator_course_reviews','p_creator_course_reviews') AND table_id=OLD.`id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_64a2b487b34fd42067897296`$$
CREATE TRIGGER `tr_delete_64a2b487b34fd42067897296` AFTER UPDATE ON `p_instruments` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('instruments','p_instruments') AND table_id=OLD.`instrument_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('instruments','p_instruments') AND table_id=OLD.`instrument_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_2025421f2fd5b4a251b726f4`$$
CREATE TRIGGER `tr_delete_2025421f2fd5b4a251b726f4` AFTER DELETE ON `p_instruments` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('instruments','p_instruments') AND table_id=OLD.`instrument_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('instruments','p_instruments') AND table_id=OLD.`instrument_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_c1cb12288f49cf73022f3592`$$
CREATE TRIGGER `tr_delete_c1cb12288f49cf73022f3592` AFTER UPDATE ON `p_lessons` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('lessons','p_lessons') AND table_id=OLD.`lesson_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('lessons','p_lessons') AND table_id=OLD.`lesson_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_6f38a9246ad3f8ed7b1ba0d7`$$
CREATE TRIGGER `tr_delete_6f38a9246ad3f8ed7b1ba0d7` AFTER DELETE ON `p_lessons` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('lessons','p_lessons') AND table_id=OLD.`lesson_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('lessons','p_lessons') AND table_id=OLD.`lesson_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_02b1c3b8725413e501b769ac`$$
CREATE TRIGGER `tr_delete_02b1c3b8725413e501b769ac` AFTER UPDATE ON `p_levels` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('levels','p_levels') AND table_id=OLD.`level_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('levels','p_levels') AND table_id=OLD.`level_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_0bb60bcf0d0952dca58cf52e`$$
CREATE TRIGGER `tr_delete_0bb60bcf0d0952dca58cf52e` AFTER DELETE ON `p_levels` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('levels','p_levels') AND table_id=OLD.`level_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('levels','p_levels') AND table_id=OLD.`level_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_2a76eb5b770d260f6cbafc51`$$
CREATE TRIGGER `tr_delete_2a76eb5b770d260f6cbafc51` AFTER UPDATE ON `p_music_sheets` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=@sornaz_deleted_by WHERE table_name IN ('music_sheets','p_music_sheets') AND table_id=OLD.`id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=@sornaz_deleted_by WHERE table_name IN ('music_sheets','p_music_sheets') AND table_id=OLD.`id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_0b8dac5e0d6fecd8f63d62a6`$$
CREATE TRIGGER `tr_delete_0b8dac5e0d6fecd8f63d62a6` AFTER DELETE ON `p_music_sheets` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('music_sheets','p_music_sheets') AND table_id=OLD.`id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=@sornaz_deleted_by WHERE table_name IN ('music_sheets','p_music_sheets') AND table_id=OLD.`id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_0718cfd8a7a3c3d1cc020e87`$$
CREATE TRIGGER `tr_delete_0718cfd8a7a3c3d1cc020e87` AFTER UPDATE ON `p_settings` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('settings','p_settings') AND table_id=OLD.`setting_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('settings','p_settings') AND table_id=OLD.`setting_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_c31b81e0f08b261e75e05b4d`$$
CREATE TRIGGER `tr_delete_c31b81e0f08b261e75e05b4d` AFTER DELETE ON `p_settings` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('settings','p_settings') AND table_id=OLD.`setting_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('settings','p_settings') AND table_id=OLD.`setting_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_72adcc991b36ac9ffd01c78b`$$
CREATE TRIGGER `tr_delete_72adcc991b36ac9ffd01c78b` AFTER UPDATE ON `p_user_instruments` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_instruments','p_user_instruments') AND table_id=OLD.`user_instrument_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_instruments','p_user_instruments') AND table_id=OLD.`user_instrument_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_f36a4dea98d2f4f11c51304a`$$
CREATE TRIGGER `tr_delete_f36a4dea98d2f4f11c51304a` AFTER DELETE ON `p_user_instruments` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_instruments','p_user_instruments') AND table_id=OLD.`user_instrument_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_instruments','p_user_instruments') AND table_id=OLD.`user_instrument_id` AND deleted_at IS NULL;

END$$
DROP TRIGGER IF EXISTS `tr_delete_fc5956647f16cc5e1f094011`$$
CREATE TRIGGER `tr_delete_fc5956647f16cc5e1f094011` AFTER UPDATE ON `p_user_lessons` FOR EACH ROW BEGIN
IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
UPDATE `p_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_lessons','p_user_lessons') AND table_id=OLD.`user_lesson_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=NEW.deleted_at,deleted_by=NEW.deleted_by WHERE table_name IN ('user_lessons','p_user_lessons') AND table_id=OLD.`user_lesson_id` AND deleted_at IS NULL;
END IF;
END$$
DROP TRIGGER IF EXISTS `tr_delete_92bca87fd2d6092036e83604`$$
CREATE TRIGGER `tr_delete_92bca87fd2d6092036e83604` AFTER DELETE ON `p_user_lessons` FOR EACH ROW BEGIN

UPDATE `p_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_lessons','p_user_lessons') AND table_id=OLD.`user_lesson_id` AND deleted_at IS NULL;
UPDATE `f_translations` SET deleted_at=CURRENT_TIMESTAMP,deleted_by=COALESCE(@sornaz_deleted_by,OLD.deleted_by) WHERE table_name IN ('user_lessons','p_user_lessons') AND table_id=OLD.`user_lesson_id` AND deleted_at IS NULL;

END$$
DELIMITER ;
