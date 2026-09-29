# Login recovery

## Confirmed findings

- The supplied database screenshot shows owner `user_id=1`, username `Sornaz`, as `pending`. Account authentication requires `active` or `approved`. Both academy accounts in the screenshot are already approved; their failure needs separate verification.
- Local configuration has `APP_ENV=local` and a production HTTPS `APP_URL`. Previously this made PHP session and remember cookies Secure even on local HTTP. Cookie policy now uses the request scheme in local/development/testing environments and preserves the HTTPS URL fallback in production for TLS proxies.
- Redirect validation errors were written to ordinary session keys while login/register views read flash keys. Redirects now also populate the flash keys, with passwords removed from old input.
- An expired HTML login form now returns to `/login` with an error and asks for the password again. JSON requests retain HTTP 419. Failed CSRF requests never execute login.
- Mobile panel requests need the already included password fingerprint fix in `MobilePanelController.php`. Legacy bearer tokens from before the account-security migration require a fresh login.

## Deployment

1. Deploy `core/session/Session.php`, `core/auth/Auth.php`, `core/http/RedirectResponse.php`, `core/csrf/CsrfMiddleware.php`, and `Modules/Analytics/Controllers/Api/MobilePanelController.php` together. Preserve exact existing filename case on Linux.
2. Keep `APP_ENV=production` and the HTTPS `APP_URL` on the public server. Use `APP_ENV=local` for local HTTP development. No environment files were modified by this patch.
3. Run `docs/database/migrations/2026_09_28_restore_site_owner_login.sql` on the affected database. It approves only the confirmed, non-deleted pending owner record; it does not change passwords or other users. This SQL has not been executed against the user's database by Codex.
4. Reload the login page, then log in again. If a prior Secure cookie prevents local HTTP sessions, remove only this local site's cookies once, then reload. Log out/in in the mobile application to obtain a current bearer token.
5. Verify each account independently, then request the mobile panel list. If an approved academy account still fails, capture the failing request path, HTTP status and response message, excluding passwords/cookies/tokens. If a successful login redirects home as a guest, check whether the login response sets a session cookie and the next request sends it; report presence only, never cookie values.

## Validation

Isolated SQLite tests exercise three users sharing the exact stored password hash, username/email/phone lookup, web identity, independent bearer tokens, logout isolation and blocked/deleted-account rejection. Tests also cover local/production cookie policy, HTML CSRF recovery, one-use error messages and JSON compatibility. No live database, SMS, email or payment requests are used.
