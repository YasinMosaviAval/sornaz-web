# Term student assignment wizard

The web and native panel now assign a student in four steps: identity (existing account or new student), address, session dates, and classroom. Existing session dates are retained. Choosing a classroom later remains possible when registering without session dates. The standalone student creation API stays available for the assignment flow, while its management-page button is removed.

Assignment uses the existing authenticated term endpoint. Enrollment and the optional classroom update share a database transaction and schedule mutex, so a foreign or unavailable classroom cannot leave a partially enrolled student. Date preview before classroom selection is read-only; final scheduling still requires and validates a classroom. Receptionist approval rules remain unchanged.

Deploy the web backend service and mobile catalog first, then the web JavaScript files together, invalidating their asset cache, then the native app update. No migration, configuration change, or new dependency is required. Roll back these files together; existing data needs no rollback. The Flutter main changes are independently transferable to the release worktree and do not depend on OMR or experimental analysis.

Validation: term_student_wizard_browser_test.cjs (Persian and English navigation and retained values), term_student_wizard_test.cjs (single-request assignment and tenant rollback), student_term_schedule_test.cjs (including preview without classroom), term_classroom_flow_test.cjs, mobile_panel_dialog_test.php, and Flutter panel_term_assignment_wizard_test.dart / panel_new_web_workflows_test.dart.
