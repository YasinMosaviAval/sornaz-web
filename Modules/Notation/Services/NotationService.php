<?php
namespace Modules\Notation\Services;

use Modules\CourseMarket\Repositories\CourseRepository;
use RuntimeException;

class NotationService
{
    public const KEYS = ['C', 'G', 'D', 'A', 'E', 'B', 'F#', 'C#', 'F', 'Bb', 'Eb', 'Ab', 'Db', 'Gb', 'Cb', 'Am', 'Em', 'Bm', 'F#m', 'C#m', 'G#m', 'D#m', 'A#m', 'Dm', 'Gm', 'Cm', 'Fm', 'Bbm', 'Ebm', 'Abm'];
    public const DURATIONS = ['w' => 64, 'h' => 32, 'q' => 16, '8' => 8, '16' => 4, '32' => 2, '64' => 1, '128' => .5, '256' => .25];

    public function __construct(private CourseRepository $r)
    {
    }

    public function instruments(): array
    {
        $translations = \Core\translation\TranslationService::manager();
        $items = [];
        foreach ($this->r->query('SELECT instrument_id FROM instruments WHERE deleted_at IS NULL ORDER BY instrument_id', []) as $row) {
            $id = (int) $row['instrument_id'];
            $fa = $translations->get('instruments', $id, 'title', 'fa') ?: '';
            $en = $translations->get('instruments', $id, 'title', 'en') ?: '';
            $items[] = ['id' => $id, 'fa' => $fa ?: $en ?: (string) $id, 'en' => $en ?: $fa ?: (string) $id];
        }
        return $items;
    }

    public function listing(int $actor, string $mode, int $page = 1): array
    {
        if (!in_array($mode, ['all', 'mine', 'saved'], true)) {
            throw new RuntimeException('Invalid list.', 422);
        }
        if ($mode !== 'all' && $actor < 1) {
            throw new RuntimeException('Sign in to continue.', 401);
        }
        $where = 's.deleted_at IS NULL AND (s.visibility=\'public\' OR s.owner_id=?)';
        $args = [$actor];
        if ($mode === 'mine') {
            $where .= ' AND s.owner_id=?';
            $args[] = $actor;
        }
        if ($mode === 'saved') {
            $where .= ' AND EXISTS(SELECT 1 FROM music_sheet_bookmarks b WHERE b.sheet_id=s.id AND b.user_id=?)';
            $args[] = $actor;
        }
        $offset = (max(1, min($page, 10000)) - 1) * 30;
        $rows = $this->r->query("SELECT s.id,s.owner_id,s.title,s.metadata,s.visibility,s.version,s.updated_at,u.username author FROM music_sheets s JOIN users u ON u.user_id=s.owner_id AND u.deleted_at IS NULL WHERE $where ORDER BY s.updated_at DESC,s.id DESC LIMIT 31 OFFSET $offset", $args);
        $more = count($rows) > 30;
        return ['items' => array_map(fn ($row) => $this->present($row, $actor), array_slice($rows, 0, 30)), 'has_more' => $more];
    }

    private function present(array $row, int $actor): array
    {
        foreach (['id', 'owner_id', 'version'] as $key) {
            $row[$key] = (int) $row[$key];
        }
        $row['metadata'] = json_decode($row['metadata'], true);
        if (isset($row['score'])) {
            $row['score'] = json_decode($row['score'], true);
        }
        $row['editable'] = $actor > 0 && $row['owner_id'] === $actor;
        $row['saved'] = $actor > 0 && (bool) $this->r->one('SELECT sheet_id FROM music_sheet_bookmarks WHERE user_id=? AND sheet_id=?', [$actor, $row['id']]);
        return $row;
    }

    public function show(int $actor, int $id): array
    {
        $row = $this->r->one('SELECT s.*,u.username author FROM music_sheets s JOIN users u ON u.user_id=s.owner_id AND u.deleted_at IS NULL WHERE s.id=? AND s.deleted_at IS NULL', [$id]);
        if (!$row || ($row['visibility'] !== 'public' && (int) $row['owner_id'] !== $actor)) {
            throw new RuntimeException('Sheet not found.', 404);
        }
        return $this->present($row, $actor);
    }

