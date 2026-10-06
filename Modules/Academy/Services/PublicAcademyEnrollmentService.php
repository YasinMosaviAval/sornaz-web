<?php

namespace Modules\Academy\Services;

use Core\database\DB;
use RuntimeException;

class PublicAcademyEnrollmentService
{
    public function formData(int $academyId, int $userId, string $locale): array
    {
        $academy = $this->academy($academyId);
        $locale = $locale === 'en' ? 'en' : 'fa';
        $rows = DB::table('academy_branch_course_terms')
            ->join('academy_branch_courses', 'academy_branch_courses.course_id', '=', 'academy_branch_course_terms.course_id')
            ->leftJoin('academy_branches', 'academy_branches.branch_id', '=', 'academy_branch_courses.branch_id')
            ->select('academy_branch_course_terms.*', 'academy_branch_courses.lesson_id', 'academy_branch_courses.branch_id', 'academy_branch_courses.course_id', 'academy_branch_courses.academy_id', 'academy_branches.user_id as branch_user_id')
            ->where('academy_branch_courses.academy_id', $academyId)->where('academy_branch_course_terms.status', 'open')
            ->whereNull('academy_branch_course_terms.deleted_at')->whereNull('academy_branch_courses.deleted_at')->orderBy('academy_branch_course_terms.term_id', 'DESC')->get();
        $eligibleTerms = $this->eligibleTermIds(array_map('intval', array_column($rows, 'term_id')));
        $terms = [];
        foreach ($rows as $row) {
            $termId = (int) $row['term_id'];
            if (!isset($eligibleTerms[$termId])) {
                continue;
            }
            $courseId = (int) $row['course_id'];
            $lessonId = (int) $row['lesson_id'];
            $courseTitle = $this->tr('academy_branch_courses', $courseId, 'title', $locale) ?: $this->tr('lessons', $lessonId, 'title', $locale);
            $termTitle = $this->tr('academy_branch_course_terms', $termId, 'title', $locale);
            $organizationUserId = (int) ($row['branch_user_id'] ?: $academy['user_id']);
            $levelIds = array_values(array_unique(array_map('intval', array_column(DB::table('user_lessons')->select('level_id')->where('user_id', $organizationUserId)->where('lesson_id', $lessonId)->where('status', 'active')->whereNull('deleted_at')->get(), 'level_id'))));
            $levels = [];
            foreach ($levelIds as $levelId) {
                if (!$levelId) {
                    continue;
                }
                $level = DB::table('levels')->where('level_id', $levelId)->where('type', 'learning')->where('is_active', 1)->whereNull('deleted_at')->first();
                if ($level) {
                    $levels[] = ['id' => $levelId, 'title' => $this->tr('levels', $levelId, 'title', $locale) ?: ($locale === 'en' ? 'Level ' . $levelId : 'سطح ' . $levelId)];
                }
            }
            if ($levels) $terms[] = ['id' => $termId, 'title' => trim(($courseTitle ?: ($locale === 'en' ? 'Course ' . $courseId : 'دوره ' . $courseId)) . ($termTitle ? ' — ' . $termTitle : '')), 'levels' => $levels];
        }
        $user = DB::table('users')->where('user_id', $userId)->whereNull('deleted_at')->first();
        $address = DB::table('user_addresses')->where('user_id', $userId)->where('is_main', 1)->whereNull('deleted_at')->first();
        return ['academy' => ['id' => $academyId, 'title' => $this->tr('academies', $academyId, 'title', $locale) ?: ($locale === 'en' ? 'Academy ' . $academyId : 'آموزشگاه ' . $academyId)], 'terms' => $terms, 'needsPhone' => (string) ($user['register_method'] ?? '') !== 'phone', 'profile' => ['name' => $this->tr('users', $userId, 'full_name', $locale) ?: (string) ($user['username'] ?? ''), 'national_id' => (string) ($user['national_code'] ?? ''), 'father_name' => $this->tr('users', $userId, 'father_name', $locale), 'birth_date' => (string) ($user['birthday'] ?? ''), 'phone' => (string) ($user['phone'] ?? ''), 'address' => $address ? $this->tr('user_addresses', (int) $address['address_id'], 'address', $locale) : '']];
    }

