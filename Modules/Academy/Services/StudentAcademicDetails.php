<?php
namespace Modules\Academy\Services;

use Core\database\DB;
use Core\translation\TranslationService;

/** Enrich only memberships already authorized by the caller. */
final class StudentAcademicDetails
{
    public function enrich(array $members): array
    {
        $tr = TranslationService::manager();
        $terms = [];
        $students = array_values(array_filter($members, fn ($member) => ($member['type'] ?? '') === 'student'));
        $ids = array_values(array_filter(array_unique(array_map(fn ($member) => (int) ($member['id'] ?? 0), $students))));
        $registrations = [];
        foreach ($ids ? DB::table('academy_branch_course_term_enrollments')->select('academy_branch_course_term_enrollments.*')->join('academy_branch_course_terms', 'academy_branch_course_terms.term_id', '=', 'academy_branch_course_term_enrollments.term_id')->whereIn('academy_branch_course_term_enrollments.member_id', $ids)->where('academy_branch_course_term_enrollments.type', 'student')->whereNull('academy_branch_course_term_enrollments.deleted_at')->whereNull('academy_branch_course_terms.deleted_at')->get() : [] as $enrollment) {
            $registrations[(int) $enrollment['member_id']][(int) $enrollment['term_id']] = $enrollment;
        }
        foreach ($members as &$member) {
            if (($member['type'] ?? '') !== 'student') continue;
            $termId = (int) ($member['termId'] ?? 0);
            if ($termId && !isset($terms[$termId])) $terms[$termId] = $this->term($termId);
            $details = $terms[$termId] ?? ['level' => '', 'teacher' => '', 'teacherNotes' => []];
            $member = array_replace($member, $details);
            $member['termRegistrations'] = [];
            foreach ($registrations[(int) ($member['id'] ?? 0)] ?? [] as $registeredTermId => $enrollment) {
                if (!isset($terms[$registeredTermId])) $terms[$registeredTermId] = $this->term($registeredTermId);
                $name = $tr->get('academy_branch_course_terms', $registeredTermId, 'title', 'fa') ?: ('#' . $registeredTermId);
                $member['termRegistrations'][] = array_replace($terms[$registeredTermId], [
                    'termId' => $registeredTermId, 'termKey' => (string) $registeredTermId,
                    'termName' => $name, 'instrument' => $name,
                    'enrollmentId' => (int) ($enrollment['term_enrollment_id'] ?? $enrollment['enrollment_id'] ?? 0),
                ]);
            }
            if (!(int) ($member['branchId'] ?? 0)) {
                $academy = DB::table('academies')->where('user_id', (int) ($member['organizationUserId'] ?? 0))->whereNull('deleted_at')->first();
                if ($academy) $member['branch'] = $tr->get('academies', (int) $academy['academy_id'], 'title', 'fa') ?: $tr->get('users', (int) $academy['user_id'], 'full_name', 'fa') ?: ('آموزشگاه ' . $academy['academy_id']);
            }
        }
        unset($member);
        return $members;
    }

    private function term(int $id): array
    {
        $tr = TranslationService::manager();
        $term = DB::table('academy_branch_course_terms')->where('term_id', $id)->whereNull('deleted_at')->first();
        if (!$term) return ['level' => '', 'teacher' => '', 'teacherNotes' => []];
        $course = DB::table('academy_branch_courses')->where('course_id', (int) $term['course_id'])->whereNull('deleted_at')->first();
        $levelId = 0;
        if ($course) {
            $org = !empty($course['branch_id']) ? DB::table('academy_branches')->where('branch_id', (int) $course['branch_id'])->first() : DB::table('academies')->where('academy_id', (int) $course['academy_id'])->first();
            if ($org) {
                $lesson = DB::table('user_lessons')->where('user_id', (int) $org['user_id'])->where('lesson_id', (int) $course['lesson_id'])->whereNull('deleted_at')->first();
                $levelId = (int) ($lesson['level_id'] ?? 0);
            }
        }
        $teachers = [];
        foreach (DB::table('academy_branch_course_term_enrollments')->where('term_id', $id)->where('type', 'teacher')->whereNull('deleted_at')->get() as $enrollment) {
            $member = DB::table('academy_branch_members')->where('member_id', (int) $enrollment['member_id'])->whereNull('deleted_at')->first();
            if ($member) $teachers[] = $tr->get('users', (int) $member['user_id'], 'full_name', 'fa') ?: ('مدرس ' . $member['user_id']);
        }
        return ['levelId' => $levelId, 'level' => $levelId ? ($tr->get('levels', $levelId, 'title', 'fa') ?: ('سطح ' . $levelId)) : '', 'teacher' => implode('، ', array_unique($teachers)), ...$this->lessonDetails($course), ...$this->sessionDetails($id)];
    }

    private function lessonDetails(?array $course): array
    {
        $id = (int) ($course['lesson_id'] ?? 0);
        return ['lessonId' => $id, 'lessonName' => $id ? (TranslationService::manager()->get('lessons', $id, 'title', 'fa') ?: '—') : '—'];
    }

    private function sessionDetails(int $id): array
    {
        $tr = TranslationService::manager();
        $notes = [];
        $modes = [];
        foreach (DB::table('academy_branch_course_term_sessions')->where('term_id', $id)->whereNull('deleted_at')->orderBy('term_session_id')->get() as $session) {
            $mode = $session['delivery_mode'] ?? null;
            if (in_array($mode, ['in_person', 'online'], true)) $modes[$mode] = true;
            $note = $tr->get('academy_branch_course_term_sessions', (int) $session['term_session_id'], 'teacher_note', 'fa');
            if (trim((string) $note) !== '') $notes[] = ['sessionId' => (int) $session['term_session_id'], 'text' => $note];
        }
        return ['teacherNotes' => $notes, 'deliveryModes' => array_keys($modes)];
    }
}
