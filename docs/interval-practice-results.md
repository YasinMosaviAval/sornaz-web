# Interval practice results

The application awards one star for ten consecutive correct answers, displays up
to five stars per level, and unlocks the next level after the first success.
Wrong answers reset the current streak. Western and Iranian progress is separate.

## Deployment

1. Back up the database and apply
   `database/migrations/2026_10_17_practice_results.sql` using the normal database
   deployment process. This creates three new tables and changes no existing rows.
2. Deploy `Modules/Education/Services/IntervalResultPolicy.php`,
   `PracticeResultService.php`, `Controllers/Api/PracticeController.php`, and
   `Resources/interval-curriculum.v1.json` under `Modules/Education`.
3. Deploy the new entries in `Modules/Education/Routes/api.php`, then the app.
4. Verify with an authenticated test account: complete ten correct answers,
   retry an upload, reopen the app, and restore progress on another device.
   Verify MySQL/InnoDB behavior on staging before production deployment.

The migration has not been applied to the production database by this change.
There is no runtime table creation. Missing tables return HTTP 503; the app keeps
its local results and retries later. Rollback SQL removes stored results: disable
the API and back up all three tables before using it.

## Storage and authentication

`f_practice_sessions` stores attempts, computed streaks and pass state.
`f_practice_answers` stores immutable ordered answers, including quarter-tone
intervals, timing, and the server-computed correctness. A user/run identifier and
session/sequence constraints make retries idempotent. Conflicting retries and
sequence gaps fail without partially applying the batch.

`f_practice_legacy_progress` stores one baseline star per old completed level,
explicitly marked `app_local_completion_v2`. Old completion marks have no answer
history and never create synthetic passed attempts.

All endpoints require the existing mobile bearer token. Cookie authentication and
client-supplied user identifiers are not accepted. Each user can read only their
own results, including when the caller is an administrator.

## API

- `POST /api/sornaz/v1/practice/interval-results`: form field `payload` contains
  JSON with `runId`, `music`, `version: 1`, `level`, `mode`, `closed`,
  and up to 100 `answers`. Each answer contains `sequence`,
  `expectedQuarterSteps`, `selectedQuarterSteps`, `rootMidi`, `direction`,
  `answeredAt`, and `elapsedMs`. See the policy and fixture for exact validation.
- `GET /api/sornaz/v1/practice/interval-results?music=western`: returns awarded
  run IDs (at most five per level), legacy credits, and recent attempt summaries.
- Add `runId` and optional `after` to GET for paginated answer history, 100 at a time.
- `POST /api/sornaz/v1/practice/interval-progress/import`: `payload` contains
  `music`, `version: 1`, and the list of legacy completed `levels`.

Responses use the existing JSON response envelope. Upload acknowledgements refer
to the submitted sequence range. The app retains local history after upload.
Progress is restored across devices; full server answer history is available via
the detail API, rather than automatically downloaded into each device journal.

Questions and answers are reported by the practice client. Validation recomputes
correctness and streaks against the frozen curriculum; it is not proof of a
supervised examination. New curriculum versions must preserve old version rules.
Regenerate version 1 only for matching source data with the application tool
`dart run tools/export_interval_curriculum.dart <destination-json-path>`.

## Validation

`scripts/practice_results_test.php` uses isolated SQLite fixtures, exercises all
224 levels, authentication, duplicate retries, ownership, quarter-tones, legacy
imports, and transactional rollback. It is included in `run-release-tests.cjs`.
These fixtures do not replace the staging MySQL and real-device checks above.
