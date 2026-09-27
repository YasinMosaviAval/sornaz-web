# Release security deployment

These changes must be deployed to the running server before they protect it.
No production database migration or relocation of existing uploads is required.

## Before updating the live checkout

1. Back up the live `.env` **outside the document root**, with access restricted
   to the deployment account. This release removes `.env` from Git tracking.
   A pull/deployment may remove the formerly tracked copy. Restore the live
   configuration before starting the application; never replace it with the
   example file or a developer's configuration.
2. Preserve needed database exports and the old root `php-error.log` outside the
   document root. Four full database exports are removed from tracking, while
   SQL migrations remain. Local working copies were preserved during development.
3. Deploy the application, root `.htaccess`, new media services/controllers and
   Composer autoload files together. A partial deployment can break media URLs.
4. On the target system run `composer dump-autoload --no-scripts --no-plugins`.
   The explicit classmap supports existing non-PSR-4 paths and Linux case-sensitive
   filenames. Do not remove it when regenerating vendor files. Reload PHP workers
   or clear OPcache through the hosting control panel after deployment.
   Include `bootstrap/autoload.php` and `bootstrap/class-paths.php` when uploading
   the updated `index.php` (and CLI entry point `sornaz`). They provide a bounded
   fallback for existing FTP deployments whose folder/filename casing differs
   from the generated Composer map. Missing files are not repaired by this fallback.
5. Ensure PHP can write `storage/logs`, sessions and the existing upload folders.
   New PHP error logs are written under `storage/logs`.

## Apache / compatible hosting

Apache 2.4, `mod_rewrite` and permission to use the root `.htaccess` directives
(`Options`, `Require`, rewrites and `DirectoryIndex`) are required. Verify the
rules on the actual host; a proxy/CDN serving files directly must enforce the
same restrictions. Do not serve the project with PHP's development web server.

The rules deny direct access to environment files, Git metadata, source/config
directories, database exports, logs and all storage paths except the controlled
account-media route. Only root `index.php` may execute through HTTP. This also
prevents execution of previously uploaded PHP files. Directory listing is off.

## Nginx

Nginx ignores `.htaccess`. Apply `docs/nginx-security.conf` inside the site's
`server` block before other regex locations. Merge with the existing HTTPS and
PHP-FPM settings; do not blindly append conflicting `location` blocks.

Keep one exact entry point (substitute the real PHP-FPM socket):

```nginx
location = /index.php {
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root/index.php;
    fastcgi_pass unix:/run/php/REPLACE-WITH-ACTUAL-FPM.sock;
}
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

Remove generic PHP execution locations and `^~ /assets/` or `^~ /storage/`
locations that bypass the security regex locations. Run `nginx -t` before reload.
This Nginx configuration is provided for deployment; the local HTTP tests used
Apache, not Nginx.

## Media behavior

- Existing chat attachment URLs still enforce conversation membership. Direct
  `storage/chat` links are denied, including files uploaded before this release.
- New chat attachments use a MIME allowlist, actual file size and a server-chosen
  extension. Executable/active-content filenames are rejected. HTML and SVG
  attachments are intentionally unsupported. This is not an antivirus scanner.
- Existing account avatar/gallery/video URLs are routed through a database check.
  Only public, undeleted media in approved collections is served. Records linked
  to academy documents are never served by that public endpoint.
- Academy documents retain the authenticated account download endpoint. Upload
  responses now return that endpoint rather than the storage path.
- Media-library URLs now check visibility. Nonpublic library files are available
  only to their owner or a site administrator; `academy_only` does not grant
  access to every logged-in user. Broader academy sharing requires an explicit
  membership policy. Legacy media outside these managed folders needs separate
  review if it is intended to be private.
- Inline audio/video supports byte ranges. Downloads use `private, no-store`,
  `nosniff` and a sandbox policy. Public media now requires PHP/database access.
- Purge cached private storage/library URLs from any CDN or reverse proxy. New
  authorization rules cannot remove copies that visitors previously downloaded.

## Verify on the deployed host

Use status-only requests; do not download secrets into terminal logs:

```sh
curl -sS -o /dev/null -w '%{http_code}\n' https://YOUR-HOST/.env
curl -sS -o /dev/null -w '%{http_code}\n' https://YOUR-HOST/.git/config
curl -sS -o /dev/null -w '%{http_code}\n' https://YOUR-HOST/docs/database/localhost.sql
curl -sS -o /dev/null -w '%{http_code}\n' https://YOUR-HOST/php-error.log
curl -sS -o /dev/null -w '%{http_code}\n' https://YOUR-HOST/storage/sessions/probe
```

All should return 403 (or an intentional edge-layer 404), never 200. Also verify:

1. Home, login, community and courses still render; styles and scripts load.
2. Existing avatars, gallery images and videos load; video seeking works.
3. A logged-out visitor cannot retrieve a document or chat attachment. A member
   can download the attachment; a nonmember cannot. Direct storage URLs stay denied.
4. Changing a library item to private prevents anonymous access immediately.
5. Valid image, PDF and voice uploads work; an executable-named upload is rejected.
6. TLS renewal's `/.well-known/acme-challenge/` path remains accessible.

## Credentials and incident follow-up

Removing files from Git does not erase history or undo past exposure. Since the
site was already online, review access logs and rotate live credentials that may
have been exposed: database, SMTP, SMS, payment credentials and application signing
keys as appropriate. Coordinate signing-key rotation because it can invalidate
remember-me/mobile tokens. Do not publish old database exports or logs. History
rewriting and live credential rotation were not performed by this change.

## Local regression checks

```sh
php scripts/release_security_test.php
php scripts/chat_media_revision_test.php
php scripts/account_profile_test.php
php scripts/deployment_case_test.php
node scripts/release_apache_test.cjs C:/xampp/apache/bin/httpd.exe
```

The security PHP test uses SQLite in memory and temporary fixture files; it never
boots the application or opens its database. It checks exact autoload path casing,
core class loading, upload rejection, media access and byte ranges. The Apache
test starts an isolated localhost instance using synthetic files and the real
root `.htaccess`, then removes its fixtures. It does not test the production host.