    private function text(mixed $value, int $limit = 180): string
    {
        if (!is_string($value) || mb_strlen($value) > $limit) {
            throw new RuntimeException('Invalid text length.', 422);
        }
        return trim($value);
    }

    public function validate(array $data): array
    {
        $meta = $data['metadata'] ?? null;
        $score = $data['score'] ?? null;
        if (!is_array($meta) || !is_array($score) || !isset($score['measures']) || !is_array($score['measures']) || !array_is_list($score['measures']) || count($score['measures']) < 1 || count($score['measures']) > 64) {
            throw new RuntimeException('Use between 1 and 64 measures.', 422);
        }
        $clean = [];
        foreach (['title', 'subtitle', 'composer', 'arranger', 'lyricist', 'tempo_text'] as $field) {
            $clean[$field] = $this->text($meta[$field] ?? '', $field === 'subtitle' ? 1000 : 180);
        }
        if ($clean['title'] === '') {
            throw new RuntimeException('Enter a title.', 422);
        }
        // Keep legacy names readable; new scores select stable catalog IDs.
        $clean['instrument'] = $this->text($meta['instrument'] ?? '');
        if ($clean['instrument'] === '') {
            throw new RuntimeException('Invalid instrument.', 422);
        }
        if (ctype_digit($clean['instrument']) && !$this->r->one('SELECT instrument_id FROM instruments WHERE instrument_id=? AND deleted_at IS NULL', [(int) $clean['instrument']])) {
            throw new RuntimeException('Invalid instrument.', 422);
        }
        if (!ctype_digit($clean['instrument']) && !in_array($clean['instrument'], ['Tar', 'Setar', 'Guitar', 'Piano', 'Violin', 'Flute', 'Voice'], true)) {
            throw new RuntimeException('Invalid instrument.', 422);
        }
        $enums = ['key' => self::KEYS, 'time' => ['2/4', '3/4', '4/4', '6/8', '9/8', '12/8', '2/2', '6/4'], 'tempo_note' => array_map('strval', array_keys(self::DURATIONS)), 'clef' => ['treble', 'bass', 'baritone-f', 'soprano', 'mezzo-soprano', 'alto', 'tenor']];
        foreach ($enums as $key => $values) {
            $v = $meta[$key] ?? null;
            if (!in_array($v, $values, true)) {
                throw new RuntimeException('Invalid ' . $key . '.', 422);
            }
            $clean[$key] = $v;
        }
        $staves = $meta['staves'] ?? [['clef' => $clean['clef']]];
        if (!is_array($staves) || !array_is_list($staves) || count($staves) < 1 || count($staves) > 2) {
            throw new RuntimeException('Invalid staves.', 422);
        }
        $clean['staves'] = [];
        foreach ($staves as $stave) {
            if (!is_array($stave) || !in_array($stave['clef'] ?? null, $enums['clef'], true)) {
                throw new RuntimeException('Invalid staff clef.', 422);
            }
            $clean['staves'][] = ['clef' => $stave['clef']];
        }
        $clean += $this->extendedMetadata($meta, $clean['key']);
        $clean['bpm'] = $this->validatedBpm($meta['bpm'] ?? null);
        [$top,$bottom] = array_map('intval', explode('/', $clean['time']));
        $capacity = $top * 64 / $bottom;
        $measures = [];
        foreach ($score['measures'] as $measure) {
            if (!is_array($measure) || !isset($measure['notes']) || !is_array($measure['notes']) || !array_is_list($measure['notes']) || count($measure['notes']) > 128) {
                throw new RuntimeException('Invalid measure.', 422);
            }
            if (isset($measure['preserve']) && !is_bool($measure['preserve'])) {
                throw new RuntimeException('Invalid measure.', 422);
            }
            $notes = [];
            $ticks = [1 => 0, 2 => 0];
            foreach ($measure['notes'] as $n) {
                if (!is_array($n) || !isset($n['pitch']) || !is_string($n['pitch']) || !preg_match('/^(?:[A-G][1-7]|[AB]0|C8)$/D', $n['pitch']) || !is_string($n['duration'] ?? null) || !isset(self::DURATIONS[$n['duration']])) {
                    throw new RuntimeException('Invalid note.', 422);
                }
                $dots = $n['dots'] ?? 0;
                if (!is_int($dots) || $dots < 0 || $dots > 2 || !is_bool($n['rest'] ?? null)) {
                    throw new RuntimeException('Invalid note duration.', 422);
                }
                $acc = $n['accidental'] ?? '';
                if (!in_array($acc, ['', '#', 'b', 'n', '##', 'bb', '+', 'd'], true)) {
                    throw new RuntimeException('Invalid accidental.', 422);
                }
                $staff = $n['staff'] ?? 1;
                if (!is_int($staff) || !in_array($staff, [1, 2], true)) {
                    throw new RuntimeException('Invalid staff.', 422);
                }
                $ticks[$staff] += self::DURATIONS[$n['duration']] * (2 - pow(.5, $dots));
                if ($ticks[$staff] > $capacity + .001) {
                    throw new RuntimeException('This measure is full.', 422);
                }
                $note = ['pitch' => $n['pitch'], 'duration' => $n['duration'], 'dots' => $dots, 'rest' => $n['rest'], 'accidental' => $acc, 'staff' => $staff];
                $pitches = $n['pitches'] ?? [];
                if (!is_array($pitches) || !array_is_list($pitches) || count($pitches) > 7 || ($n['rest'] && $pitches)) {
                    throw new RuntimeException('Invalid chord.', 422);
                }
                $note['pitches'] = [];
                foreach ($pitches as $tone) {
                    if (!is_array($tone) || !is_string($tone['pitch'] ?? null)
                        || !preg_match('/^(?:[A-G][1-7]|[AB]0|C8)$/D', $tone['pitch'])
                        || !in_array($tone['accidental'] ?? '', ['', '#', 'b', 'n', '##', 'bb', '+', 'd'], true)) {
                        throw new RuntimeException('Invalid chord tone.', 422);
                    }
                    $note['pitches'][] = ['pitch' => $tone['pitch'], 'accidental' => $tone['accidental'] ?? ''];
                }
                foreach (['tieNext', 'tiePrevious'] as $tie) {
                    if (isset($n[$tie])) {
                        if (!is_bool($n[$tie]) || ($n[$tie] && $n['rest'])) {
                            throw new RuntimeException('Invalid tie.', 422);
                        }
                        $note[$tie] = $n[$tie];
                    }
                }
                foreach (['dynamic' => ['', 'ppp', 'pp', 'p', 'mp', 'mf', 'f', 'ff', 'fff', 'sf', 'sff', 'sfff', 'sfz', 'sffz', 'sfffz', 'fz', 'ffz', 'fffz'], 'articulation' => ['', 'staccato', 'accent', 'tenuto', 'marcato', 'staccatissimo'], 'bow' => ['', 'up', 'down'], 'ornament' => ['', 'trill', 'mordent'], 'finger' => ['', '0', '1', '2', '3', '4', '5']] as $field => $values) {
                    $value = $n[$field] ?? '';
                    if (!in_array($value, $values, true)) {
                        throw new RuntimeException('Invalid note marking.', 422);
                    }
                    $note[$field] = $value;
                }
                $notes[] = $note;
            }
            $measures[] = ['notes' => $notes, 'preserve' => ($measure['preserve'] ?? false) === true];
        }
        while ($measures && !$measures[count($measures) - 1]['notes'] && !$measures[count($measures) - 1]['preserve']) {
            array_pop($measures);
        }
        if (!$measures) {
            throw new RuntimeException('Enter at least one note before saving.', 422);
        }
        $visibility = $data['visibility'] ?? 'private';
        if (!in_array($visibility, ['private', 'public'], true)) {
            throw new RuntimeException('Invalid visibility.', 422);
        }
        return ['title' => $clean['title'], 'metadata' => json_encode($clean, JSON_UNESCAPED_UNICODE), 'score' => json_encode(['measures' => $measures], JSON_UNESCAPED_UNICODE), 'visibility' => $visibility];
    }

