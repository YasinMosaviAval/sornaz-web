<?php

namespace Core\database;

use PDO;

/** Public activity descriptions: never expose SQL identifiers or changed field names. */
final class ActivityText
{
    private const ENTITIES = [
        'financial_system_discounts' => ['تخفیف برای ترم‌های آموزشی', 'discount for educational terms'],
        'academies' => ['آموزشگاه', 'academy'], 'academy_branches' => ['شعبه', 'branch'],
        'lessons' => ['درس', 'lesson'], 'user_lessons' => ['درس و سطح آموزشی', 'lesson and learning level'],
        'levels' => ['سطح آموزشی', 'learning level'], 'instruments' => ['ساز', 'instrument'],
        'academy_branch_courses' => ['دوره آموزشی', 'course'], 'academy_branch_course_terms' => ['ترم آموزشی', 'educational term'],
        'academy_branch_classrooms' => ['کلاس', 'classroom'], 'academy_branch_classroom_types' => ['نوع کلاس', 'classroom type'],
        'academy_branch_classroom_equipment' => ['تجهیز کلاس', 'classroom equipment'],
        'academy_branch_members' => ['عضویت در آموزشگاه یا شعبه', 'academy or branch membership'],
        'academy_branch_member_contracts' => ['قرارداد کارمند', 'staff contract'],
        'academy_branch_member_roles' => ['نقش کارمند', 'staff role'],
        'academy_branch_member_permissions' => ['دسترسی کارمند', 'staff permission'],
        'academy_branch_classroom_assets' => ['تجهیز کلاس', 'classroom equipment'],
        'academy_documents' => ['مدرک آموزشگاه', 'academy document'],
        'academy_branch_bookings' => ['زمان برگزاری کلاس', 'class booking'],
        'academy_branch_course_term_sessions' => ['جلسه آموزشی', 'class session'],
        'academy_branch_course_term_session_attendances' => ['حضور و غیاب جلسه', 'session attendance'],
        'academy_branch_course_term_enrollments' => ['ثبت‌نام هنرجو در ترم', 'term enrollment'],
        'academy_branch_course_term_waiting_list' => ['درخواست ثبت‌نام ترم', 'term enrollment request'],
        'z_user_profiles' => ['پروفایل کاربر', 'user profile'], 'z_user_settings' => ['تنظیمات حساب', 'account settings'],
        'user_awards' => ['افتخار کاربر', 'user award'], 'user_badges' => ['نشان کاربر', 'user badge'],
        'user_certificates' => ['گواهی کاربر', 'user certificate'], 'user_educations' => ['سابقه تحصیلی', 'education record'],
        'user_experiences' => ['سابقه کاری', 'work experience'], 'user_publications' => ['اثر منتشرشده', 'publication'],
        'user_events' => ['رویداد', 'event'], 'user_polls' => ['نظرسنجی', 'poll'],
        'user_poll_options' => ['گزینه نظرسنجی', 'poll option'], 'user_poll_votes' => ['رأی نظرسنجی', 'poll vote'],
        'creator_courses' => ['دوره آنلاین', 'online course'], 'creator_course_orders' => ['ثبت‌نام دوره آنلاین', 'online course enrollment'],
        'financial_system_ledger_entries' => ['سند مالی', 'financial ledger entry'], 'financial_system_refunds' => ['بازپرداخت وجه', 'refund'],
        'academy_subscription_periods' => ['اشتراک آموزشگاه', 'academy subscription'], 'academy_subscription_payments' => ['پرداخت اشتراک آموزشگاه', 'academy subscription payment'],
        'f_settings' => ['تنظیمات سایت', 'site settings'],
        'media_files' => ['رسانه حساب کاربری', 'account media'],
        'classroom_types' => ['نوع کلاس', 'classroom type'],
        'user_sessions' => ['ورود به سایت', 'site sign-in'],
        'tracking_user_sessions' => ['نشست بازدید سایت', 'site browsing session'],
        'tracking_user_page_views' => ['بازدید صفحه سایت', 'site page visit'],
        'tracking_user_events' => ['تعامل با سایت', 'site interaction'],
        'tracking_user_activity_intervals' => ['زمان حضور در سایت', 'time spent on the site'],
        'tracking_user_content_engagements' => ['مطالعه محتوای سایت', 'site content viewed'],
        'tracking_user_consents' => ['تنظیمات رضایت حریم خصوصی', 'privacy consent settings'],
        'auth_remember_tokens' => ['ورود ماندگار به حساب', 'remembered sign-in'],
        'user_contacts' => ['اطلاعات تماس', 'contact details'],
        'academy_branch_scheduling_rules' => ['قانون زمان‌بندی', 'scheduling rule'],
        'academy_branch_course_term_invoices' => ['صورتحساب ترم', 'term invoice'],
        'academy_branch_course_term_invoice_installments' => ['قسط شهریه ترم', 'term tuition installment'],
        'academy_branch_types' => ['نوع آموزشی شعبه', 'branch educational type'],
        'user_referrals' => ['دعوت کاربر', 'user invitation'],
        'social_profiles' => ['پروفایل اجتماعی', 'social profile'],
        'social_follows' => ['دنبال‌کردن کاربر', 'following a user'],
        'social_comments' => ['دیدگاه', 'comment'],
        'social_posts' => ['پست', 'post'],
        'posts' => ['محتوای سایت', 'site content'],
        'settings' => ['تنظیمات سایت', 'site settings'],
        'users' => ['حساب کاربری', 'user account'], 'user_roles' => ['نقش کاربر', 'user role'],
        'user_permissions' => ['دسترسی کاربر', 'user permission'], 'user_instruments' => ['ساز کاربر', 'user instrument'],
        'user_availabilities' => ['زمان‌های در دسترس', 'availability'],
        'access_system_roles' => ['نقش', 'role'], 'access_system_permissions' => ['دسترسی', 'permission'],
        'user_points' => ['امتیاز کاربر', 'user points'], 'user_point_rules' => ['نوع امتیاز', 'point rule'],
        'financial_system_accounts' => ['حساب مالی', 'financial account'], 'financial_system_invoices' => ['صورتحساب', 'invoice'],
        'financial_system_transactions' => ['تراکنش مالی', 'financial transaction'], 'financial_system_payments' => ['پرداخت', 'payment'],
        'financial_system_installments' => ['قسط', 'installment'], 'financial_system_currency' => ['واحد پول', 'currency'],
        'user_files' => ['رسانه گالری', 'gallery media'], 'files' => ['فایل', 'file'],
        'user_addresses' => ['نشانی کاربر', 'user address'], 'user_achievements' => ['دستاورد کاربر', 'user achievement'],
        'user_profiles' => ['پروفایل کاربر', 'user profile'], 'user_settings' => ['تنظیمات حساب', 'account settings'],
    ];

