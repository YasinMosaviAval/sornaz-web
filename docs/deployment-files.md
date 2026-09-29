# Server and GitHub file guide

[نسخهٔ فارسی](deployment-files.fa.md)

This guide covers the current `sornaz` website repository. “Do not upload” does not mean “delete from the server.” Preserve production configuration and user data during deployment. Uploading to GitHub here means tracking files in Git and pushing commits.

## 1. File decision table

| File or path | Production server | GitHub |
| --- | --- | --- |
| `index.php` | Required web entry point | Track |
| `bootstrap/`, `core/`, `config/`, `routes/` | Required; deploy together from the same release | Track; configuration must not contain real credentials |
| `Modules/`, including module `Resources` and `Lib` directories | Required, including templates and the bundled PHPMailer library | Track |
| `resources/` and `views/` | Required; both participate in template loading | Track |
| `assets/`, including CSS, JS, fonts, static images and `assets/vendor/` | Required; see media and fixture exceptions below | Track application assets |
| Root `.htaccess` and directory protection files | Required for Apache or compatible hosting; Nginx needs separate rules | Track; do not omit hidden files from the release |
| `composer.json` and `composer.lock` | Include for dependency installation and autoload generation | Track both |
| `vendor/` | Required; generate on the server or ship a compatible release copy | Prefer generated files outside Git; see current repository caveat below |
| `.env` | Production-specific configuration is required; preserve the server copy | Never track |
| `.env.example` | Not needed at runtime | Track placeholders without secrets |
| `sornaz` | Only needed when using the CLI entry point | Track; this is a PHP script, not a database dump |
| `google494712e9ea54cca1.html`, `google7cd438ee4c3edccf.html` | Preserve if used for ownership verification of this site | Track if they belong to the project |
| `storage/` | Directory structure and write access are needed; do not upload local contents wholesale | Only reviewed structural/protection files such as `.gitkeep` and `.htaccess` |
| `docs/` | Not required for web requests; use relevant instructions and SQL separately | Track documentation and migrations without private data |
| `scripts/` | Not required for ordinary web requests; deploy only reviewed operational scripts when needed, preferably outside the web root | Track tool and test source and configuration |
| `.php-cs-fixer.dist.php`, `.prettierrc.json` | Not required at runtime | Track |
| `scripts/format-tools/package.json`, `package-lock.json` | Not required at runtime | Track both |
| `scripts/php-complexity-baseline.json` | Not required at runtime | Track |
| `.gitignore`, `.gitattributes` | Not needed by the website; may exist in a server checkout | Track |
| `.git/` | Exclude from manual uploads; block HTTP access if deploying through a Git checkout | Do not upload its internal files manually; Git transfers history itself |
| `.vscode/`, `.revision/` | Not required | Exclude personal settings and temporary files |

**Important distinction:** `assets/vendor/` contains browser dependencies and must be deployed. Root `vendor/` contains PHP dependencies and autoload files and is also required at runtime. `scripts/format-tools/node_modules/` contains development tools and is not needed by the website.

## 2. Runtime files and private data

Do not upload these local files to production or track them on GitHub:

- Local `.env`, passwords, private keys and tokens.
- `php-error.log`, `storage/logs/`, and test browser profiles or caches inside it.
- `storage/sessions/sess_*`; sessions belong to their own environment.
- `storage/backups/`, full database exports, SQL containing user data, `docs/database/backups/`, and `docs/database/sornaz database/`.
- User files in `storage/account-media/`, `storage/chat/`, `storage/course-media/`, and `storage/social-media/`.
- Uploads in `assets/media/library/` and `assets/media/chat-groups/`.
- `storage/mobile-social-stage/`, `storage/notation-browser-check/`, `storage/social-web-check/`, and `storage/public-apps-check/`.
- `storage/windows-build-tools/`, `storage/windows-release/`, and `storage/windows-stage/`.
- `storage/code-quality/`, `scripts/format-tools/node_modules/`, and temporary verification files such as `storage/apply-audio-fixes.ps1`, `storage/audio-fix-baseline.json`, and `storage/theme-fix-baseline.txt`.

These locations may contain essential production data. Preserve the server contents during updates. When moving servers, migrate actual media and database contents separately through a controlled backup and transfer process. Protection files such as `storage/course-media/.htaccess` and `storage/social-media/.htaccess` are exceptions: preserve and deploy them.

`assets/media/users/` is used by the project's test-data service. Do not automatically include its contents as production media. Check database references and media licenses before removing or transferring files. `ATTRIBUTION.json` and `VIDEO-SOURCES.json` describe media sources; retain source and license information for media you keep.

## 3. Current Git state: cleanup still needed

At the time of writing, the tracked-file list included:

- 1,217 files under `storage/logs/`.
- 31 files under `storage/account-media/` and one SQL file under `storage/backups/`.
- 13 files under `vendor/`.

Adding a path to `.gitignore` does not untrack existing files. Review these before the next push and remove private or generated files from the index while preserving necessary disk and server copies. Removing a file from the latest commit does not erase earlier history. If secrets have already been pushed, review history and rotate exposed credentials where applicable. This local inspection does not establish what has already reached GitHub.

For `vendor/`, establish reproducible Composer installation before changing tracking policy. Omitting it abruptly from deployment breaks autoloading. This guide has not cleaned files or modified the index.

## 4. Suggested deployment procedure

1. Back up the production database, configuration and user files to private storage.
2. Test the release candidate; see [Code maintenance](code-maintenance.md).
3. Package the required source and static assets from the table. Exclude local configuration, logs, sessions, backups and development tools. Do not ZIP the entire local directory without filtering it.
4. Install from the lockfile and regenerate autoload files on the target or a compatible build environment:

   ```sh
   composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-scripts --no-plugins
   composer dump-autoload --optimize --no-dev --no-scripts --no-plugins
   ```

   If the host has no Composer, ship `vendor/` built for that same release. Linux paths are case-sensitive. Include matching `bootstrap/autoload.php`, `bootstrap/class-paths.php`, and application source. Do not run `composer update` on production.
5. Preserve production `.env` and user data. Create required storage directories with appropriate PHP ownership and write permissions; avoid world-writable `777` permissions. Deployment must not blindly delete additional server files.
6. Apply only required, reviewed migrations. Uploading SQL does not execute it. Do not import the entire local database into production. A SQL file being in `docs/database/` does not establish that it is safe or required to run.
7. Enforce private-file access rules in the web server. **Uploading `.htaccess` is insufficient for Nginx.** `docs/nginx-security.conf` and `docs/nginx-private-files-emergency.conf` are configuration integration references; placing them in `public_html` does not activate them. Follow [Private-file hosting instructions](private-files-hosting.md).
8. Check the home page, login, conversations, authorized media access and denial of direct private-file downloads. Refresh OPcache through the hosting panel if needed.

The current project has `index.php` at its root. Do not deploy merely by switching the document root to an assumed `public/` directory. PHP source can be required on disk while direct HTTP access to it is blocked. Template locations such as `views/` must not be used to store secrets.

This document is guidance only; it changes no server files, Git settings, history or data.