    private function extendedMetadata(array $meta, string $key): array
    {
        $scaleType = $meta['scale_type'] ?? (str_ends_with($key, 'm') ? 'minor' : 'major');
        if (!in_array($scaleType, ['major', 'minor', 'melodic_minor', 'harmonic_minor'], true)
            || ($scaleType === 'major') === str_ends_with($key, 'm')) {
            throw new RuntimeException('Invalid scale.', 422);
        }
        $tempoDots = $meta['tempo_dots'] ?? 0;
        if (!is_int($tempoDots) || $tempoDots < 0 || $tempoDots > 2) {
            throw new RuntimeException('Invalid beat dots.', 422);
        }
        return ['scale_type' => $scaleType, 'tempo_dots' => $tempoDots];
    }

    private function validatedBpm(mixed $value): int
    {
        $bpm = filter_var($value, FILTER_VALIDATE_INT);
        if ($bpm === false || $bpm < 20 || $bpm > 300) {
            throw new RuntimeException('Tempo must be between 20 and 300.', 422);
        }
        return $bpm;
    }

    public function save(int $actor, int $id, array $data): array
    {
        if ($actor < 1) {
            throw new RuntimeException('Sign in to continue.', 401);
        }
        $values = $this->validate($data);
        if (!$id) {
            $id = $this->r->insert('music_sheets', ['owner_id' => $actor] + $values);
            return $this->show($actor, $id);
        }
        $this->r->transaction(function () use ($actor, $id, $values, $data) {
            $row = $this->r->one('SELECT owner_id,version,deleted_at FROM music_sheets WHERE id=? FOR UPDATE', [$id]);
            if (!$row || $row['deleted_at'] !== null) {
                throw new RuntimeException('Sheet not found.', 404);
            }
            if ((int) $row['owner_id'] !== $actor) {
                throw new RuntimeException('Only the owner can edit this sheet.', 403);
            }
            if ((int) ($data['version'] ?? 0) !== (int) $row['version']) {
                throw new RuntimeException('This sheet changed elsewhere. Reload before saving.', 409);
            }
            $this->r->query('UPDATE music_sheets SET title=?,metadata=?,score=?,visibility=?,version=version+1,updated_at=CURRENT_TIMESTAMP WHERE id=?', array_merge(array_values($values), [$id]));
        });
        return $this->show($actor, $id);
    }

