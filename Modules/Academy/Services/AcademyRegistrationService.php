<?php

namespace Modules\Academy\Services;

use Core\database\DB;
use Core\translation\TranslationService;
use Modules\System\Services\UserService;
use RuntimeException;

class AcademyRegistrationService {
    public function __construct(protected UserService $users) {}

    public function register(array $data): int {
        return transaction(function () use ($data) {
            $data['type'] = 'academy';
            $data['full_name'] = $data['academy_name'];
            $userId = $this->users->register($data);
            if (!$userId) throw new RuntimeException('ایجاد حساب آموزشگاه ناموفق بود.');

            $academyId = DB::table('academies')->insertGetId(['user_id' => $userId]);
            if (!$academyId) throw new RuntimeException('ایجاد آموزشگاه ناموفق بود.');

            $profileId = DB::table('user_profiles')->insertGetId(['user_id' => $userId]);
            $accountId = DB::table('financial_system_accounts')->insertGetId([
                'user_id' => $userId,
                'type' => 'academy_main',
                'balance' => 0,
                'status' => 'active',
            ]);
            $branchId = DB::table('academy_branches')->insertGetId([
                'academy_id' => $academyId,
                'user_id' => $userId,
                'is_main' => 1,
                'timezone' => $data['timezone'] ?? 'Asia/Tehran',
            ]);
            if (!$profileId || !$accountId || !$branchId) {
                throw new RuntimeException('ایجاد اطلاعات پایه آموزشگاه ناموفق بود.');
            }

            $locale = $data['locale'] ?? 'fa';
            $translations = TranslationService::manager();
            foreach ([
                'title' => $data['academy_name'],
                'slogan' => $data['slogan'] ?? '',
                'short_description' => $data['short_description'] ?? '',
                'description' => $data['biography'] ?? '',
            ] as $field => $value) {
                if (!$translations->set('academies', $academyId, $field, $value, $locale)) {
                    throw new RuntimeException('ثبت اطلاعات ترجمه‌شده آموزشگاه ناموفق بود.');
                }
            }

            return $academyId;
        });
    }

    public function all(): array {
        $statement = db()->prepare(<<<SQL
            SELECT
                academies.academy_id AS id,
                academies.user_id,
                users.username,
                users.status,
                COUNT(DISTINCT academy_branches.branch_id) AS branches,
                COUNT(DISTINCT academy_branch_courses.course_id) AS classes,
                COUNT(DISTINCT CASE
                    WHEN academy_branch_course_term_enrollments.type = 'student'
                     AND academy_branch_course_term_enrollments.deleted_at IS NULL
                    THEN academy_branch_course_term_enrollments.member_id
                END) AS students
            FROM academies
            INNER JOIN users ON users.user_id = academies.user_id
            LEFT JOIN academy_branches
                ON academy_branches.academy_id = academies.academy_id
               AND academy_branches.deleted_at IS NULL
            LEFT JOIN academy_branch_courses
                ON academy_branch_courses.branch_id = academy_branches.branch_id
               AND academy_branch_courses.deleted_at IS NULL
            LEFT JOIN academy_branch_course_terms
                ON academy_branch_course_terms.course_id = academy_branch_courses.course_id
               AND academy_branch_course_terms.deleted_at IS NULL
            LEFT JOIN academy_branch_course_term_enrollments
                ON academy_branch_course_term_enrollments.term_id = academy_branch_course_terms.term_id
            WHERE academies.deleted_at IS NULL
              AND users.deleted_at IS NULL
              AND users.type = 'academy'
            GROUP BY academies.academy_id, academies.user_id, users.username, users.status
            ORDER BY academies.academy_id DESC
        SQL);
        $statement->execute();
        $rows = $statement->fetchAll();
        $translations = TranslationService::manager();
        $locale = app()->getLocale();

        return array_map(function (array $row) use ($translations, $locale) {
            $academyId = (int)$row['id'];
            return [
                'id' => $academyId,
                'name' => $translations->get('academies', $academyId, 'title', $locale) ?: $row['username'],
                'slogan' => $translations->get('academies', $academyId, 'slogan', $locale) ?: '',
                'summary' => $translations->get('academies', $academyId, 'short_description', $locale) ?: '',
                'bio' => $translations->get('academies', $academyId, 'description', $locale) ?: '',
                'status' => $row['status'],
                'branches' => (int)$row['branches'],
                'classes' => (int)$row['classes'],
                'students' => (int)$row['students'],
            ];
        }, $rows);
    }
}
