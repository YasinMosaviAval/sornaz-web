// Explicit allowlist: every suite uses fixtures, not the application database.
const {spawnSync} = require('node:child_process');
const path = require('node:path');
const root = path.resolve(__dirname, '..');
const php = [
 'table-prefix-test.php',
 'scoped_backup_test.php', 'launch_hardening_test.php', 'content_safety_test.php', 'financial_records_test.php', 'personal_panel_data_test.php', 'personal_panel_api_test.php', 'mobile_contact_change_test.php', 'tenant_access_test.php', 'reliability_test.php', 'core_functionality_test.php', 'chat_media_revision_test.php',
 'account_security_test.php', 'account_session_test.php', 'shared_password_login_test.php', 'account_profile_test.php', 'contact_change_test.php',
 'public_directory_privacy_test.php', 'public_copy_test.php', 'registration_otp_policy_test.php',
 'academy_registration_flow_test.php', 'mobile_panel_test.php', 'story_feed_test.php',
 'story_highlights_test.php', 'social_comment_threads_test.php', 'notation_validation_test.php',
 'notation_instruments_test.php', 'personal_offerings_access_test.php', 'deployment_case_test.php', 'release_security_test.php'
];
const suites = php.map(file => ['php',file]);
suites.push([process.execPath,'csrf_fetch_test.cjs'],[process.execPath,'profile_render_security_test.cjs']);
if (process.argv.includes('--browser')) {
 suites.push([process.execPath,'core_functionality_browser_test.cjs'],[process.execPath,'public_copy_test.cjs'],[process.execPath,'financial_records_browser_test.cjs']);
}
let failed=0;
for (const [command,file] of suites) {
 console.log(`\nRunning ${file}`);
 const result=spawnSync(command,[path.join('scripts',file)],{cwd:root,stdio:'inherit',timeout:120000});
 if(result.error || result.status!==0){failed++;console.error(`FAILED: ${file}${result.error ? ` (${result.error.message})` : ''}`);}
}
console.log(`\n${suites.length-failed}/${suites.length} suites passed.`);
process.exitCode=failed?1:0;