    public function remove(int $actor, int $id, int $version): array
    {
        if ($actor < 1) {
            throw new RuntimeException('Sign in to continue.', 401);
        }
        return $this->r->transaction(function () use ($actor, $id, $version) {
            $s = $this->r->one('SELECT owner_id,version,deleted_at FROM music_sheets WHERE id=? FOR UPDATE', [$id]);
            if (!$s || $s['deleted_at'] !== null) {
                throw new RuntimeException('Sheet not found.', 404);
            }
            if ((int) $s['owner_id'] !== $actor) {
                throw new RuntimeException('Only the owner can delete this sheet.', 403);
            }
            if ((int) $s['version'] !== $version) {
                throw new RuntimeException('This sheet changed elsewhere. Reload before deleting.', 409);
            }
            $this->r->query('UPDATE music_sheets SET deleted_at=CURRENT_TIMESTAMP,version=version+1 WHERE id=?', [$id]);
            return ['deleted' => true];
        });
    }

    public function bookmark(int $actor, int $id, bool $active): array
    {
        if ($actor < 1) {
            throw new RuntimeException('Sign in to continue.', 401);
        }
        $this->show($actor, $id);
        if ($active) {
            $this->r->query('INSERT IGNORE INTO music_sheet_bookmarks(user_id,sheet_id) VALUES(?,?)', [$actor, $id]);
        } else {
            $this->r->query('DELETE FROM music_sheet_bookmarks WHERE user_id=? AND sheet_id=?', [$actor, $id]);
        }
        return ['saved' => $active];
    }
}
