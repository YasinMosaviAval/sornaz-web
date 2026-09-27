# Account security deployment

## Required order

1. Back up the database and the current application files. Preserve the server `.env`.
2. Run `docs/database/migrations/2026_09_28_account_security.sql` in the **website database** using the hosting database tool. It creates two independent tables and does not rewrite user records. Run this before uploading the PHP changes.
3. Deploy the application changes, `assets/theme/csrf.js`, layouts, `bootstrap/autoload.php`, `bootstrap/class-paths.php` and generated `vendor/composer` files together. Preserve Linux filename casing. If Composer is available, regenerate autoload files on the server with `composer dump-autoload --no-scripts --no-plugins`.
4. Confirm `APP_URL` uses the actual HTTPS website URL and `APP_KEY` is a strong private random value. Keep the existing key if already securely configured. Reload PHP/clear opcode cache through the hosting panel if stale code is served; clear CDN caches for changed JavaScript and authenticated HTML.
5. Existing browser sessions, remember cookies and mobile bearer tokens require a fresh login once. This is intentional. Mobile clients keep using the same API and opaque token format; no APK build is required.

## Smoke checks on the server

- With a test account: login, remember login, logout, then confirm replay of the old remember/bearer credential fails. Verify registration and password reset with controlled delivery recipients.
- Change/reset the password and confirm another already-open session loses access on its next request.
- Confirm POST without a CSRF token returns 419 on web routes. Valid login, chat, profile save, inline text editing and upload requests should still succeed. Bearer API routes do not require a browser CSRF token.
- Check database-backed throttling returns 429 after the configured threshold. Missing security tables cause authentication endpoints to return 503. Inspect PHP logs without publishing their contents.
- Rate limits use `REMOTE_ADDR`. If a reverse proxy hides client addresses, configure the web server to restore client IPs **only from trusted proxy addresses**. Do not trust arbitrary forwarded headers in application code.
- Public `/users` output must omit birthdays, login/registration times and private schedules. Contact fields and addresses are present only after the account explicitly enables contact sharing. Previously unset personal contact sharing now defaults to off.

## Scope and limitations

Local tests use isolated SQLite fixtures and simulated delivery; they do not establish that the live MySQL migration, proxy configuration or SMS/email delivery has succeeded. Apply the checklist after deployment.

Direct private-file protection in nginx remains pending by request. This account-security change does not resolve that separate release blocker.

Rate limits are fixed windows: per group/IP 60 attempts per 15 minutes (20 for OTP delivery), per destination 30 attempts (5 for OTP delivery), plus one delivery per minute window. Shared networks may hit the IP cap. Monitor legitimate failures before adjusting limits. Reset responses deliberately do not disclose whether an account exists; delivery failures are logged with a generic message.
