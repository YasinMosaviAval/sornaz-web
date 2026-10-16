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
        $english = $locale === 'en';
        $entity = self::ENTITIES[$table][$english ? 1 : 0] ?? ($english ? 'account activity' : 'فعالیت حساب کاربری');
        if ($table === 'financial_system_discounts' && isset($data['value']) && is_numeric($data['value'])) {
            $value = rtrim(rtrim(number_format((float) $data['value'], 2, '.', ''), '0'), '.');
            $percent = in_array($data['type'] ?? '', ['percent', 'percentage'], true);
            $entity = $english ? ($percent ? $value . '% discount for educational terms' : 'discount of ' . $value . ' for educational terms')
                : ($percent ? 'تخفیف ' . $value . ' درصدی برای ترم‌های آموزشی' : 'تخفیف با مبلغ ' . $value . ' برای ترم‌های آموزشی');
        }
        $verb = ['insert' => ['ایجاد', 'Created'], 'update' => ['ویرایش', 'Updated'], 'delete' => ['حذف', 'Removed']][$operation] ?? ['به‌روزرسانی', 'Updated'];
        $title = $verb[$english ? 1 : 0] . ' ' . $entity;
        $message = $english ? $title . '. You can review the details in the relevant section of your panel.'
            : $title . ' انجام شد. جزئیات را در بخش مربوط در پنل خود بررسی کنید.';
        return ['title' => $title, 'message' => $message];
    }

    public static function legacy(PDO $pdo, array $row, string $title, string $message, string $locale): array
    {
        if (!preg_match('/^(ایجاد|ویرایش|حذف) اطلاعات در |^Data (insert|update|delete) in /u', $title, $match)) {
            return ['title' => $title, 'message' => $message];
        }
        $operation = ['ایجاد' => 'insert', 'ویرایش' => 'update', 'حذف' => 'delete'][$match[1] ?? ''] ?? ($match[2] ?? 'update');
        $table = (string) ($row['related_entity_type'] ?? '');
        // Existing notifications have no event snapshot. Do not substitute a record's current values for its historical values.
        return self::describe($table, $operation, [], $locale);
    }

    public static function action(string $action, string $locale): string
    {
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
