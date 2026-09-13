# Music notation

The website route is `/music-sheets`; it is linked from the public header and account sidebar. Apply `Modules/Notation/schema.sql` to the target database when deploying this module. Tables are additive and the script is repeatable. The development database has already been migrated; production deployment is separate.

## Shared editor

`assets/notation/` is the canonical browser implementation. Copy this directory unchanged to the Flutter application's `assets/notation/` (both asset directories are declared in pubspec). VexFlow 4.2.5 and its MIT license are bundled locally; rendering does not download a CDN script. The mobile host is `lib/screens/Notation/music_sheets_page.dart`. Changes to the browser assets must be copied before rebuilding the APK.

List and metadata screens follow light/dark themes and Persian/English locale. The staff editor deliberately remains white in both themes. Select a measure, choose duration/markings, then press the piano to append a note; select an engraved note to change its markings or change its duration. Full measures advance automatically. Undo/redo, rests, quarter-tone accidentals, clefs, key/time signatures, tempo, bowing, finger numbers, dynamics, articulations, ornaments, playback and JSON export are supported. The website also offers browser print/PDF. This initial score format supports a single monophonic staff, up to 64 measures; it does not claim chords, multiple voices, MusicXML/MIDI interchange, or sampled instrument sounds. Instrument is score metadata; playback uses a synthesized reference tone.

## API and access

Web session endpoints: `/music-sheets/api`. Mobile bearer endpoints: `/api/sornaz/v1/music-sheets`.

- GET root: `mode=all|mine|saved&page=1`, 30 items per page plus `has_more`.
- GET `/{id}`: visible score and metadata.
- POST root / `/{id}`: create / update with metadata, score, visibility and current version.
- POST `/{id}/delete`: owner soft-delete with current version.
- POST `/{id}/bookmark`: authenticated user's `active` boolean.

Responses use the existing response-factory envelope. Public sheets are readable by everyone; private sheets only by their owner. Only owners may update/delete. Updates and deletions use optimistic versions and a row lock; stale writes return 409. Website writes require session plus a nonempty valid CSRF token. Mobile identity comes exclusively from bearer authentication. The embedded editor receives user ID and settings, never the bearer token. Its native bridge has a fixed operation whitelist and does not accept arbitrary URLs. External navigation is blocked. Android exports use a dedicated channel that validates the JSON format and sanitizes the filename before writing to the configured folder.

Online saves use the shared server database. Successful saves from the phone also retain an account-scoped local copy for offline reading. Explicit downloads retain a local copy and export JSON into `Sornaz/Music Sheets`; a settings page allows changing this location with the system folder picker. A failed save leaves the current editor content in memory for retry; this is not background synchronization or persistent offline draft storage. Deploy the updated server validation before using the additional clefs and tied notes against sornaz.com.

## Editor revision (2026-09-11)

The editor toolbar holds save, undo/redo, playback, metadata and export, with save status next to the title. Guests see playback only. The horizontally scrolling keyboard spans A0–C8. Overflow notes split into tied segments and shift existing notes forward without losing them; playback sustains across ties. Beams are generated before drawing notes to suppress individual flags. The current measure and sounding note are highlighted, and a gray note marks the next insertion position. Measure numbers accompany subsequent line clefs; tempo is above the first staff.

Clefs use the [VexFlow clef definitions](https://www.vexflow.com/build/docs/clef.html): treble, third/fourth-line F, and first through fourth-line C. The tempo dropdown contains conventional names, with the user's numeric BPM retained independently; see [music21 tempo definitions](https://github.com/cuthbertLab/music21/blob/master/music21/tempo.py).

Checks: `node scripts/notation_model_test.cjs`, `php scripts/notation_validation_test.php`, and `node scripts/notation_browser_test.cjs`. The browser check uses an installed Playwright Core package supplied through `PLAYWRIGHT_CORE_PATH` (or the local ignored browser-check package) and headless Edge. It covers Persian/English mobile layouts, keyboard range, numeric input, clefs, ties, playback and guest controls. No APK is needed for these checks.

## Validation (2026-09-07)

- 21 isolated PHP service checks: private/public access, owner authorization, bookmarks, revision conflicts, deletion, malformed notes, measure capacity and quarter tones. Connection-local temporary tables only.
- Live local PHP routes: page boot, API envelope, guest rejection, invalid bearer and CSRF rejection.
- Chromium browser: metadata preserved across theme changes, fixed-white editor, notes and markings, undo/redo, save/reopen, JSON export, playback, Persian RTL and desktop/mobile resize.
- Flutter: 35 tests passed including four native notation API checks; changed-file Dart analysis clean. One additional About-us widget test passed: all five exact external URLs launch and each row has the same 24-pixel horizontal inset.

Reference screenshots are the user's 2026-09-07 013321, 013330 and 013345 images. Figma API access hit the account's Starter-plan quota, so implementation used those supplied screenshots.

## Compact editor and A4 export

The native toolbar sends commands through the shared editor bridge. Save, metadata, playback, JSON and PDF export remain in the native bar; undo, redo and selected-note deletion overlay the score title. The piano has a rest overlay and a button that opens the animated markings palette. Empty marking choices have blank labels. Beat units are ordered from whole notes to sixty-fourth notes. Subtitles, lyricists and arrangers are optional. Credits share a compact row with tempo and meter.

Saved and read-only scores omit trailing empty bars; only a full last measure creates a new empty editor bar. Playback begins at the selected note, highlighting the sounding note with the theme color. Responsive staves wrap without horizontal scrolling.

Vector A4 export uses approximately five to eight measures per line, with up to ten staff lines on the first page and twelve on subsequent pages. Android opens its system print dialog with Save as PDF; the browser uses print/PDF. Short scores are not padded with empty bars. Each list card includes PDF export and owner-only visibility changes with version conflict checks.

The copyright text field is no longer accepted. The existing music_sheets.owner_id column links ownership to the authenticated users.user_id; clients cannot assign ownership. No additional database column is required.
## Controls revision (2026-09-12)

The editor uses a single top-bar row for save, play/pause and metadata editing. Download and PDF remain list-card actions and are omitted from the editor toolbar. Seven duration buttons and two independently deselectable dot choices share a row above the piano. The other six marking selectors open in a panel anchored to the bottom edge of the viewport, with a close control and backdrop. Selected-note deletion uses a red trash icon. The time signature remains engraved on the staff and is omitted from the tempo caption.