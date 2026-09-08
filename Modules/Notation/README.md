# Music notation

The website route is `/music-sheets`; it is linked from the public header and account sidebar. Apply `Modules/Notation/schema.sql` to the target database when deploying this module. Tables are additive and the script is repeatable. The development database has already been migrated; production deployment is separate.

## Shared editor

`assets/notation/` is the canonical browser implementation. Copy this directory unchanged to the Flutter application's `assets/notation/` (both asset directories are declared in pubspec). VexFlow 4.2.5 and its MIT license are bundled locally; rendering does not download a CDN script. The mobile host is `lib/screens/Notation/music_sheets_page.dart`. Changes to the browser assets must be copied before rebuilding the APK.

List and metadata screens follow light/dark themes and Persian/English locale. The staff editor deliberately remains white in both themes. Select a measure, choose duration/markings, then press the piano to append a note; select an engraved note to change its markings or delete it. Full measures advance automatically. Undo/redo, rests, quarter-tone accidentals, clefs, key/time signatures, tempo, bowing, finger numbers, dynamics, articulations, ornaments, playback and JSON export are supported. The website also offers browser print/PDF. This initial score format supports a single monophonic staff, up to 64 measures; it does not claim chords, multiple voices, MusicXML/MIDI interchange, or sampled instrument sounds. Instrument is score metadata; playback uses a synthesized reference tone.

## API and access

Web session endpoints: `/music-sheets/api`. Mobile bearer endpoints: `/api/sornaz/v1/music-sheets`.

- GET root: `mode=all|mine|saved&page=1`, 30 items per page plus `has_more`.
- GET `/{id}`: visible score and metadata.
- POST root / `/{id}`: create / update with metadata, score, visibility and current version.
- POST `/{id}/delete`: owner soft-delete with current version.
- POST `/{id}/bookmark`: authenticated user's `active` boolean.

Responses use the existing response-factory envelope. Public sheets are readable by everyone; private sheets only by their owner. Only owners may update/delete. Updates and deletions use optimistic versions and a row lock; stale writes return 409. Website writes require session plus a nonempty valid CSRF token. Mobile identity comes exclusively from bearer authentication. The embedded editor receives user ID and settings, never the bearer token. Its native bridge has a fixed operation whitelist and does not accept arbitrary URLs. External navigation is blocked. Export uses a narrowly scoped FileProvider JSON cache directory.

Online saves use the shared server database. A failed save leaves the current editor content in memory for retry or JSON export; this is not background synchronization or persistent offline draft storage. Deploy the server module and schema before expecting online saves from the APK against sornaz.com.

## Validation (2026-09-07)

- 21 isolated PHP service checks: private/public access, owner authorization, bookmarks, revision conflicts, deletion, malformed notes, measure capacity and quarter tones. Connection-local temporary tables only.
- Live local PHP routes: page boot, API envelope, guest rejection, invalid bearer and CSRF rejection.
- Chromium browser: metadata preserved across theme changes, fixed-white editor, notes and markings, undo/redo, save/reopen, JSON export, playback, Persian RTL and desktop/mobile resize.
- Flutter: 35 tests passed including four native notation API checks; changed-file Dart analysis clean. One additional About-us widget test passed: all five exact external URLs launch and each row has the same 24-pixel horizontal inset.

Reference screenshots are the user's 2026-09-07 013321, 013330 and 013345 images. Figma API access hit the account's Starter-plan quota, so implementation used those supplied screenshots.