    public static function describe(string $table, string $operation, array $data = [], string $locale = 'fa'): array
    {
        $table = class_exists(TableNames::class) ? TableNames::logical($table) : $table;
        $english = $locale === 'en';
        $entity = self::ENTITIES[$table][$english ? 1 : 0] ?? ($english ? 'account activity' : 'فعالیت حساب کاربری');
        if ($table === 'financial_system_discounts' && isset($data['value']) && is_numeric($data['value'])) {
            $value = rtrim(rtrim(number_format((float) $data['value'], 2, '.', ''), '0'), '.');
            $percent = in_array($data['type'] ?? '', ['percent', 'percentage'], true);
            $entity = $english ? ($percent ? $value . '% discount for educational terms' : 'discount of ' . $value . ' for educational terms')
                : ($percent ? 'تخفیف ' . $value . ' درصدی برای ترم‌های آموزشی' : 'تخفیف با مبلغ ' . $value . ' برای ترم‌های آموزشی');
        }
        if ($table === 'users' && in_array($data['type'] ?? '', ['branch', 'academy'], true)) {
            $labels = ['branch' => ['حساب کاربری شعبه', 'branch account'], 'academy' => ['حساب کاربری آموزشگاه', 'academy account']];
            $entity = $labels[$data['type']][$english ? 1 : 0];
        }
        if ($table === 'users' && array_key_exists('avatar_file_id', $data)) $entity = $english ? 'profile photo' : 'عکس پروفایل';
        if (in_array($table, ['media_files', 'user_files'], true)) {
            $labels = ['avatar' => ['عکس پروفایل', 'profile photo'], 'cover' => ['عکس کاور', 'cover photo'], 'logo' => ['لوگو', 'logo'], 'intro_video' => ['ویدیوی معرفی', 'introduction video'], 'gallery' => ['رسانه گالری', 'gallery media'], 'teacher_gallery' => ['رسانه گالری استاد', 'teacher gallery media'], 'document' => ['مدرک پیوست‌شده', 'attached document']];
            $entity = $labels[$data['collection'] ?? ''][$english ? 1 : 0] ?? $entity;
        }
        if ($table === 'user_sessions' && $operation === 'insert') {
            return ['title' => $english ? 'Signed in to the site' : 'ورود به سایت', 'message' => $english ? 'A sign-in to your account was recorded.' : 'ورود به حساب کاربری شما ثبت شد.'];
        }
        $verb = ['insert' => ['ایجاد', 'Created'], 'update' => ['ویرایش', 'Updated'], 'delete' => ['حذف', 'Removed']][$operation] ?? ['به‌روزرسانی', 'Updated'];
        $title = $verb[$english ? 1 : 0] . ' ' . $entity;
        $message = $english ? $title . '. You can review the details in the relevant section of your panel.'
            : $title . ' انجام شد. جزئیات را در بخش مربوط در پنل خود بررسی کنید.';
        return ['title' => $title, 'message' => $message];
    }