    private function eligibleTermIds(array $termIds): array
    {
        if (!$termIds) return [];
        $students = array_fill_keys(array_column(DB::table('academy_branch_course_term_enrollments')->select('term_id')->whereIn('term_id', $termIds)->where('type', 'student')->whereNull('deleted_at')->get(), 'term_id'), true);
        $teachers = array_fill_keys(array_column(DB::table('academy_branch_course_term_enrollments')->select('term_id')->whereIn('term_id', $termIds)->where('type', 'teacher')->where('status', 'active')->whereNull('deleted_at')->get(), 'term_id'), true);
        $sessions = array_fill_keys(array_column(DB::table('academy_branch_course_term_sessions')
            ->join('academy_branch_bookings', 'academy_branch_bookings.booking_id', '=', 'academy_branch_course_term_sessions.booking_id')
            ->select('academy_branch_course_term_sessions.term_id')
            ->whereIn('academy_branch_course_term_sessions.term_id', $termIds)
            ->where('academy_branch_bookings.status', 'approved')
            ->where('academy_branch_bookings.requested_date', '>=', date('Y-m-d'))
            ->whereNull('academy_branch_bookings.deleted_at')
            ->whereNull('academy_branch_course_term_sessions.deleted_at')->get(), 'term_id'), true);
        $drafts = array_fill_keys(array_column(DB::table('translations')->select('table_id')->where('table_name', 'academy_branch_course_terms')->whereIn('table_id', $termIds)->where('field', 'schedule_draft')->whereNull('deleted_at')->get(), 'table_id'), true);
        return array_filter(array_fill_keys($termIds, true), static fn ($id) => !isset($students[$id]) && isset($teachers[$id]) && (isset($sessions[$id]) || isset($drafts[$id])), ARRAY_FILTER_USE_KEY);
    }

