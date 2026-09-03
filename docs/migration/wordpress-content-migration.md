# WordPress content migration

Source dump: `docs/database/sornaz database/sornazco_maindb.sql`

The migration keeps the prepared content and local article images in the new application, while synchronizing authoritative WordPress metadata:

- original local publication, creation, modification, and comment dates;
- decoded WordPress slugs and canonical legacy URL mapping;
- all six WordPress article categories and their post relationships;
- post status, author (when email matches), view count, comment count, and GUID;
- all approved comments, reply relationships, guest details, and moderation state;
- trashed WordPress comments as `trash`, without exposing them publicly;
- persistent WordPress-to-application IDs in `legacy_import_map` for idempotent reruns.

## Result on 2026-09-03

- 455 existing content records matched: 267 posts, 70 products, and 118 music-theory records.
- 122 published articles have 122 unique non-empty slugs.
- All 122 published articles have their WordPress categories; 139 category relationships were restored.
- 43 approved WordPress comments and 32 trash comments were synchronized.
- Two newly approved comments and one older missed reply were added.
- Existing application-only pending/test comments were preserved.
- WordPress draft ID 5379 (`نوشته تستی اول`) was intentionally not added because it is a test draft with no counterpart in the prepared application.

## Re-run

1. Load the dump into the isolated local database:

   `powershell -ExecutionPolicy Bypass -File docs/database/load-wordpress-dump.ps1`

2. Preview without writes:

   `php docs/database/import-wordpress-content`

3. Apply after taking a database backup:

   `php docs/database/import-wordpress-content --apply`

4. Verify URL and comment integrity:

   `php docs/database/verify-wordpress-content`
