# Stage 4: reliability

## Payment behavior

- Course purchases, installment payments and subscription payments serialize start/callback operations with a resource-specific MySQL advisory lock. Online and offline installment operations use the same invoice lock. Subscription period generation also has a lock.
- A repeated checkout reconciles the existing authority with the gateway. `IN_BANK` reuses the authority; `PAID`/`VERIFIED` still require verification using the stored amount; `FAILED`/`REVERSED` permit a new attempt. Unknown responses or timeouts do not authorize another charge. A callback cancellation hint alone does not prove that no money was received.
- Successful verification requires an accepted code and a reference ID. Replayed callbacks cannot downgrade paid records. Invoice status follows remaining unpaid installments.
- An additional verified course purchase is stored as `paid_review` (the existing status column is VARCHAR). It does not grant a second entitlement or inflate the normal seller sales list. Its receipt asks the buyer to contact support. It still represents received funds, not a refund.
- Duplicate or mismatched verified installment/subscription funds are recorded as paid with `gateway_message = 'Verified payment requires manual reconciliation'`. The installment/period is not overwritten and the callback displays a review message. Replaying the callback preserves that message. Support must reconcile or refund through the gateway; this change performs no automatic refunds.
- An active subscription payment prevents switching to a different priced plan. A canceled/failed attempt is checked with the gateway before retry. Offline payments reject an active online attempt or a repeated reference on the same invoice.
- Requests without a saved authority after an interrupted operation require support review. They are not silently discarded based on age.

Inquiry is a status lookup, not payment verification. Implementation follows the [official inquiry documentation](https://www.zarinpal.com/docs/paymentGateway/otherMethods/Inquiry) and [verification documentation](https://www.zarinpal.com/docs/sdk/nodejs/method/verify). Production requests now use `https://payment.zarinpal.com`; sandbox remains separate.

## Database correctness and performance

- Normal, raw and EXISTS predicates retain binding order. UPDATE/DELETE include raw conditions; OR groups remain bounded by later conditions.
- Chat reaction totals and viewer likes use one grouped query per batch instead of two queries per message. A 200-message fixture asserts exactly one reaction SELECT instead of 400. This is a query-count improvement, not a production latency benchmark. Other profile/reply lookups and large-history rendering still need measurement under production load.
- Invoice access bootstrap is computed once per payment check.
- Offline payment requests no longer execute schema changes or query information_schema. Required columns belong in deployment migrations.
- Native-panel tests exposed a regression after session hardening. The server now binds its temporary bearer context to the verified user's password fingerprint and clears the temporary remember token, then restores the original browser session.

## Deployment and remaining checks

1. Preserve the production `.env` and database backup. Deploy all changed PHP/JavaScript and Composer metadata together; regenerate Composer metadata on Linux if possible.
2. Stage 4 adds no required table. Verify that the existing `2026_09_01_add_manual_payments_and_academy_subscriptions.sql` migration was already applied, including `payer_name` and `bank_card_type`. Do not blindly rerun schema changes on a live database. The stage 2 security tables remain required.
3. MySQL must support `GET_LOCK`/`RELEASE_LOCK`, and all payment writers must use the same primary database server. This is not a multi-primary distributed lock. Acquisition waits at most five seconds and fails closed. Keep gateway calls outside database transactions. Do not change merchant, sandbox or currency settings while payments are unresolved.
4. Run the optional two-connection test against an isolated MySQL server using `SORNAZ_TEST_MYSQL_DSN`, `SORNAZ_TEST_MYSQL_USER`, and `SORNAZ_TEST_MYSQL_PASSWORD`: `php scripts/payment_mysql_lock_test.php`. Credentials must stay in the environment. This test does not use application tables.
5. In a deployed test environment, exercise simultaneous requests from two browser sessions, a gateway timeout, a canceled return, repeated success callbacks, one partial invoice followed by full settlement, and a subscription plan change while payment is pending. Confirm the provider's actual sandbox behavior before enabling production checkout.
6. Review legacy multiple open authorities and existing duplicate settlements before rollout. New locks cannot undo requests or charges created by older code. Review `creator_course_orders` with `status='paid_review'` and invoice/subscription payments with the reconciliation message above. Refunds and changes to existing financial records require an operator's reconciliation.
7. Deploy through a maintenance window or coordinated worker restart so old and new payment code do not run concurrently. Restart PHP workers/clear OPcache and changed asset caches as appropriate.

## Local verification

```text
node scripts/run-release-tests.cjs --browser
```

The runner uses a fixed allowlist of isolated tests, reports each failure, and exits nonzero if any suite fails. Browser suites use mocked routes. Gateway tests simulate responses and never send payment requests. The standard runner does not exercise MySQL advisory locks or live gateway connectivity; the optional MySQL test and sandbox checks above remain deployment tasks.

No production deployment, live financial transaction, credential change, historical refund or APK build was performed. Stage 1 Nginx private-file protection and stage 2 production verification remain outstanding independently of these local changes.