    public function joinWaitingList(int $academyId, int $userId, int $termId, int $levelId, string $phone = '', string $note = '', string $locale = 'fa', array $profile = []): array
    {
        return \Modules\System\Services\PaymentMutex::run(db(), 'waiting-join:' . $termId . ':' . $userId, fn () => transaction(function () use ($academyId, $userId, $termId, $levelId, $phone, $note, $locale, $profile) {
            $data = $this->formData($academyId, $userId, $locale);
            $term = null;
            foreach ($data['terms'] as $item) {
                if ($item['id'] === $termId) {
                    $term = $item;
                    break;
                }
            }
            if (!$term) {
                throw new RuntimeException($locale === 'en' ? 'The selected term is no longer available for registration.' : 'ترم انتخاب‌شده دیگر شرایط ثبت‌نام را ندارد. فهرست ترم‌ها را دوباره بررسی کنید.');
            }
            if (!in_array($levelId, array_column($term['levels'], 'id'), true)) {
                throw new RuntimeException($locale === 'en' ? 'The selected level is not available for this term.' : 'سطح انتخاب‌شده برای این ترم ارائه نمی‌شود.');
            }
            $user = DB::table('users')->where('user_id', $userId)->whereNull('deleted_at')->first();
            if (!$user) {
                throw new RuntimeException('User not found.');
            }
            if ($data['needsPhone']) {
                $phone = $this->digits($phone);
                if (!preg_match('/^09\d{9}$/', $phone)) {
                    throw new RuntimeException($locale === 'en' ? 'Enter a valid mobile number.' : 'شماره موبایل معتبر وارد کنید.');
                }
                if (empty($user['phone'])) {
                    DB::table('users')->where('user_id', $userId)->update(['phone' => $phone, 'updated_at' => date('Y-m-d H:i:s'), 'updated_by' => $userId]);
                }
            }
            $row = DB::table('academy_branch_course_terms')->join('academy_branch_courses', 'academy_branch_courses.course_id', '=', 'academy_branch_course_terms.course_id')->where('academy_branch_course_terms.term_id', $termId)->first();
            $branchId = $row['branch_id'] !== null ? (int) $row['branch_id'] : null;
            $now = date('Y-m-d H:i:s');
            $memberQuery = DB::table('academy_branch_members')->where('academy_id', $academyId)->where('user_id', $userId)->whereNull('deleted_at');
            if ($branchId) {
                $memberQuery->where('branch_id', $branchId);
            }
            $member = $memberQuery->first();
            if ($member) {
                $memberId = (int) $member['member_id'];
            } else {
                $memberId = DB::table('academy_branch_members')->insertGetId(['academy_id' => $academyId, 'branch_id' => $branchId, 'user_id' => $userId, 'status' => 'pending', 'joined_at' => date('Y-m-d'), 'created_at' => $now, 'created_by' => $userId, 'updated_at' => $now, 'updated_by' => $userId]);
            }
            $role = DB::table('access_system_roles')->where('type', 'academy')->whereRaw("name LIKE '%student%'")->whereNull('deleted_at')->orderBy('role_id')->first();
            if ($role && !DB::table('academy_branch_member_roles')->where('member_id', $memberId)->where('role_id', (int) $role['role_id'])->whereNull('deleted_at')->first()) {
                DB::table('academy_branch_member_roles')->insert(['member_id' => $memberId, 'role_id' => (int) $role['role_id'], 'is_main' => 1, 'created_at' => $now, 'created_by' => $userId, 'updated_at' => $now, 'updated_by' => $userId]);
            }
            $exists = DB::table('academy_branch_course_term_waiting_list')->where('term_id', $termId)->where('member_id', $memberId)->whereNull('deleted_at')->first();
            if ($exists) {
                throw new RuntimeException($locale === 'en' ? 'You are already on the waiting list for this term.' : 'شما قبلاً در فهرست انتظار این ترم ثبت شده‌اید.');
            }
            $lesson = DB::table('user_lessons')->where('user_id', $userId)->where('lesson_id', (int) $row['lesson_id'])->whereNull('deleted_at')->first();
            $lessonValues = ['level_id' => $levelId, 'status' => 'active', 'updated_at' => $now, 'updated_by' => $userId];
            if ($lesson) {
                DB::table('user_lessons')->where('user_lesson_id', (int) $lesson['user_lesson_id'])->update($lessonValues);
            } else {
                DB::table('user_lessons')->insert(['user_id' => $userId, 'lesson_id' => (int) $row['lesson_id'], 'level_id' => $levelId, 'start_date' => date('Y-m-d'), 'status' => 'active', 'is_primary' => 0, 'created_at' => $now, 'created_by' => $userId, 'updated_at' => $now, 'updated_by' => $userId]);
            }
            $waitingId = DB::table('academy_branch_course_term_waiting_list')->insertGetId(['term_id' => $termId, 'member_id' => $memberId, 'created_at' => $now, 'created_by' => $userId, 'updated_at' => $now, 'updated_by' => $userId]);
            if (trim($note) !== '') {
                DB::table('translations')->insert(['table_name' => 'academy_branch_course_term_waiting_list', 'table_id' => $waitingId, 'field' => 'description', 'locale' => $locale === 'en' ? 'en' : 'fa', 'value' => trim($note), 'created_at' => $now, 'created_by' => $userId, 'updated_at' => $now, 'updated_by' => $userId]);
            }
            if (trim((string) ($profile['name'] ?? '')) === '' || trim((string) ($profile['national_id'] ?? '')) === '' || trim((string) ($profile['father_name'] ?? '')) === '' || trim((string) ($profile['birth_date'] ?? '')) === '' || trim((string) ($profile['address'] ?? '')) === '') {
                throw new RuntimeException('اطلاعات هویتی و آدرس هنرجو را کامل کنید.');
            }
            (new \Modules\Analytics\Services\AdminDashboardService())->saveApplicantProfile($userId, array_merge($profile, ['phone' => $this->digits($phone), 'note' => $note]));
            $conversationId = $this->createRegistrationConversation($academyId, $branchId, $userId, $termId, $waitingId, $now);
            $academy = DB::table('academies')->where('academy_id', $academyId)->whereNull('deleted_at')->first();
            $branch = $branchId ? DB::table('academy_branches')->where('branch_id', $branchId)->whereNull('deleted_at')->first() : null;
            $recipients = array_values(array_unique(array_filter(array_map(fn ($m) => (int) $m['user_id'], DB::table('conversation_members')->where('conversation_id', $conversationId)->whereNull('deleted_at')->get()))));
            foreach ($recipients as $recipientId) {
                if ($recipientId === $userId) {
                    continue;
                }
                $messageId = DB::table('user_messages')->insertGetId([
                    'sender_id' => $userId, 'receiver_user_id' => $recipientId, 'type' => 'notification',
                    'status' => 'published', 'related_entity_type' => 'academy_term_waiting_list',
                    'related_entity_id' => $waitingId, 'is_read' => 0,
                    'created_at' => $now, 'created_by' => $userId, 'updated_at' => $now, 'updated_by' => $userId,
                ]);
                foreach (['fa' => ['ثبت‌نام جدید در فهرست انتظار', 'هنرجوی جدید برای ترم #' . $termId . ' در فهرست انتظار قرار گرفت.'], 'en' => ['New waiting-list registration', 'A student joined the waiting list for term #' . $termId . '.']] as $language => $texts) {
                    foreach (['title' => $texts[0], 'message' => $texts[1]] as $field => $value) {
                        DB::table('translations')->insert(['table_name' => 'user_messages', 'table_id' => $messageId, 'field' => $field, 'locale' => $language, 'value' => $value, 'created_at' => $now, 'created_by' => $userId, 'updated_at' => $now, 'updated_by' => $userId]);
                    }
                }
            }
            return ['id' => $waitingId, 'conversationId' => $conversationId];
        }));
    }

