<?php
namespace Modules\Analytics\Controllers\Api;

use Core\http\ResponseFactory;

final class MobileLearningController
{
    public function index()
    {
        $learningUserId = (int) auth()->id();
        $learningLocale = locale() === 'en' ? 'en' : 'fa';
        $learningSql = "SELECT e.type AS enrollment_type,e.status AS enrollment_status,
 t.term_id,t.status AS term_status,t.start_date,t.end_date,
 c.course_id,c.branch_id,
 s.term_session_id,b.requested_date,b.start_time,b.end_time,
 r.classroom_id,
 COALESCE((SELECT value FROM translations WHERE table_name='academy_branch_courses' AND table_id=c.course_id AND field='title' AND locale=? AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),CONCAT('دوره ',c.course_id)) AS course_title,
 COALESCE((SELECT value FROM translations WHERE table_name='academy_branch_course_terms' AND table_id=t.term_id AND field='title' AND locale=? AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),CONCAT('ترم ',t.term_id)) AS term_title,
 COALESCE((SELECT value FROM translations WHERE table_name='academy_branches' AND table_id=c.branch_id AND field='name' AND locale=? AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),CONCAT('شعبه ',c.branch_id)) AS branch_title,
 COALESCE((SELECT value FROM translations WHERE table_name='academy_branch_classrooms' AND table_id=r.classroom_id AND field='title' AND locale=? AND deleted_at IS NULL ORDER BY translation_id DESC LIMIT 1),IF(r.classroom_id IS NULL,'—',CONCAT('کلاس ',r.classroom_id))) AS classroom_title
 FROM academy_branch_members m
 JOIN academy_branch_course_term_enrollments e ON e.member_id=m.member_id AND e.deleted_at IS NULL AND e.type IN ('teacher','student')
 JOIN academy_branch_course_terms t ON t.term_id=e.term_id AND t.deleted_at IS NULL
 JOIN academy_branch_courses c ON c.course_id=t.course_id AND c.deleted_at IS NULL
 LEFT JOIN academy_branch_course_term_sessions s ON s.term_id=t.term_id AND e.status='active' AND m.status='active' AND s.deleted_at IS NULL
 LEFT JOIN academy_branch_bookings b ON b.booking_id=s.booking_id AND b.deleted_at IS NULL
 LEFT JOIN academy_branch_classrooms r ON r.classroom_id=s.classroom_id AND r.deleted_at IS NULL
 WHERE m.user_id=? AND m.deleted_at IS NULL
 ORDER BY COALESCE(b.requested_date,t.start_date) DESC,b.start_time";
        $learningStatement = db()->prepare($learningSql);
        $learningStatement->execute([$learningLocale, $learningLocale, $learningLocale, $learningLocale, $learningUserId]);
        $learningRows = $learningStatement->fetchAll(\PDO::FETCH_ASSOC);
        $learningCourses = $learningTerms = $learningClasses = [];
        foreach ($learningRows as $learningRow) {
            $learningCourses[(int) $learningRow['course_id']] = $learningRow;
            $termId = (int) $learningRow['term_id'];
            if (!isset($learningTerms[$termId])) $learningTerms[$termId] = $learningRow + ['sessions' => []];
            if (!empty($learningRow['term_session_id'])) {
                $sessionId = (int) $learningRow['term_session_id'];
                $learningTerms[$termId]['sessions'][$sessionId] = ['id' => $sessionId, 'termId' => $termId, 'date' => $learningRow['requested_date'], 'startTime' => substr((string) $learningRow['start_time'], 0, 5), 'endTime' => substr((string) $learningRow['end_time'], 0, 5)];
                $learningClasses[$sessionId] = $learningRow + ['id' => $sessionId, 'sessionId' => $sessionId];
            }
        }
        foreach ($learningTerms as &$term) $term['sessions'] = array_values($term['sessions']);
        unset($term);
        return ResponseFactory::json(['courses' => array_values($learningCourses), 'terms' => array_values($learningTerms), 'classes' => array_values($learningClasses)]);
    }
}
