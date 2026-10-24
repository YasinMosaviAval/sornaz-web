// Explicit allowlist: every suite uses fixtures, not the application database.
const {spawnSync} = require('node:child_process');
const path = require('node:path');
const root = path.resolve(__dirname, '..');
const php = [
 'term_session_delivery_test.php',
 'post_table_status_test.php',
 'post_editor_options_test.php',
 'asset_version_test.php',
 'table-prefix-test.php', 'mobile_finance_bootstrap_test.php', 'enrollment_dashboard_scope_test.php',
 'page_source_trace_test.php',
 'sidebar_identity_render_test.php', 'panel_report_snapshot_test.php', 'practice_results_test.php', 'dashboard_layout_test.php',
 'scoped_backup_test.php', 'launch_hardening_test.php', 'content_safety_test.php', 'financial_records_test.php', 'personal_panel_data_test.php', 'personal_panel_api_test.php', 'mobile_contact_change_test.php', 'tenant_access_test.php', 'reliability_test.php', 'core_functionality_test.php', 'chat_media_revision_test.php',
 'account_security_test.php', 'account_session_test.php', 'shared_password_login_test.php', 'account_profile_test.php', 'contact_change_test.php',
 'public_directory_privacy_test.php', 'public_copy_test.php', 'registration_otp_policy_test.php',
 'academy_registration_flow_test.php', 'mobile_panel_test.php', 'mobile_panel_parity_test.php', 'mobile_panel_dialog_test.php', 'mobile_panel_presentation_test.php', 'mobile_point_history_test.php', 'panel_sidebar_navigation_test.php', 'story_feed_test.php',
 'story_highlights_test.php', 'social_comment_threads_test.php', 'notation_validation_test.php',
 'notation_instruments_test.php', 'personal_offerings_access_test.php', 'deployment_case_test.php', 'release_security_test.php'
];
const suites = php.map(file => ['php',file]);
suites.push(['php','national_holiday_branch_test.php']);
suites.push([process.execPath,'panel_requested_ui_test.cjs']);
suites.push([process.execPath,'dashboard_cards_revision_test.cjs']);
suites.push([process.execPath,'existing_member_user_test.cjs'],[process.execPath,'student_term_schedule_test.cjs']);
suites.push([process.execPath,'organization_teaching_revision_test.cjs']);
suites.push([process.execPath,'term_capacity_test.cjs']);
suites.push([process.execPath,'term_lifecycle_test.cjs']);
suites.push([process.execPath,'student_multiple_terms_test.cjs']);
suites.push([process.execPath,'staff_registration_test.cjs'],[process.execPath,'waiting_list_chat_test.cjs']);
suites.push([process.execPath,'term_batch_policy_test.cjs']);
suites.push([process.execPath,'term_batch_waiting_test.cjs']);
suites.push([process.execPath,'term_student_assignment_test.cjs']);
suites.push([process.execPath,'panel_report_source_test.cjs'],[process.execPath,'term_people_policy_test.cjs']);
suites.push([process.execPath,'availability_target_policy_test.cjs'],[process.execPath,'csrf_fetch_test.cjs'],[process.execPath,'profile_render_security_test.cjs'],[process.execPath,'lesson_usage_test.cjs'],[process.execPath,'staff_role_policy_test.cjs'],[process.execPath,'panel_activity_text_test.cjs'],[process.execPath,'term_discount_policy_test.cjs']);
if (process.argv.includes('--browser')) {
 suites.push([process.execPath,'personal_points_chart_browser_test.cjs']);
 suites.push([process.execPath,'contract_type_charts_browser_test.cjs']);
 suites.push([process.execPath,'schedule_heatmap_browser_test.cjs']);
 suites.push([process.execPath,'student_lesson_chart_browser_test.cjs']);
 suites.push([process.execPath,'term_lesson_chart_browser_test.cjs']);
 suites.push([process.execPath,'guide_library_browser_test.cjs']);
 suites.push([process.execPath,'panel_introductions_browser_test.cjs']);
 suites.push([process.execPath,'panel_workspace_tabs_browser_test.cjs']);
 suites.push([process.execPath,'posts_workspace_pagination_browser_test.cjs']);
 suites.push([process.execPath,'post_editor_browser_test.cjs']);
 suites.push([process.execPath,'post_media_upload_test.cjs']);
 suites.push([process.execPath,'posts_panel_performance_browser_test.cjs']);
 suites.push([process.execPath,'panel_content_tables_browser_test.cjs']);
 suites.push([process.execPath,'branch_dialog_scope_browser_test.cjs']);
 suites.push([process.execPath,'term_schedule_sort_browser_test.cjs']);
 suites.push([process.execPath,'term_lifecycle_browser_test.cjs']);
 suites.push([process.execPath,'panel_auto_sort_browser_test.cjs']);
 suites.push([process.execPath,'enrollment_dialog_browser_test.cjs']);
 suites.push([process.execPath,'member_user_picker_browser_test.cjs']);
 suites.push([process.execPath,'organization_catalog_browser_test.cjs']);
 suites.push([process.execPath,'term_attendance_persistence_browser_test.cjs']);
 suites.push([process.execPath,'student_open_terms_browser_test.cjs']);
 suites.push([process.execPath,'student_term_rows_browser_test.cjs']);
 suites.push([process.execPath,'staff_registration_browser_test.cjs']);
 suites.push([process.execPath,'term_batch_browser_test.cjs']);
 suites.push([process.execPath,'term_people_browser_test.cjs'],[process.execPath,'scheduling_tabs_browser_test.cjs'],[process.execPath,'panel_guides_browser_test.cjs'],[process.execPath,'staff_contract_forms_browser_test.cjs'],[process.execPath,'points_stability_browser_test.cjs'],[process.execPath,'personal_points_pagination_browser_test.cjs'],[process.execPath,'access_users_realtime_browser_test.cjs'],[process.execPath,'term_discounts_browser_test.cjs']);
 suites.push([process.execPath,'core_functionality_browser_test.cjs'],[process.execPath,'public_copy_test.cjs'],[process.execPath,'financial_records_browser_test.cjs'],[process.execPath,'panel_table_browser_test.cjs'],[process.execPath,'page_source_trace_browser_test.cjs'],[process.execPath,'lesson_usage_browser_test.cjs']);
}
let failed=0;
for (const [command,file] of suites) {
 console.log(`\nRunning ${file}`);
 const args=command==='php' && file==='mobile_panel_test.php' ? ['-d','short_open_tag=1',path.join('scripts',file)] : [path.join('scripts',file)];
 const result=spawnSync(command,args,{cwd:root,stdio:'inherit',timeout:120000});
 if(result.error || result.status!==0){failed++;console.error(`FAILED: ${file}${result.error ? ` (${result.error.message})` : ''}`);}
}
console.log(`\n${suites.length-failed}/${suites.length} suites passed.`);
process.exitCode=failed?1:0;
