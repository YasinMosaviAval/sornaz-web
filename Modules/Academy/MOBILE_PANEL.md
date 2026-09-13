# Native Flutter user panel and academy search

The third main navigation destination is a native Flutter user panel. It uses Material widgets for lists, filters, forms, account settings, conversations, statistics and exports. It does not embed the website or exchange bearer tokens for browser cookies. The social profile remains the fifth destination.

The home page replaces New courses with academy search by name, instrument and city. Academy results and details also use Flutter widgets. Updated courses remain available.

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

Chat supports text, attachments, recording and playback of voice messages, likes, editing/deleting the sender's messages, forwarding, group details, membership management and authorized downloads. Message loading drains the server's batches rather than silently stopping at 200 messages.

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