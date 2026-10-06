-- Read-only. Every row in the first result must be ready.
SELECT m.logical_name,m.physical_name,
CASE WHEN src.TABLE_NAME IS NULL THEN 'MISSING_SOURCE'
 WHEN src.TABLE_TYPE <> 'BASE TABLE' OR src.ENGINE <> 'InnoDB' THEN 'REVIEW_ENGINE'
 WHEN m.logical_name <> m.physical_name AND dst.TABLE_NAME IS NOT NULL THEN 'TARGET_COLLISION'
 ELSE 'READY' END AS migration_status
FROM (
SELECT 'academies' AS logical_name, 'p_academies' AS physical_name
UNION ALL
SELECT 'academy_branches' AS logical_name, 'p_academy_branches' AS physical_name
UNION ALL
SELECT 'academy_branch_bookings' AS logical_name, 'p_academy_branch_bookings' AS physical_name
UNION ALL
SELECT 'academy_branch_classrooms' AS logical_name, 'p_academy_branch_classrooms' AS physical_name
UNION ALL
SELECT 'academy_branch_classroom_assets' AS logical_name, 'p_academy_branch_classroom_assets' AS physical_name
UNION ALL
SELECT 'academy_branch_courses' AS logical_name, 'p_academy_branch_courses' AS physical_name
UNION ALL
SELECT 'academy_branch_course_terms' AS logical_name, 'p_academy_branch_course_terms' AS physical_name
UNION ALL
SELECT 'academy_branch_course_term_enrollments' AS logical_name, 'p_academy_branch_course_term_enrollments' AS physical_name
UNION ALL
SELECT 'academy_branch_course_term_invoices' AS logical_name, 'p_academy_branch_course_term_invoices' AS physical_name
UNION ALL
SELECT 'academy_branch_course_term_invoice_installments' AS logical_name, 'p_academy_branch_course_term_invoice_installments' AS physical_name
UNION ALL
SELECT 'academy_branch_course_term_schedule_skips' AS logical_name, 'p_academy_branch_course_term_schedule_skips' AS physical_name
UNION ALL
SELECT 'academy_branch_course_term_sessions' AS logical_name, 'p_academy_branch_course_term_sessions' AS physical_name
UNION ALL
SELECT 'academy_branch_course_term_session_attendances' AS logical_name, 'p_academy_branch_course_term_session_attendances' AS physical_name
UNION ALL
SELECT 'academy_branch_course_term_waiting_list' AS logical_name, 'p_academy_branch_course_term_waiting_list' AS physical_name
UNION ALL
SELECT 'academy_branch_members' AS logical_name, 'p_academy_branch_members' AS physical_name
UNION ALL
SELECT 'academy_branch_member_contracts' AS logical_name, 'p_academy_branch_member_contracts' AS physical_name
UNION ALL
SELECT 'academy_branch_member_permissions' AS logical_name, 'p_academy_branch_member_permissions' AS physical_name
UNION ALL
SELECT 'academy_branch_member_roles' AS logical_name, 'p_academy_branch_member_roles' AS physical_name
UNION ALL
SELECT 'academy_branch_scheduling_rules' AS logical_name, 'p_academy_branch_scheduling_rules' AS physical_name
UNION ALL
SELECT 'academy_branch_types' AS logical_name, 'p_academy_branch_types' AS physical_name
UNION ALL
SELECT 'academy_documents' AS logical_name, 'p_academy_documents' AS physical_name
UNION ALL
SELECT 'academy_national_holiday_settings' AS logical_name, 'p_academy_national_holiday_settings' AS physical_name
UNION ALL
SELECT 'academy_subscription_payments' AS logical_name, 'p_academy_subscription_payments' AS physical_name
UNION ALL
SELECT 'academy_subscription_periods' AS logical_name, 'p_academy_subscription_periods' AS physical_name
UNION ALL
SELECT 'access_system_permissions' AS logical_name, 'f_access_system_permissions' AS physical_name
UNION ALL
SELECT 'access_system_roles' AS logical_name, 'f_access_system_roles' AS physical_name
UNION ALL
SELECT 'access_system_role_permissions' AS logical_name, 'f_access_system_role_permissions' AS physical_name
UNION ALL
SELECT 'access_system_setting_permissions' AS logical_name, 'f_access_system_setting_permissions' AS physical_name
UNION ALL
SELECT 'article_comment_receipts' AS logical_name, 'f_article_comment_receipts' AS physical_name
UNION ALL
SELECT 'auth_rate_limits' AS logical_name, 'f_auth_rate_limits' AS physical_name
UNION ALL
SELECT 'auth_remember_tokens' AS logical_name, 'f_auth_remember_tokens' AS physical_name
UNION ALL
SELECT 'categories' AS logical_name, 'f_categories' AS physical_name
UNION ALL
SELECT 'classroom_types' AS logical_name, 'p_classroom_types' AS physical_name
UNION ALL
SELECT 'comments' AS logical_name, 'f_comments' AS physical_name
UNION ALL
SELECT 'conversations' AS logical_name, 'f_conversations' AS physical_name
UNION ALL
SELECT 'conversation_members' AS logical_name, 'f_conversation_members' AS physical_name
UNION ALL
SELECT 'conversation_messages' AS logical_name, 'f_conversation_messages' AS physical_name
UNION ALL
SELECT 'conversation_message_reactions' AS logical_name, 'f_conversation_message_reactions' AS physical_name
UNION ALL
SELECT 'creator_courses' AS logical_name, 'p_creator_courses' AS physical_name
UNION ALL
SELECT 'creator_course_details' AS logical_name, 'p_creator_course_details' AS physical_name
UNION ALL
SELECT 'creator_course_lessons' AS logical_name, 'p_creator_course_lessons' AS physical_name
UNION ALL
SELECT 'creator_course_media' AS logical_name, 'p_creator_course_media' AS physical_name
UNION ALL
SELECT 'creator_course_orders' AS logical_name, 'p_creator_course_orders' AS physical_name
UNION ALL
SELECT 'creator_course_questions' AS logical_name, 'p_creator_course_questions' AS physical_name
UNION ALL
SELECT 'creator_course_reactions' AS logical_name, 'p_creator_course_reactions' AS physical_name
UNION ALL
SELECT 'creator_course_reports' AS logical_name, 'p_creator_course_reports' AS physical_name
UNION ALL
SELECT 'creator_course_reviews' AS logical_name, 'p_creator_course_reviews' AS physical_name
UNION ALL
SELECT 'creator_course_schedules' AS logical_name, 'p_creator_course_schedules' AS physical_name
UNION ALL
SELECT 'creator_lesson_access' AS logical_name, 'p_creator_lesson_access' AS physical_name
UNION ALL
SELECT 'financial_system_accounts' AS logical_name, 'f_financial_system_accounts' AS physical_name
UNION ALL
SELECT 'financial_system_currency' AS logical_name, 'f_financial_system_currency' AS physical_name
UNION ALL
SELECT 'financial_system_discounts' AS logical_name, 'f_financial_system_discounts' AS physical_name
UNION ALL
SELECT 'financial_system_ledger_entries' AS logical_name, 'f_financial_system_ledger_entries' AS physical_name
UNION ALL
SELECT 'financial_system_payments' AS logical_name, 'f_financial_system_payments' AS physical_name
UNION ALL
SELECT 'financial_system_refunds' AS logical_name, 'f_financial_system_refunds' AS physical_name
UNION ALL
SELECT 'financial_system_transactions' AS logical_name, 'f_financial_system_transactions' AS physical_name
UNION ALL
SELECT 'f_settings' AS logical_name, 'f_settings' AS physical_name
UNION ALL
SELECT 'f_timezone' AS logical_name, 'f_timezone' AS physical_name
UNION ALL
SELECT 'f_translations' AS logical_name, 'f_translations' AS physical_name
UNION ALL
SELECT 'instruments' AS logical_name, 'p_instruments' AS physical_name
UNION ALL
SELECT 'lessons' AS logical_name, 'p_lessons' AS physical_name
UNION ALL
SELECT 'levels' AS logical_name, 'p_levels' AS physical_name
UNION ALL
SELECT 'media_files' AS logical_name, 'f_media_files' AS physical_name
UNION ALL
SELECT 'music_sheets' AS logical_name, 'p_music_sheets' AS physical_name
UNION ALL
SELECT 'music_sheet_bookmarks' AS logical_name, 'p_music_sheet_bookmarks' AS physical_name
UNION ALL
SELECT 'posts' AS logical_name, 'f_posts' AS physical_name
UNION ALL
SELECT 'public_ratings' AS logical_name, 'f_public_ratings' AS physical_name
UNION ALL
SELECT 'settings' AS logical_name, 'p_settings' AS physical_name
UNION ALL
SELECT 'social_account_settings' AS logical_name, 'f_social_account_settings' AS physical_name
UNION ALL
SELECT 'social_bookmarks' AS logical_name, 'f_social_bookmarks' AS physical_name
UNION ALL
SELECT 'social_comments' AS logical_name, 'f_social_comments' AS physical_name
UNION ALL
SELECT 'social_comment_likes' AS logical_name, 'f_social_comment_likes' AS physical_name
UNION ALL
SELECT 'social_follows' AS logical_name, 'f_social_follows' AS physical_name
UNION ALL
SELECT 'social_highlights' AS logical_name, 'f_social_highlights' AS physical_name
UNION ALL
SELECT 'social_highlight_stories' AS logical_name, 'f_social_highlight_stories' AS physical_name
UNION ALL
SELECT 'social_lesson_progress' AS logical_name, 'p_social_lesson_progress' AS physical_name
UNION ALL
SELECT 'social_media' AS logical_name, 'f_social_media' AS physical_name
UNION ALL
SELECT 'social_notifications' AS logical_name, 'f_social_notifications' AS physical_name
UNION ALL
SELECT 'social_posts' AS logical_name, 'f_social_posts' AS physical_name
UNION ALL
SELECT 'social_profiles' AS logical_name, 'f_social_profiles' AS physical_name
UNION ALL
SELECT 'social_reactions' AS logical_name, 'f_social_reactions' AS physical_name
UNION ALL
SELECT 'social_story_mentions' AS logical_name, 'f_social_story_mentions' AS physical_name
UNION ALL
SELECT 'tracking_ingestion_batches' AS logical_name, 'f_tracking_ingestion_batches' AS physical_name
UNION ALL
SELECT 'tracking_user_activity_intervals' AS logical_name, 'f_tracking_user_activity_intervals' AS physical_name
UNION ALL
SELECT 'tracking_user_consents' AS logical_name, 'f_tracking_user_consents' AS physical_name
UNION ALL
SELECT 'tracking_user_content_engagements' AS logical_name, 'f_tracking_user_content_engagements' AS physical_name
UNION ALL
SELECT 'tracking_user_events' AS logical_name, 'f_tracking_user_events' AS physical_name
UNION ALL
SELECT 'tracking_user_page_views' AS logical_name, 'f_tracking_user_page_views' AS physical_name
UNION ALL
SELECT 'tracking_user_sessions' AS logical_name, 'f_tracking_user_sessions' AS physical_name
UNION ALL
SELECT 'translations' AS logical_name, 'p_translations' AS physical_name
UNION ALL
SELECT 'users' AS logical_name, 'f_users' AS physical_name
UNION ALL
SELECT 'user_addresses' AS logical_name, 'f_user_addresses' AS physical_name
UNION ALL
SELECT 'user_availabilities' AS logical_name, 'f_user_availabilities' AS physical_name
UNION ALL
SELECT 'user_awards' AS logical_name, 'f_user_awards' AS physical_name
UNION ALL
SELECT 'user_badges' AS logical_name, 'f_user_badges' AS physical_name
UNION ALL
SELECT 'user_certificates' AS logical_name, 'f_user_certificates' AS physical_name
UNION ALL
SELECT 'user_contacts' AS logical_name, 'f_user_contacts' AS physical_name
UNION ALL
SELECT 'user_educations' AS logical_name, 'f_user_educations' AS physical_name
UNION ALL
SELECT 'user_events' AS logical_name, 'f_user_events' AS physical_name
UNION ALL
SELECT 'user_experiences' AS logical_name, 'f_user_experiences' AS physical_name
UNION ALL
SELECT 'user_instruments' AS logical_name, 'p_user_instruments' AS physical_name
UNION ALL
SELECT 'user_lessons' AS logical_name, 'p_user_lessons' AS physical_name
UNION ALL
SELECT 'user_merges' AS logical_name, 'f_user_merges' AS physical_name
UNION ALL
SELECT 'user_messages' AS logical_name, 'f_user_messages' AS physical_name
UNION ALL
SELECT 'user_permissions' AS logical_name, 'f_user_permissions' AS physical_name
UNION ALL
SELECT 'user_points' AS logical_name, 'f_user_points' AS physical_name
UNION ALL
SELECT 'user_point_rules' AS logical_name, 'f_user_point_rules' AS physical_name
UNION ALL
SELECT 'user_polls' AS logical_name, 'f_user_polls' AS physical_name
UNION ALL
SELECT 'user_poll_options' AS logical_name, 'f_user_poll_options' AS physical_name
UNION ALL
SELECT 'user_poll_votes' AS logical_name, 'f_user_poll_votes' AS physical_name
UNION ALL
SELECT 'user_publications' AS logical_name, 'f_user_publications' AS physical_name
UNION ALL
SELECT 'user_referrals' AS logical_name, 'f_user_referrals' AS physical_name
UNION ALL
SELECT 'user_roles' AS logical_name, 'f_user_roles' AS physical_name
UNION ALL
SELECT 'user_sessions' AS logical_name, 'f_user_sessions' AS physical_name
UNION ALL
SELECT 'verification_levels' AS logical_name, 'f_verification_levels' AS physical_name
UNION ALL
SELECT 'world_iran_counties' AS logical_name, 'f_world_iran_counties' AS physical_name
UNION ALL
SELECT 'world_iran_provinces' AS logical_name, 'f_world_iran_provinces' AS physical_name
UNION ALL
SELECT 'z_settings' AS logical_name, 'f_legacy_settings' AS physical_name
UNION ALL
SELECT 'z_user_profiles' AS logical_name, 'f_user_profiles' AS physical_name
UNION ALL
SELECT 'z_user_settings' AS logical_name, 'f_user_settings' AS physical_name
UNION ALL
SELECT 'academy_term_invoice_payments' AS logical_name, 'p_academy_term_invoice_payments' AS physical_name
) m
LEFT JOIN information_schema.TABLES src ON src.TABLE_SCHEMA=DATABASE() AND src.TABLE_NAME=m.logical_name
LEFT JOIN information_schema.TABLES dst ON dst.TABLE_SCHEMA=DATABASE() AND dst.TABLE_NAME=m.physical_name;

-- All counts below must be zero; visibility depends on the DB account grants.
SELECT 'views' AS object_type,COUNT(*) AS object_count FROM information_schema.VIEWS WHERE TABLE_SCHEMA=DATABASE()
UNION ALL SELECT 'triggers',COUNT(*) FROM information_schema.TRIGGERS WHERE TRIGGER_SCHEMA=DATABASE()
UNION ALL SELECT 'routines',COUNT(*) FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA=DATABASE()
UNION ALL SELECT 'events',COUNT(*) FROM information_schema.EVENTS WHERE EVENT_SCHEMA=DATABASE();

-- No cross-schema FK rows should be returned.
SELECT TABLE_SCHEMA,TABLE_NAME,REFERENCED_TABLE_SCHEMA,REFERENCED_TABLE_NAME
FROM information_schema.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_NAME IS NOT NULL
AND (TABLE_SCHEMA=DATABASE() OR REFERENCED_TABLE_SCHEMA=DATABASE())
AND (TABLE_SCHEMA<>DATABASE() OR REFERENCED_TABLE_SCHEMA<>DATABASE());