    private function createRegistrationConversation(int $academyId, ?int $branchId, int $applicantId, int $termId, int $waitingId, string $now): int
    {
        $academy = DB::table('academies')->where('academy_id', $academyId)->whereNull('deleted_at')->first();
        $branch = $branchId ? DB::table('academy_branches')->where('branch_id', $branchId)->whereNull('deleted_at')->first() : null;
        $members = [$applicantId, (int) ($academy['user_id'] ?? 0), (int) ($branch['user_id'] ?? 0)];
        foreach (DB::table('academy_branch_members')->where('academy_id', $academyId)->where('status', 'active')->whereNull('deleted_at')->get() as $member) {
            $memberBranch = (int) ($member['branch_id'] ?? 0);
            $roles = DB::table('academy_branch_member_roles')->join('access_system_roles', 'access_system_roles.role_id', '=', 'academy_branch_member_roles.role_id')->where('academy_branch_member_roles.member_id', (int) $member['member_id'])->whereNull('academy_branch_member_roles.deleted_at')->whereNull('access_system_roles.deleted_at')->get();
            foreach ($roles as $role) {
                $name = strtolower((string) $role['name']);
                if (!in_array($name, ['academy_owner', 'academy_manager', 'academy_receptionist', 'branch_owner', 'branch_manager', 'branch_receptionist', 'academy_branch_owner', 'academy_branch_manager', 'academy_branch_receptionist'], true)) {
                    continue;
                }
                if ((str_starts_with($name, 'branch_') || str_starts_with($name, 'academy_branch_')) && (!$branchId || $memberBranch !== $branchId)) {
                    continue;
                }
                $members[] = (int) $member['user_id'];
                break;
            }
        }
        $members = array_values(array_unique(array_filter($members)));
        $conversationId = (int) DB::table('conversations')->insertGetId(['type' => 'group', 'title' => 'ثبت‌نام ترم #' . $termId . ' · درخواست #' . $waitingId, 'created_at' => $now, 'created_by' => $applicantId, 'updated_at' => $now, 'updated_by' => $applicantId]);
        foreach ($members as $memberId) {
            DB::table('conversation_members')->insert(['conversation_id' => $conversationId, 'user_id' => $memberId, 'role' => $memberId === $applicantId ? 'member' : 'admin', 'is_muted' => 0, 'joined_at' => $now, 'created_at' => $now, 'created_by' => $applicantId, 'updated_at' => $now, 'updated_by' => $applicantId]);
        }
        $messageId = (int) DB::table('conversation_messages')->insertGetId(['conversation_id' => $conversationId, 'sender_id' => $applicantId, 'body' => 'درخواست ثبت‌نام برای ترم #' . $termId . ' ثبت شد. لطفاً ساعت و مدرس را در همین گفتگو هماهنگ کنید.', 'created_at' => $now, 'created_by' => $applicantId, 'updated_at' => $now, 'updated_by' => $applicantId]);
        DB::table('conversations')->where('conversation_id', $conversationId)->update(['last_message_id' => $messageId]);
        DB::table('translations')->insert(['table_name' => 'academy_branch_course_term_waiting_list', 'table_id' => $waitingId, 'field' => 'conversation_id', 'locale' => 'fa', 'value' => (string) $conversationId, 'created_at' => $now, 'created_by' => $applicantId, 'updated_at' => $now, 'updated_by' => $applicantId]);
        return $conversationId;
    }