    public static function legacy(PDO $pdo, array $row, string $title, string $message, string $locale, bool $technical = false): array
    {
        $table = (string) ($row['related_entity_type'] ?? '');
        $table = class_exists(TableNames::class) ? TableNames::logical($table) : $table;
        $operation = NotificationMetadata::operation((string) ($row['event_operation'] ?? ''), $table, $title);
        $old = preg_match('/^(ایجاد|ویرایش|حذف) اطلاعات در |^Data (insert|update|delete) in /u', $title);
        $vague = str_contains($title, 'فعالیت حساب کاربری') || str_contains(strtolower($title), 'account activity');
        if (!$operation || $table === 'admin_notification' || (!isset(self::ENTITIES[$table]) && !$old && !$vague)) return ['title' => $title, 'message' => $message];
        // Use only the historical event text, never the current record's values.
        $context = [];
        if (preg_match('/\bcollection\b["\s:=]+(avatar|cover|logo|intro_video|gallery|teacher_gallery|document)\b/', $message, $match)) $context['collection'] = $match[1];
        if ($table === 'users' && preg_match('/\bavatar_file_id\b/', $message)) $context['avatar_file_id'] = null;
        $public = self::describe($table, $operation, $context, $locale);
        if (!$old && !$vague && !$context && !str_contains($title, $table) && !preg_match('/\b[a-z]+_[a-z_]+\b/', $title)) $public['title'] = $title;
        $public['message'] = $locale === 'en' ? $public['title'] . '. You can review this change in the relevant section of your panel.' : $public['title'] . ' انجام شد. این تغییر را می‌توانید در بخش مربوط در پنل خود بررسی کنید.';
        $date = strtotime((string) ($row['created_at'] ?? ''));
        if ($date !== false) $public['message'] = ($locale === 'en' ? 'On ' : 'در تاریخ ') . date('Y/m/d H:i', $date) . ($locale === 'en' ? ': ' : '، ') . $public['message'];
        if ($technical && $message !== '' && $message !== $public['message']) $public['message'] .= "\n\n" . ($locale === 'en' ? 'Original details:' : 'جزئیات اولیه:') . "\n" . $message;
        return $public;
    }
    public static function point(array $row, string $locale): string
    {
        $metadata = json_decode((string) ($row['metadata'] ?? ''), true);
        $snapshot = $metadata['activity'][$locale]['title'] ?? null;
        if (is_string($snapshot) && $snapshot !== '') return $snapshot;
        return self::action((string) ($row['action'] ?? ''), $locale);
    }
    public static function action(string $action, string $locale): string
    {
        if (preg_match('/^(create|edit|update|delete|remove):([a-z_]+)$/D', $action, $legacy)) {
            $action = $legacy[2] . '.' . (['create' => 'insert', 'edit' => 'update', 'remove' => 'delete'][$legacy[1]] ?? $legacy[1]);
        }
        if (preg_match('/^([a-z_]+)\.(insert|update|delete)$/D', $action, $parts)) {
            return self::describe($parts[1], $parts[2], [], $locale)['title'];
        }
        $labels = ['page_view' => ['بازدید صفحه', 'Page visit'], 'form_submit' => ['ارسال فرم', 'Form submission'],
            'search' => ['جست‌وجو در سایت', 'Site search'], 'content_engagement' => ['مطالعه محتوای آموزشی', 'Learning content viewed'],
            'login' => ['ورود به حساب', 'Sign in'], 'register' => ['ثبت‌نام', 'Registration'],
            'manual' => ['اعطای امتیاز', 'Points awarded'], 'click' => ['تعامل با صفحه', 'Page interaction']];
        return $labels[$action][$locale === 'en' ? 1 : 0] ?? ($locale === 'en' ? 'Account activity' : 'فعالیت حساب کاربری');
    }
}
