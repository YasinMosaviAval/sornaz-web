# Music notation

The website route is `/music-sheets`; it is linked from the public header and account sidebar. Apply `Modules/Notation/schema.sql` to the target database when deploying this module. Tables are additive and the script is repeatable. The development database has already been migrated; production deployment is separate.

## Shared editor

The model, importer, engraving, music symbols and core editor in `assets/notation/` are shared with Flutter. Website-only adapters are `web_storage.js`, `web_tools.js`, `omr.js` and `panel.css`; do not copy the whole website directory over the mobile bundle. VexFlow 4.2.5 and its MIT license are bundled locally; rendering does not download a CDN script. The mobile host is `lib/screens/Notation/music_sheets_page.dart`.

## Website parity (2026-10-25)

The account panel uses a web workspace: responsive sheet cards, panel forms, document-flow engraving tools and a settings tab next to the new-sheet action. Settings preserve the open unsaved score and offer a return action. The public web editor also opens settings as a page. Native Flutter assets and layouts are unchanged. Deployment details for this UI revision and the site-wide header help controls are in `docs/notation-web-layout-header-help.fa.md`.

The website now supplies the native host features that the shared editor previously called without a web implementation: account-scoped local saves and a last-valid backup, explicit upload with remote revision checks, local playlists and favorites, JSON downloads, and persistent duration/piano-label preferences. Existing server sheets remain available through All/Mine/Saved; local lists and drafts are specific to the browser and account. Save stores a local draft; Upload is the explicit server operation. Clearing browser storage removes local drafts, so download JSON for an independent backup. Offline editing works after the page and its assets have loaded; this is not a new installable offline application or a background synchronization service.

The founder also receives the same experimental image-to-melody review workflow: gallery/camera input, crop/reset, local pixel recognition in a worker, small-angle deskew, manual clef/pitch/duration review and the standard import preview/confirmation. Exactly one printed five-line staff is supported. Rhythm, rests and uncertain accidentals are never invented. Images are not uploaded. Browser downloads use the browser's configured location instead of the native filesystem picker. Independent Flutter development screens for audio/performance analysis are not linked from its notation page and are not part of this migration.

The shared editor already supports grand staff, chords, voices, tuplets, staff transfer, repeats and jumps, key/tempo/clef changes, beams, ornaments, dynamics, piano pedals, text, ties/slurs, playback and JSON/MusicXML/PDF interchange. Seven Flutter behavioral fixtures now run against the website assets, covering timing, MusicXML round trips, import confirmation/undo, grand staff and narrow-screen engraving. See `docs/notation-web-parity-deployment.fa.md` for the exact deployment and verification list.

List and metadata screens follow light/dark themes and Persian/English locale. The staff editor deliberately remains white in both themes. Select a measure, choose duration/markings, then press the piano to append a note; select an engraved note to change its markings or change its duration. Full measures advance automatically. Scores support up to 64 measures, two staves and independent voices. MIDI interchange and sampled instrument sounds are not implemented; playback uses a synthesized reference tone.

## API and access

Web session endpoints: `/music-sheets/api`. Mobile bearer endpoints: `/api/sornaz/v1/music-sheets`.

- GET root: `mode=all|mine|saved&page=1`, 30 items per page plus `has_more`.
- GET `/instruments`: active instrument IDs and Persian/English titles from the instruments catalog. New metadata stores the selected ID; legacy instrument names remain readable. Flutter caches the catalog for offline reuse.
- GET `/{id}`: visible score and metadata.
- POST root / `/{id}`: create / update with metadata, score, visibility and current version.
- POST `/{id}/delete`: owner soft-delete with current version.
- POST `/{id}/bookmark`: authenticated user's `active` boolean.

Responses use the existing response-factory envelope. Public sheets are readable by everyone; private sheets only by their owner. Only owners may update/delete. Updates and deletions use optimistic versions and a row lock; stale writes return 409. Website writes require session plus a nonempty valid CSRF token. Mobile identity comes exclusively from bearer authentication. The embedded editor receives user ID and settings, never the bearer token. Its native bridge has a fixed operation whitelist and does not accept arbitrary URLs. External navigation is blocked. Android exports use a dedicated channel that validates the JSON format and sanitizes the filename before writing to the configured folder.

Explicit uploads use the shared server database; local Save retains an account-scoped draft. Native downloads export JSON into `Sornaz/Music Sheets` and native settings expose a folder picker; website downloads use the browser's download settings. A failed save keeps the editor content available for retry. There is no background synchronization. Deploy compatible server validation before using additional score fields against sornaz.com.

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

## Form and engraving revision (2026-09-13)

The metadata form has five rows with equal flexible spacing. Description, composer, arranger and lyricist are optional. New scores start private; visibility is changed on the list card. Tempo names become available after selecting a beat unit and valid integer BPM, and only overlapping practice ranges are offered. `ui_symbols.js` supplies proportional vector beat/rest/control symbols without depending on platform music fonts. Saved status is green; unsaved status is yellow.

Low and high pitches use display-only octave shifts with bracket labels: A0–B1 use 16vb, C2–G2 use 8vb, C7–B7 use 8va, and C8 uses 16va (the requested app labels). Stored pitches and playback frequencies are unchanged. Brackets span adjacent notes, restart on wrapped lines, and are included in A4 PDF engraving.

Verification: `node scripts/notation_browser_test.cjs`, `node scripts/notation_model_test.cjs`, `php scripts/notation_validation_test.php`, and `php scripts/notation_instruments_test.php`. Browser checks cover both locales, form spacing, tempo filtering, database IDs, saved-state colors, octave brackets, pitch preservation and PDF output.