    public function ensureConversation(int $waitingId): int
    {
        return \Modules\System\Services\PaymentMutex::run(db(), 'waiting-chat:' . $waitingId, fn () => transaction(function () use ($waitingId) {
            $link = DB::table('translations')->where('table_name', 'academy_branch_course_term_waiting_list')->where('table_id', $waitingId)->where('field', 'conversation_id')->whereNull('deleted_at')->orderBy('translation_id', 'DESC')->first();
            if ($link && DB::table('conversations')->where('conversation_id', (int) $link['value'])->whereNull('deleted_at')->first()) {
                return (int) $link['value'];
            }
            $waiting = DB::table('academy_branch_course_term_waiting_list')->where('term_waiting_list_id', $waitingId)->whereNull('deleted_at')->first();
            $member = $waiting ? DB::table('academy_branch_members')->where('member_id', (int) $waiting['member_id'])->whereNull('deleted_at')->first() : null;
            $term = $waiting ? DB::table('academy_branch_course_terms')->where('term_id', (int) $waiting['term_id'])->whereNull('deleted_at')->first() : null;
            $course = $term ? DB::table('academy_branch_courses')->where('course_id', (int) $term['course_id'])->whereNull('deleted_at')->first() : null;
            if (!$member || !$course || (int) $member['academy_id'] !== (int) $course['academy_id']) {
                throw new RuntimeException('درخواست معتبر نیست.');
            }
            return $this->createRegistrationConversation((int) $course['academy_id'], $course['branch_id'] !== null ? (int) $course['branch_id'] : null, (int) $member['user_id'], (int) $waiting['term_id'], $waitingId, date('Y-m-d H:i:s'));
        }));
    }

    private function academy(int $id): array
    {
        $row = DB::table('academies')->where('academy_id', $id)->whereNull('deleted_at')->first();
        if (!$row) {
            throw new RuntimeException('Academy not found.');
        }
        return $row;
    }

    private function tr(string $table, int $id, string $field, string $locale): string
    {
        $r = DB::table('translations')->where('table_name', $table)->where('table_id', $id)->where('field', $field)->where('locale', $locale)->whereNull('deleted_at')->orderBy('translation_id', 'DESC')->first();
        return (string) ($r['value'] ?? '');
    }

    private function digits(string $v): string
    {
        return preg_replace('/\D+/', '', strtr($v, ['۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9']));
    }
}
