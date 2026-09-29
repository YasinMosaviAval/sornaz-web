# Stage 3: core functionality

## Changes

- The public contact form posts to `/contact` with CSRF and request throttling. A successful response now means the message and both language records were committed to the database. Messages appear in the existing administrator message inbox for user 1 (`/analytics/admin-messages`). Delivery by email is not implemented or claimed.
- The mobile contact endpoint uses the same persistence service. Existing mobile clients may omit contact details; web guests must provide a name and email. Authenticated web submissions use the account identity. No mobile build is required.
- Failed contact submissions preserve the form contents. Duplicate concurrent submissions from the same form are prevented. A network failure after commit can still require checking the inbox before retrying; no cross-request idempotency key is implemented.
- Unknown routes return HTTP 404, including structured JSON for API requests. Plain string route results are no longer discarded by the response class.
- Chat responses include `hasMore` for backlog pagination and accept up to 200 message IDs in `refresh`. Only messages in the authorized conversation are returned. Responses include current message data and deleted IDs for that refresh batch.
- The web chat drains pending pages, merges messages by ID and periodically checks loaded messages in batches. Large histories take several polling cycles to refresh all older edits/reactions. Read receipts cannot move backwards or jump ahead on an empty page.
- Room changes invalidate old requests. Late send responses do not clear the draft of a different room. Replies, shared-content links, image previews, unavailable shared content and system messages render in the panel and embedded community chat. Message text remains escaped.

## Deployment

Deploy the PHP changes, generated Composer metadata and changed JavaScript together. This stage uses existing `user_messages` and `translations` tables and introduces no new migration. The stage 2 account-security migration remains required because contact requests use its rate-limit storage.

Preserve existing Linux filename casing, particularly `core/http/response.php`. Clear stale PHP opcode caches and changed asset caches as needed.

## Verification

Local tests use an in-memory SQLite database and mocked browser routes:

```text
php scripts/core_functionality_test.php
php scripts/chat_media_revision_test.php
node scripts/core_functionality_browser_test.cjs
```

On the deployed site, submit a controlled guest contact and an authenticated contact, then check the administrator inbox in both languages. Verify a nonexistent page and API endpoint return 404. With two test accounts, confirm new messages, edits, deletions, likes, attachments and rapid room switching in both the user panel and community chat.

Local tests do not confirm production deployment. Pending Nginx private-file protection from stage 1 and production checks from stage 2 remain separate outstanding items.
