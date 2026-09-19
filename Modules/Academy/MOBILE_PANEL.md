# Native Flutter user panel and academy search

The third main navigation destination is a native Flutter user panel. It uses Material widgets for lists, filters, forms, account settings, conversations, statistics and exports. It does not embed the website or exchange bearer tokens for browser cookies. The social profile remains the fifth destination.

The home page replaces New courses with academy search by name, instrument and city. Academy results and details also use Flutter widgets. Updated courses remain available.

## Native academy registration

Home also offers a native academy registration form, available to guests and signed-in users. It shares the website's validation, email/mobile OTP verification and registration services. No WebView is used. The three public hero sentences are the same for guests, members and administrators; position-based CMS overrides cannot replace those marked text nodes.

After registering an academy, both web and app offer **Register main branch** or **Continue without a branch**. Skipping finishes registration without inserting a branch or branch account. Academy-level membership is established at academy creation, so a branch can be added later from the panel. For guest requests, the academy account is the owner used for any subsequent main branch.

Registration endpoints under `/api/sornaz/v1/academy-registration`:

- GET root initializes or resumes a session-bound flow and returns terms and its current stage.
- POST `/send-code` validates the selected `academy` or `branch` step and sends its OTP.
- POST `/submit` verifies the code and registers the selected step.
- POST `/without-branch` completes the academy-only path.

POSTs require the session cookie and `X-Academy-Flow` proof issued by GET. Optional bearer authentication determines the requester; a client cannot choose the academy or manager IDs. OTP state is separate from ordinary account registration, bound to the flow, step and form data, with the existing retry and attempt limits. Completed submissions are idempotent within the flow. The Flutter client retains only the session cookie in secure storage, never form passwords; incomplete branch setup can be resumed during the flow's 24-hour lifetime. Website branch skipping is a CSRF-protected POST.

Checks: `php scripts/academy_registration_flow_test.php`, `node scripts/home_hero_test.cjs`, and Flutter `academy_registration_test.dart` / `site_panel_academy_test.dart`. Tests use fake OTP delivery and do not create live accounts or send messages.

## Server endpoints and rollout

Deploy the PHP changes alongside the app source before testing against the live site:

- `Modules/Analytics/Routes/api.php`
- `Modules/Analytics/Controllers/Api/MobilePanelController.php`
- `Modules/Analytics/Controllers/Api/MobileLearningController.php`
- `Modules/Analytics/Services/MobilePanelAccess.php`
- `Modules/Analytics/Services/MobilePanelCatalog.php`
- `Modules/Analytics/Controllers/Web/AdminGuideController.php`
- `Modules/Academy/Controllers/Api/PublicAcademyController.php`
- `Modules/Academy/Routes/api.php`
- `Modules/Academy/Services/AcademyBranchService.php`

This work does not deploy the server or build an APK/EXE. The former `/auth/web-session` endpoint and the panel WebView dependency were removed. Other unrelated application features retain their existing rendering mechanisms.

Public endpoints:

- `GET /api/sornaz/v1/academies/options`
- `GET /api/sornaz/v1/academies?q=...&instrument=...&city=...&page=1`
- `GET /api/sornaz/v1/academies/{id}`

Authenticated native endpoints:

- `GET /api/sornaz/v1/panel`: role-filtered section and form definitions.
- `GET /api/sornaz/v1/panel/{section}/{operation}`: lists, options, detail and authorized downloads.
- `POST /api/sornaz/v1/panel/{section}/{operation}`: explicitly registered operations only.

POST requests use multipart form data. `payload_b64` contains base64-encoded UTF-8 JSON, including nested form values. File fields are actual multipart uploads. Route identifiers are passed separately as query parameters; list pagination and filters retain the underlying service contract. Responses use the normal framework `{status, data}` envelope.

## Native coverage

The catalog contains 49 visible sections across personal learning, account settings, chat, messages and notifications, academy/branch management, teachers and students, classrooms and their categories, courses and terms, attendance, organization/member schedules, availability exceptions, finance and subscriptions, gallery, profile content, points, access management, site content and settings.

Lists support search, pagination where the source supports it, selection, scoped actions and CSV/PDF export of displayed records. Forms support files, nested collections, dates, time, booleans, dependent selections and multiple selections. PDF generation uses the bundled Persian font and A4 pages without a browser or network font download.

Chat supports text, attachments, recording and playback of voice messages, likes, editing/deleting the sender's messages, forwarding, group details, membership management and authorized downloads. Message history loads one batch per explicit action, with a Load more messages button for subsequent batches. There is no periodic polling or automatic history draining. Sending fetches only the newly sent range; likes, edits and deletions update the displayed message after the mutation succeeds.

## Mobile request budget and navigation

The five destinations are Home, User panel, Feed, Music tools and Profile. Notation is an internal Music tools route. Profile uses the signed-in user's profile and owner-filtered posts, while Feed shows community posts and stories.

Tabs mount only when selected and retain their state after visiting. A non-adjacent tab selection skips intermediate pages so it cannot preload their APIs. Account changes still replace account-bound content.

Panel GETs share in-flight requests, serialize distinct requests and use an account-instance and language-isolated memory cache (10 minutes for the section catalog, 2 minutes for resource data, at most 64 entries). Cached JSON is copied before use. There is no background revalidation. Explicit refresh is throttled to once per key per 10 seconds; successful mutations invalidate the cache. Errors stop queued and subsequent network reads for at least 30 seconds; HTTP 429/503 respect Retry-After up to one hour. Disposed clients and changed accounts cannot return private cached data or start new reads.

Local regression coverage: `test/panel_request_budget_test.dart`, `test/native_panel_test.dart`, `test/main_tabs_notation_test.dart` and `test/site_panel_academy_test.dart`. Tests use mocked HTTP clients, including 100 concurrent identical reads, idle chat, tab revisits and failure cooldown. No production requests or APK build are needed.

The dashboard displays actual API statistics. Website demo-only reports, generated sample charts and destructive development seed tools are not exposed as user data. The native reports destination uses the real scoped dashboard data.

## Access and account isolation

Every native call requires the existing validated bearer token. Section visibility derives from the website's account types, active memberships, management roles/contracts and site-administrator rules. The server dispatches only catalogued routes and replays their original middleware. Business services retain operation-level authorization. Member editing also explicitly checks that the member belongs to the actor's accessible scope.

The bridge temporarily installs the bearer identity for existing server services, then restores the entire original session in a `finally` block. It does not create or return an authenticated browser session. Cookie-only requests are rejected. CSRF middleware is skipped only inside this bearer-only adapter. Numeric route identifiers and classroom-category codes are validated before dispatch.

The client rejects requests and late responses after account switching. It avoids forwarding bearer credentials through redirect responses and shows generic network/server errors instead of raw SQL, API paths or exception text.

## Verification

Run without producing an application build:

- `php scripts/mobile_panel_test.php`
- `dart analyze lib/screens/Site`
- `flutter test --no-pub test/native_panel_test.dart test/site_panel_academy_test.dart test/home_revision_test.dart test/profile_home_test.dart`

The PHP test uses test doubles for data access and real token validation, middleware, route dispatch and a real business controller. It does not modify the application database. Flutter tests exercise native form selections, filtering, pagination, account isolation, group-member actions, chat pagination, home navigation and Persian PDF/CSV output.

These checks do not replace device validation of microphone permissions, audio playback, file pickers, payment handoff, or role-specific operations against a deployed server with actual test accounts. No live account mutations or payment transactions were performed during development.
