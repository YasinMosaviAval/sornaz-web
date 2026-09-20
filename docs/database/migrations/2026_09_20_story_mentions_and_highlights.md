# Story mentions and profile highlights

Apply `2026_09_20_story_mentions_and_highlights.sql` before deploying the updated Social API or app. The migration creates three tables and does not modify existing stories. It has not been run against the production database.

- Mentions are stored separately from the rendered media so profiles remain clickable.
- Highlight editing and archive access require the owning account.
- Highlighted stories and covers remain publicly viewable after story expiry; unhighlighted expired media remains private.
- Deleting a highlight does not delete its source stories.

Validation: `php scripts/story_highlights_test.php` and `php scripts/story_feed_test.php` use isolated SQLite databases and do not connect to production.