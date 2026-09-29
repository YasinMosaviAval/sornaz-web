# Code maintenance

## Install development tools

Use PHP 8.2 and Node.js with npm. From the repository root:

```sh
php scripts/install-php-formatter.php
npm ci --prefix scripts/format-tools --ignore-scripts --no-audit --no-fund
```

PHP CS Fixer is pinned to 3.95.27 and verified by SHA-256 before installation. Prettier is pinned to 3.9.9 with an npm lockfile. Tools are isolated from production Composer dependencies and ignored by Git. Keep TLS certificate verification enabled when installing them.

## Routine checks

```sh
composer style:check
composer style:fix
composer quality:report
composer quality:check
composer test:release
```

`style:fix` rewrites files; the other commands check or report. Review formatting changes separately from changes to behavior whenever possible.

PHP formatting covers `core` and `Modules`, excluding `Resources` templates and `Lib` dependencies (488 files at introduction). JavaScript formatting covers `assets/**/*.js`, excluding vendor and minified scripts (114 files). PHP risky fixers are disabled. Existing control-flow statements gain explicit braces. Templates and embedded scripts need a separate review before broader formatting.

## Method size

`quality:report` lists long lines and named functions with more than 400 body tokens. Tokens exclude whitespace and comments; this measures size, not cyclomatic complexity. Anonymous functions count toward their enclosing method. Large SQL strings and data catalogs still require human review.

`quality:check` rejects new oversized methods and growth above the committed `scripts/php-complexity-baseline.json`. The baseline currently records 128 existing large methods. It acknowledges existing debt rather than declaring those methods acceptable. Run `php scripts/php-maintainability.php baseline` only after reviewing changes; do not regenerate it merely to silence a failure. These checks run when invoked and are not yet enforced by CI.

Prioritize behavior tests before extracting responsibilities from payment, enrollment, permissions and backup services. Separate validation, authorization, queries, mutations and serialization where useful. Preserve transaction boundaries, authorization order and API response types. Avoid moving a large method to another file without improving its responsibilities.

## Refactoring completed in this pass

- `ChatService::messages`: extracted page queries, refresh queries, read-cursor updates, reaction loading and message serialization. Membership checks still precede message access.
- `ContactMessageService::submit`: extracted validation, message creation and translation persistence. Writes remain inside the existing transaction.
- Updated the contact browser test to tolerate formatter whitespace while retaining its behavioral assertions.

For a strictly whitespace-only pass, `php scripts/php-maintainability.php snapshot` before and `verify` after can compare PHP tokens. Adding braces or extracting methods intentionally changes tokens; this comparison is not proof of behavior preservation and should not replace tests.

Run release tests and syntax checks before deploying. Browser tests use isolated JavaScript environments, and database tests use isolated fixtures; they do not certify production hosting or real payment-provider behavior. This maintenance pass requires no database migration or APK build. Existing security and business-logic findings remain separate work.
