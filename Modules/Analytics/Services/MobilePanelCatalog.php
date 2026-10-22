<?php
namespace Modules\Analytics\Services;

use Core\router\Router;

/** Declarative data contracts for native Material forms, not HTML templates. */
final class MobilePanelCatalog
{
    private function f(string $key, string $fa, string $type = 'text', mixed $options = null, bool $required = false): array
    {
        return array_filter(['key' => $key, 'label' => $fa, 'type' => $type, 'options' => $options, 'required' => $required], fn ($v) => $v !== null);
    }

    private function a(string $path, string $label, string $method = 'POST', array $fields = [], bool $row = true): array
    {
        return compact('path', 'label', 'method', 'fields', 'row');
    }

    private function s(string $key, string $label, string $en, string $path, string $rows, array $fields = [], string $access = 'management', bool $crud = true): array
    {
        $actions = ['list' => $this->a($path, 'نمایش', 'GET', [], false)];
        if ($crud) {
            $actions += ['create' => $this->a($path, 'افزودن', 'POST', $fields, false), 'update' => $this->a($path . '/{id}/update', 'ویرایش', 'POST', $fields), 'delete' => $this->a($path . '/{id}/delete', 'حذف')];
        }
        return compact('key', 'label', 'en', 'rows', 'fields', 'access', 'actions');
    }

    public function sections(bool $webDialogs = false): array
    {
        $f = fn (...$args) => $this->f(...$args);
        $a = fn (...$args) => $this->a(...$args);
        $s = fn (...$args) => $this->s(...$args);
        $description = [$f('summary', 'خلاصه', 'multiline'), $f('description', 'توضیحات', 'multiline')];
        $organization = $f('organizationUserId', 'آموزشگاه یا شعبه', 'select', ['source' => 'organizations', 'id' => 'user_id']);
        $status = $f('status', 'وضعیت', 'select', ['pending' => 'در انتظار تأیید', 'active' => 'فعال', 'inactive' => 'غیرفعال']);
        $addressFields = [$f('address', 'نشانی', 'multiline'), $f('postal_code', 'کد پستی'), $f('city_id', 'شهر', 'select', ['source' => 'catalog.cities']), $f('is_main', 'نشانی اصلی', 'bool')];
        $sections = [];
        $sections['dashboard'] = $s('dashboard', 'داشبورد', 'Dashboard', '/analytics/admin-dashboard', '', [], 'common', false);
        foreach (['my-courses' => ['دوره‌های من', 'My courses', 'courses'], 'my-terms' => ['ترم‌های من', 'My terms', 'terms'], 'my-classrooms' => ['کلاس‌های من', 'My classes', 'classes']] as $key => $meta) {
            $sections[$key] = $s($key, $meta[0], $meta[1], '/analytics/admin-learning', $meta[2], [], 'common', false);
        }
        $sections['account'] = $s('account', 'حساب کاربری', 'Account', '/analytics/admin-account', '', [], 'common', false);
        $sections['account']['actions'] += [
            'invite' => $a('/system/my-invite', 'لینک دعوت من', 'GET', [], false) + ['hidden' => true],
            'profile' => $a('/analytics/admin-account/profile', 'ویرایش مشخصات', 'POST', [$f('name', 'نام', 'text', null, true), $f('email', 'ایمیل', 'email'), $f('phone', 'شماره تماس', 'phone'), $f('founded', 'تاریخ تأسیس', 'date'), $f('address', 'نشانی', 'multiline')], false),
            'bio' => $a('/analytics/admin-account/bio', 'معرفی', 'POST', [$f('shortIntro', 'معرفی کوتاه', 'multiline'), $f('biography', 'درباره من', 'multiline')], false),
            'privacy' => $a('/analytics/admin-account/privacy', 'حریم خصوصی', 'POST', [$f('showPublicProfile', 'نمایش عمومی پروفایل', 'bool')], false),
            'security' => $a('/analytics/admin-account/security', 'تغییر رمز عبور', 'POST', [$f('password', 'رمز عبور جدید', 'password', null, true), $f('passwordConfirmation', 'تکرار رمز عبور', 'password', null, true)], false),
            'avatar' => $a('/analytics/admin-account/media/avatar', 'عکس پروفایل', 'POST', [$f('file', 'تصویر', 'file'), $f('title', 'عنوان', 'text', null, true)], false),
            'cover' => $a('/analytics/admin-account/media/cover', 'تصویر کاور', 'POST', [$f('file', 'تصویر', 'file'), $f('title', 'عنوان', 'text', null, true)], false),
            'document' => $a('/analytics/admin-account/media/document', 'افزودن سند', 'POST', [$f('file', 'فایل', 'file'), $f('title', 'عنوان'), $f('document_type', 'نوع سند'), $f('document_number', 'شماره سند'), $f('issued_at', 'تاریخ صدور', 'date'), $f('expires_at', 'تاریخ انقضا', 'date')], false),
            'backup' => $a('/analytics/admin-account/backups', 'پشتیبان‌گیری', 'POST', [], false) + ['access' => 'founder'],
            'merge' => $a('/analytics/admin-account/merges', 'درخواست ادغام حساب', 'POST', [$f('targetUserId', 'کاربر مقصد', 'number'), $f('reason', 'دلیل', 'multiline')], false),
            'end-session' => $a('/analytics/admin-account/sessions/{id}/end', 'پایان نشست'),
            'delete-media' => $a('/analytics/admin-account/media/{id}/delete', 'حذف فایل'),
            'download-media' => $a('/analytics/admin-account/media/{id}/download', 'دانلود فایل', 'GET') + ['download' => true],
            'download-backup' => $a('/analytics/admin-account/backups/{id}/download', 'دانلود پشتیبان', 'GET') + ['download' => true, 'access' => 'founder'],
            'cancel-merge' => $a('/analytics/admin-account/merges/{id}/cancel', 'لغو ادغام'),
            'decide-merge' => $a('/analytics/admin-account/merges/{id}/decision', 'بررسی ادغام', 'POST', [$f('decision', 'نتیجه', 'select', ['approved' => 'تأیید', 'rejected' => 'رد']), $f('reason', 'توضیح', 'multiline')]) + ['admin' => true],
        ];
        $sections['chat'] = $s('chat', 'گفتگوها', 'Chats', '/analytics/chat', 'conversations', [$f('type', 'نوع گفتگو', 'select', ['direct' => 'خصوصی', 'group' => 'گروه']), $f('title', 'نام گروه'), $f('userIds', 'اعضا', 'multi', ['source' => 'people'])], 'common', false);
        $sections['chat']['actions'] += [
            'create' => $a('/analytics/chat', 'گفتگوی جدید', 'POST', $sections['chat']['fields'], false),
            'messages' => $a('/analytics/chat/{id}/messages', 'پیام‌ها', 'GET'),
            'details' => $a('/analytics/chat/{id}/details', 'اطلاعات گفتگو', 'GET'),
            'send' => $a('/analytics/chat/{id}/messages', 'ارسال پیام', 'POST', [$f('body', 'پیام', 'multiline'), $f('file', 'پیوست', 'file')]),
            'rename' => $a('/analytics/chat/{id}/rename', 'تغییر نام', 'POST', [$f('title', 'نام گفتگو')]),
            'avatar' => $a('/analytics/chat/{id}/avatar', 'تصویر گفتگو', 'POST', [$f('file', 'تصویر', 'file')]),
            'members' => $a('/analytics/chat/{id}/members', 'افزودن عضو', 'POST', [$f('userIds', 'اعضا', 'multi', ['source' => 'people'])]),
            'remove-member' => $a('/analytics/chat/{id}/members/{userId}/remove', 'حذف عضو'),
            'leave' => $a('/analytics/chat/{id}/leave', 'ترک گفتگو'), 'delete' => $a('/analytics/chat/{id}/delete', 'حذف گفتگو'),
            'like' => $a('/analytics/chat/messages/{id}/like', 'پسندیدن پیام'),
            'edit-message' => $a('/analytics/chat/messages/{id}/edit', 'ویرایش پیام', 'POST', [$f('body', 'متن پیام', 'multiline')]),
            'delete-message' => $a('/analytics/chat/messages/{id}/delete', 'حذف پیام'),
            'forward' => $a('/analytics/chat/messages/{id}/forward', 'ارسال به گفتگو', 'POST', [$f('conversationIds', 'گفتگوها', 'multi', ['source' => 'conversations'])]),
            'file' => $a('/analytics/chat/messages/{id}/file', 'دانلود پیوست', 'GET') + ['download' => true],
        ];
        $sections['messages'] = $s('messages', 'پیام‌ها', 'Messages', '/analytics/admin-messages', 'messages', [$f('receiverId', 'گیرنده', 'select', ['source' => 'recipients']), $f('title', 'عنوان', 'text', null, true), $f('body', 'متن', 'multiline', null, true)], 'common');
        $sections['messages']['actions']['create']['access'] = 'member';
        $sections['notifications'] = $s('notifications', 'اعلان‌ها', 'Notifications', '/analytics/admin-notifications', 'items', [$f('title', 'عنوان', 'text', null, true), $f('body', 'متن', 'multiline', null, true), $f('audience', 'مخاطبان'), $f('asDraft', 'پیش‌نویس', 'bool')], 'common');
        $sections['notifications']['actions']['create']['access'] = 'management';
        foreach (['messages', 'notifications'] as $key) {
            foreach (['read' => 'خوانده‌شده', 'unread' => 'خوانده‌نشده'] as $op => $label) {
                $sections[$key]['actions'][$op] = $a('/analytics/admin-' . $key . '/{id}/' . $op, $label);
            }
        }
        $sections['branches'] = $s('branches', 'شعبه‌ها', 'Branches', '/academy/admin/branches', 'branches', [
            $f('academy_id', 'آموزشگاه', 'select', ['source' => 'catalog.academies']), $f('name', 'نام شعبه', 'text', null, true), $f('username', 'نام کاربری'), $f('email', 'ایمیل', 'email'), $f('password', 'رمز عبور', 'password'), $f('password2', 'تکرار رمز', 'password'), $f('type_id', 'نوع شعبه', 'select', ['source' => 'catalog.types']), $f('physical_type', 'نوع فعالیت', 'select', ['physical' => 'حضوری', 'virtual' => 'مجازی', 'hybrid' => 'ترکیبی']), $f('short_description', 'معرفی کوتاه', 'multiline'), $f('slogan', 'شعار'), $f('bio', 'درباره شعبه', 'multiline'), $f('addresses', 'نشانی‌ها', 'rows', $addressFields), $status]);
        $sections['branch-types'] = $s('branch-types', 'انواع شعبه', 'Branch types', '/academy/admin/branches', 'catalog.types', [$f('title', 'عنوان', 'text', null, true), $description[1]], 'admin', false);
        $sections['branch-types']['actions'] += ['create' => $a('/academy/admin/branches/types', 'افزودن نوع', 'POST', $sections['branch-types']['fields'], false), 'update' => $a('/academy/admin/branch-types/{id}/update', 'ویرایش', 'POST', $sections['branch-types']['fields']), 'delete' => $a('/academy/admin/branch-types/{id}/delete', 'حذف')];
        $memberFields = [$organization, $f('name', 'نام و نام خانوادگی', 'text', null, true), $f('phone', 'شماره تماس', 'phone'), $f('nationalId', 'کد ملی'), $f('gender', 'جنسیت', 'select', ['male' => 'مرد', 'female' => 'زن']), $f('birthDate', 'تاریخ تولد', 'date'), $f('roleId', 'نقش', 'select', ['source' => 'staff_catalog.roles']), $f('startDate', 'شروع همکاری', 'date'), $f('endDate', 'پایان همکاری', 'date'), $f('profileVisibility', 'نمایش پروفایل', 'select', ['public' => 'عمومی', 'private' => 'خصوصی']), $f('lessonId', 'ساز یا درس', 'select', ['source' => 'staff_catalog.lessons']), $f('levelId', 'سطح', 'select', ['source' => 'staff_catalog.levels']), $f('price', 'مبلغ قرارداد', 'number'), $f('currencyId', 'واحد پول', 'select', ['source' => 'staff_catalog.currencies']), $f('contractTitle', 'عنوان قرارداد'), $f('contractDescription', 'شرح قرارداد', 'multiline')];
        $sections['members'] = $s('members', 'اساتید و کارکنان', 'Teachers and staff', '/academy/admin/members', 'members', $memberFields);
        $sections['members']['actions']['list']['path'] = '/academy/admin/members/data';
        $sections['students'] = $s('students', 'هنرجویان', 'Students', '/academy/admin/courses/student-options', '', [$organization, $f('name', 'نام و نام خانوادگی', 'text', null, true), $f('phone', 'شماره تماس', 'phone'), $f('nationalId', 'کد ملی'), $f('birthDate', 'تاریخ تولد', 'date'), $f('termId', 'ترم', 'select', ['source' => 'terms']), $f('registrationDate', 'تاریخ ثبت‌نام', 'date'), $f('fatherName', 'نام پدر'), $f('parentName', 'نام سرپرست'), $f('parentPhone', 'تماس سرپرست', 'phone'), $f('parentNationalId', 'کد ملی سرپرست'), $f('parentBirthDate', 'تولد سرپرست', 'date'), $f('addresses', 'نشانی‌ها', 'rows', $addressFields)], 'management', false);
        $sections['students']['actions']['create'] = $a('/academy/admin/students', 'ثبت‌نام هنرجو', 'POST', $sections['students']['fields'], false);
        $sections['classrooms'] = $s('classrooms', 'کلاس‌ها', 'Classrooms', '/academy/admin/classrooms', 'classrooms', [$f('branchId', 'شعبه', 'select', ['source' => 'branches']), $f('typeId', 'نوع کلاس', 'select', ['source' => 'types']), $f('name', 'نام کلاس', 'text', null, true), $f('capacity', 'ظرفیت', 'number'), $f('equipment', 'تجهیزات', 'multiline'), ...$description, $status]);
        $sections['classroom-types'] = $s('classroom-types', 'انواع کلاس', 'Classroom types', '/academy/admin/classroom-types', 'types', [$f('title', 'عنوان', 'text', null, true), $f('type', 'دسته'), $f('capacity', 'ظرفیت', 'number'), ...$description]);
        $sections['classroom-types']['actions']['list']['path'] = '/academy/admin/classrooms';
        $sections['courses'] = $s('courses', 'دوره‌ها', 'Courses', '/academy/admin/courses', 'courses', [$organization, $f('lesson_id', 'ساز یا درس', 'select', ['source' => 'lessons']), $f('name', 'نام دوره', 'text', null, true), $f('teacher_capacity', 'ظرفیت مدرس', 'number'), $f('student_capacity', 'ظرفیت هنرجو', 'number'), $f('status', 'وضعیت', 'select', ['pending' => 'در انتظار', 'open' => 'باز', 'ongoing' => 'در حال برگزاری', 'finished' => 'پایان یافته']), ...$description]);
        $sections['course-levels'] = $s('course-levels', 'سطوح دوره', 'Course levels', '/academy/admin/course-levels', 'levels', [$f('title', 'عنوان', 'text', null, true), $f('sort_order', 'ترتیب', 'number'), ...$description]);
        $sections['course-levels']['actions']['list']['path'] = '/academy/admin/courses';
        $sessionFields = [$f('date', 'تاریخ', 'date'), $f('startTime', 'شروع', 'time'), $f('endTime', 'پایان', 'time'), $f('classroomId', 'کلاس', 'select', ['source' => 'classrooms'])];
        $sections['terms'] = $s('terms', 'ترم‌ها', 'Terms', '/academy/admin/terms', 'terms', [$f('courseId', 'دوره', 'select', ['source' => 'courses']), $f('name', 'نام ترم', 'text', null, true), $f('classroomId', 'کلاس', 'select', ['source' => 'classrooms']), $f('teachers', 'مدرسان', 'multi', ['source' => 'teachers']), $f('students', 'هنرجویان', 'multi', ['source' => 'students']), $f('sessions', 'جلسه‌ها', 'rows', $sessionFields), $f('repeatType', 'تکرار', 'select', ['none' => 'بدون تکرار', 'weekly' => 'هفتگی']), $f('cost', 'شهریه', 'number'), $f('currencyId', 'واحد پول', 'select', ['source' => 'currencies']), $f('installmentCount', 'تعداد اقساط', 'number'), $f('discountId', 'تخفیف', 'select', ['source' => 'discounts']), ...$description, $status]);
        foreach (['cancel' => 'درخواست لغو جلسه', 'cancel/approve' => 'تأیید لغو جلسه', 'cancel/reject' => 'رد لغو جلسه', 'restore' => 'بازگرداندن جلسه'] as $op => $label) {
            $sections['terms']['actions'][str_replace('/', '-', $op)] = $a('/academy/admin/terms/{id}/sessions/{sessionId}/' . $op, $label, 'POST', [$f('reason', 'دلیل', 'multiline'), $f('makeupDate', 'تاریخ جبرانی', 'date'), $f('startTime', 'شروع', 'time'), $f('endTime', 'پایان', 'time')]);
        }
        $sections['finance'] = $s('finance', 'امور مالی و اقساط', 'Finance', '/academy/admin/term-invoices', 'invoices', [$f('statusCode', 'وضعیت'), $f('dueDate', 'سررسید', 'date'), $f('title', 'عنوان'), $f('amount', 'مبلغ', 'number'), ...$description], 'management', false);
        $sections['finance']['actions'] += ['update' => $a('/academy/admin/term-invoices/{id}/update', 'ویرایش صورتحساب', 'POST', $sections['finance']['fields']), 'pay' => $a('/academy/admin/term-invoices/{invoiceId}/installments/{installmentId}/zarinpal', 'پرداخت آنلاین'), 'offline' => $a('/academy/admin/term-invoices/{invoiceId}/installments/{installmentId}/offline', 'ثبت پرداخت', 'POST', [$f('amount', 'مبلغ', 'number'), $f('reference', 'شماره پیگیری'), $f('description', 'توضیح', 'multiline')])];
        $sections['subscriptions'] = $s('subscriptions', 'اشتراک آموزشگاه', 'Subscriptions', '/academy/admin/subscriptions', 'subscriptions', [], 'management', false);
        $sections['subscriptions']['actions']['pay'] = $a('/academy/admin/subscriptions/{id}/zarinpal', 'پرداخت اشتراک');
        $sections['schedules'] = $s('schedules', 'برنامه کلاس‌ها و حضور و غیاب', 'Class schedule', '/academy/admin/class-schedules', 'sessions', [], 'member', false);
        $sections['schedules']['actions']['attendance'] = $a('/academy/admin/class-schedules/{id}/attendance', 'حضور و غیاب', 'POST', [$f('students', 'هنرجویان', 'rows', [$f('id', 'هنرجو', 'select', ['source' => 'students']), $f('status', 'وضعیت', 'select', ['present' => 'حاضر', 'absent' => 'غایب', 'late' => 'تأخیر']), $f('note', 'یادداشت')])]);
        $ranges = [$f('start', 'شروع', 'time'), $f('end', 'پایان', 'time'), $f('status', 'وضعیت', 'select', ['فعال' => 'فعال', 'غیرفعال' => 'غیرفعال'])];
        $sections['member-schedules'] = $s('member-schedules', 'زمان‌های حضور اعضا', 'Member availability', '/academy/admin/member-schedules', 'schedules', [$f('membershipId', 'عضو', 'select', ['source' => 'members']), $f('day', 'روز هفته', 'select', ['0' => 'شنبه', '1' => 'یکشنبه', '2' => 'دوشنبه', '3' => 'سه‌شنبه', '4' => 'چهارشنبه', '5' => 'پنجشنبه', '6' => 'جمعه']), $f('repeatDate', 'تاریخ', 'date'), $f('repeatPeriod', 'تکرار', 'select', ['none' => 'بدون تکرار', 'weekly' => 'هفتگی']), $f('timezone', 'منطقه زمانی', 'select', ['source' => 'staff_catalog.timezones']), $f('ranges', 'بازه‌های زمانی', 'rows', $ranges), ...$description]);
        $sections['availability-exceptions'] = $s('availability-exceptions', 'استثناهای حضور', 'Availability exceptions', '/academy/admin/availability-exceptions', 'exceptions', [$organization, $f('membershipId', 'عضو', 'select', ['source' => 'members']), $f('targetType', 'مربوط به', 'select', ['member' => 'عضو', 'organization' => 'سازمان']), $f('date', 'تاریخ', 'date'), $f('allDay', 'تمام روز', 'bool'), $f('type', 'نوع استثنا'), $f('ranges', 'بازه‌های زمانی', 'rows', $ranges), ...$description]);
        $sections['scheduling-rules'] = $s('scheduling-rules', 'قوانین زمان‌بندی', 'Scheduling rules', '/analytics/admin-scheduling-rules', 'rules', [$f('organizationKey', 'سازمان', 'select', ['source' => 'organizations', 'id' => 'key']), $f('title', 'عنوان'), $f('value', 'مقدار', 'number'), $f('type', 'نوع قانون'), $f('valueUnit', 'واحد'), $status, ...$description]);
        $sections['lessons'] = $s('lessons', 'سازها و درس‌ها', 'Instruments and lessons', '/academy/admin/branch-offerings/lessons', 'lessons', [$f('organization_user_id', 'آموزشگاه یا شعبه', 'select', ['source' => 'organizations', 'id' => 'user_id']), $f('lesson_id', 'ساز یا درس', 'select', ['source' => 'lessons_catalog']), $f('level_id', 'سطح', 'select', ['source' => 'levels']), $f('is_primary', 'درس اصلی', 'bool'), $f('start_date', 'شروع', 'date'), $status, ...$description]);
        $sections['lessons']['actions']['list']['path'] = '/academy/admin/branch-offerings';
        $sections['lessons']['actions']['delete']['path'] = '/academy/admin/branch-offerings/lessons/{id}/delete';
        $sections['gallery'] = $s('gallery', 'گالری', 'Gallery', '/analytics/admin-gallery', 'items', [$f('file', 'فایل', 'file'), $f('title', 'عنوان', 'text', null, true), $f('ownerId', 'مالک', 'select', ['source' => 'owners']), $f('collection', 'مجموعه', 'select', ['cover' => 'کاور', 'logo' => 'لوگو', 'intro_video' => 'ویدیوی معرفی', 'gallery' => 'گالری']), ...$description]);
        $sections['points'] = $s('points', 'امتیازها', 'Points', '/analytics/admin-points', 'items', [$f('title', 'عنوان'), $f('action', 'عملیات'), $f('points', 'امتیاز', 'number'), $f('dailyCap', 'سقف روزانه', 'number'), $f('cooldownMinutes', 'فاصله زمانی', 'number'), $f('branchId', 'شعبه', 'select', ['source' => 'branches']), $status]);
        $sections['users'] = $s('users', 'کاربران و دسترسی‌ها', 'Users and access', '/analytics/admin-user-access', 'users', [$f('roleIds', 'نقش‌ها', 'multi', ['source' => 'roles']), $f('permissionIds', 'مجوزها', 'multi', ['source' => 'permissions'])], 'management', false);
        $sections['users']['actions']['update'] = $a('/analytics/admin-user-access/{id}', 'ویرایش دسترسی', 'POST', $sections['users']['fields']);
        foreach (['awards' => ['پاداش‌ها و جوایز', 'Awards', 'title issuer date description'], 'certificates' => ['گواهی‌ها', 'Certificates', 'title issuer issue_date expire_date certificate_url file_path description'], 'experiences' => ['سوابق کاری', 'Experience', 'title organization start_date end_date address_id description'], 'educations' => ['تحصیلات', 'Education', 'degree institution field start_date end_date description'], 'events' => ['رویدادها', 'Events', 'title event_date event_type address_id description'], 'publications' => ['آثار منتشرشده', 'Publications', 'title publisher published_date url content is_peer_reviewed'], 'badges' => ['نشان‌ها', 'Badges', 'verification_level_id granted_at expires_at status'], 'polls' => ['نظرسنجی‌ها', 'Polls', 'question description expires_at type is_anonymous status options']] as $key => $meta) {
            $fields = [$f('user_id', 'سازمان', 'select', ['source' => 'organizations'])];
            foreach (explode(' ', $meta[2]) as $field) {
                $fields[] = $this->profileField($field);
            }
            $sections[$key] = $s($key, $meta[0], $meta[1], '/analytics/admin-profile-content/' . $key, 'items', $fields);
            $sections[$key]['actions']['update']['path'] = '/analytics/admin-profile-content/' . $key . '/{id}';
            $sections[$key]['actions']['status'] = $a('/analytics/admin-profile-content/' . $key . '/{id}/status', 'تغییر وضعیت تأیید');
        }
        $sections['polls']['actions']['vote'] = $a('/analytics/admin-profile-content/polls/{id}/vote', 'رأی دادن', 'POST', [$f('options', 'گزینه‌ها', 'multi', ['source' => 'options'])]);
        $sections['posts'] = $s('posts', 'نوشته‌ها', 'Posts', '/analytics/admin-posts', 'items', [$f('title', 'عنوان', 'text', null, true), $f('slug', 'نامک'), $f('type', 'نوع نوشته'), $f('status', 'وضعیت', 'select', ['draft' => 'پیش‌نویس', 'published' => 'منتشرشده', 'pending' => 'در انتظار']), $f('visibility', 'نمایش', 'select', ['public' => 'عمومی', 'private' => 'خصوصی']), $f('published_at', 'زمان انتشار', 'date'), $f('categories', 'دسته‌ها', 'multi', ['source' => 'categories']), $f('cover', 'تصویر', 'file'), ...$description, $f('content', 'متن نوشته', 'multiline')]);
        foreach (['trash' => 'انتقال به زباله‌دان', 'restore' => 'بازیابی'] as $op => $label) {
            $sections['posts']['actions'][$op] = $a('/analytics/admin-posts/{id}/' . $op, $label);
        }
        $sections['post-categories'] = $s('post-categories', 'دسته‌بندی نوشته‌ها', 'Post categories', '/analytics/admin-post-categories', 'items', [$f('title_fa', 'عنوان فارسی'), $f('title_en', 'عنوان انگلیسی'), $f('slug', 'نامک'), $f('group', 'گروه')]);
        $sections['comments'] = $s('comments', 'دیدگاه‌ها', 'Comments', '/analytics/admin-comments', 'items', [$f('content', 'متن دیدگاه', 'multiline'), $status]);
        $sections['comments']['actions']['reply'] = $a('/analytics/admin-comments/{id}/reply', 'پاسخ', 'POST', [$f('content', 'متن پاسخ', 'multiline')]);
        $sections['media'] = $s('media', 'رسانه‌ها', 'Media', '/analytics/admin-media', 'items', [$f('file', 'فایل', 'file'), $f('title', 'عنوان'), ...$description]);
        $sections['media']['actions']['create']['path'] = '/analytics/admin-media/upload';
        $sections['tracking'] = $s('tracking', 'گزارش فعالیت‌ها', 'Activity reports', '/analytics/admin-tracking', '', [], 'admin', false);
        $sections['national-holidays'] = $s('national-holidays', 'تعطیلات رسمی', 'National holidays', '/academy/admin/national-holidays', 'holidays', [$f('title', 'عنوان'), $f('date', 'تاریخ', 'date'), $f('is_closed', 'تعطیل', 'bool'), ...$description], 'admin');
        foreach (['roles' => ['نقش‌ها', 'Roles'], 'permissions' => ['مجوزها', 'Permissions']] as $key => $meta) {
            $fields = [$f('name', 'نام', 'text', null, true), $f('translations', 'عنوان‌ها', 'object', [$f('fa', 'فارسی'), $f('en', 'انگلیسی')]), $f('scope', 'محدوده'), $f('type', 'نوع'), $f('group', 'گروه')];
            if ($key === 'roles') {
                $fields = [...$fields, $f('level', 'سطح', 'number'), $f('parentId', 'نقش بالاتر', 'select', ['source' => 'roles']), $f('color', 'رنگ'), $f('sortOrder', 'ترتیب', 'number'), $f('permissionIds', 'مجوزها', 'multi', ['source' => 'permissions'])];
            } else {
                $fields = [...$fields, $f('resource', 'بخش'), $f('action', 'عملیات'), $f('risk', 'درجه حساسیت'), $f('approval', 'نیاز به تأیید', 'bool')];
            }
            $sections[$key] = $s($key, $meta[0], $meta[1], '/analytics/admin-' . $key, $key, $fields, 'admin');
            $sections[$key]['actions']['list']['path'] = '/analytics/admin-access-catalog';
            $sections[$key]['actions']['update']['path'] = '/analytics/admin-' . $key . '/{id}';
        }
        array_unshift($sections['terms']['fields'], $f('branchId', 'شعبه', 'select', ['source' => 'branches'], true));
        foreach ($sections['terms']['fields'] as &$field) {
            if ($field['key'] === 'repeatType') {
                $field['options'] = ['no-period' => 'بدون تکرار', 'week' => 'هفتگی', '2-week' => 'دو هفته', '3-week' => 'سه هفته', '4-week' => 'چهار هفته', 'month' => 'ماهانه', 'year' => 'سالانه'];
            }
            if ($field['key'] === 'status') {
                $field['options'] = ['pending' => 'در انتظار', 'open' => 'باز', 'ongoing' => 'در حال برگزاری', 'finished' => 'پایان یافته'];
            }
        } unset($field);
        foreach (['create', 'update'] as $op) {
            $sections['terms']['actions'][$op]['fields'] = $sections['terms']['fields'];
        }
        $sections['terms']['actions']['discounts'] = $a('/academy/admin/term-discounts', 'لیست تخفیف‌ها', 'GET', [], false);
        $sections['terms']['actions']['discount'] = $a('/academy/admin/term-discounts', 'افزودن تخفیف', 'POST', [$f('title', 'عنوان', 'text', null, true), $f('type', 'نوع', 'select', ['percentage' => 'درصدی', 'fixed' => 'مبلغ ثابت']), $f('value', 'مقدار', 'number', null, true)], false);
        $sections['schedules']['rows'] = 'schedules';
        $sections['schedules']['actions']['attendance']['fields'] = [$f('teacherNote', 'یادداشت استاد', 'multiline') + ['en' => 'Teacher note', 'visibleWhen' => ['canWriteTeacherNote' => true], 'maxLength' => 10000], $f('attendance', 'حضور و غیاب', 'rows', [$f('memberId', 'عضو', 'select', ['source' => 'attendance']), $f('status', 'وضعیت', 'select', ['present' => 'حاضر', 'absent' => 'غایب', 'late' => 'تأخیر', 'leave' => 'مرخصی', 'excused_absence' => 'غیبت موجه', 'online' => 'آنلاین']), $f('description', 'توضیح')])];
        $sections['settings'] = $s('settings', 'تنظیمات سایت', 'Site settings', '/analytics/site-settings', '', [], 'management', false);
        $sections['settings']['actions']['save'] = $a('/analytics/admin-settings', 'ویرایش تنظیمات', 'POST', [$f('primaryFont', 'قلم'), $f('fontScale', 'اندازه متن', 'number'), $f('language', 'زبان', 'select', ['fa' => 'فارسی', 'en' => 'English']), $f('themeMode', 'ظاهر', 'select', ['dark' => 'تیره', 'light' => 'روشن']), $f('colorTheme', 'تم رنگی', 'select', ['indigo' => 'نیلی', 'emerald' => 'زمردی', 'rose' => 'رز', 'amber' => 'کهربایی'])], false);
        $sections['reports'] = $s('reports', 'گزارش‌ها و نمودارها', 'Reports and charts', '/analytics/admin-reports', 'reports', [], 'management', false);
        $sections['guides'] = $s('guides', 'راهنمای عملکردها', 'Guides', '/analytics/admin-guides', 'items', [], 'founder', false);
        $guideFields = [$f('fa', 'فارسی', 'object', [$f('title', 'عنوان'), $f('content', 'متن', 'multiline')]), $f('en', 'English', 'object', [$f('title', 'Title'), $f('content', 'Content', 'multiline')])];
        $sections['guides']['actions']['update'] = $a('/analytics/admin-guides', 'ویرایش راهنما', 'POST', $guideFields);
        $sections['pages'] = $s('pages', 'برگه‌ها', 'Pages', '/analytics/admin-site-pages', 'pages', [], 'admin', false);
        $sections['page-content'] = $s('page-content', 'محتوای برگه', 'Page content', '/analytics/site-page-content', 'items', [], 'admin', false);
        $sections['page-content']['hidden'] = true;
        $sections['page-content']['actions']['update'] = $a('/analytics/admin-site-page-content', 'ویرایش محتوا', 'POST', [$f('fa', 'متن فارسی', 'multiline'), $f('en', 'English text', 'multiline'), $f('value', 'تصویر یا آیکن')]);
        $sections['notifications']['rows'] = 'notifications';
        foreach (['publish' => 'انتشار', 'expire' => 'پایان انتشار'] as $op => $label) {
            $sections['notifications']['actions'][$op] = $a('/analytics/admin-notifications/{id}/' . $op, $label);
            $sections['notifications']['actions'][$op]['access'] = 'management';
        }
        $sections['students']['actions']['list']['path'] = '/academy/admin/members/data';
        $sections['students']['rows'] = 'members';
        $sections['students']['where'] = ['type' => 'student'];
        $sections['members']['exclude'] = ['type' => 'student'];
        $sections['students']['optionActions'] = ['options'];
        $sections['students']['actions']['options'] = $a('/academy/admin/courses/student-options', 'گزینه‌های ثبت‌نام', 'GET', [], false) + ['hidden' => true];
        $sections['students']['actions']['update'] = $a('/academy/admin/members/{id}/update', 'ویرایش هنرجو', 'POST', $sections['students']['fields']);
        $sections['students']['actions']['delete'] = $a('/academy/admin/members/{id}/delete', 'حذف هنرجو');
        $sections['students']['actions']['status'] = $a('/academy/admin/members/{id}/status', 'تغییر وضعیت');
        $sections['branch-types']['rows'] = 'types';
        foreach ($sections as &$section) {
            foreach ($section['actions'] as &$action) {
                foreach ($action['fields'] as &$field) {
                    if (($field['options']['source'] ?? '') === 'catalog.academies') {
                        $field['options']['source'] = 'academies';
                    }
                    if (($field['options']['source'] ?? '') === 'catalog.types') {
                        $field['options']['source'] = 'types';
                    }
                    if ($section['key'] === 'members' && $field['key'] === 'organizationUserId') {
                        $field['options']['source'] = 'staff_catalog.organizations';
                    }
                    if (($field['options']['source'] ?? '') === 'staff_catalog.lessons') {
                        $field['options']['id'] = 'lesson_id';
                    }
                    if ($section['key'] === 'gallery' && $field['key'] === 'ownerId') {
                        $field['options']['id'] = 'userId';
                    }
                    if ($section['key'] === 'classrooms' && $field['key'] === 'typeId') {
                        $field['options']['source'] = 'classroomTypes';
                    }
                    if ($section['key'] === 'classrooms' && $field['key'] === 'equipment') {
                        $field['type'] = 'rows';
                        $field['options'] = [$f('name', 'نام وسیله'), $f('qty', 'تعداد', 'number')];
                    }
                    if ($section['key'] === 'users' && $field['key'] === 'roleIds') {
                        $field['initial'] = 'roles';
                    }
                    if ($section['key'] === 'users' && $field['key'] === 'permissionIds') {
                        $field['initial'] = 'permissions';
                    }
                    if ($section['key'] === 'notifications' && $field['key'] === 'audience') {
                        $field['type'] = 'select';
                        $field['options'] = array_combine(['همه', 'هنرجویان', 'اساتید', 'والدین', 'پرسنل'], ['همه', 'هنرجویان', 'اساتید', 'والدین', 'پرسنل']);
                    }
                }
            }
        } unset($section,$action,$field);
        foreach ($sections['chat']['actions'] as &$action) {
            foreach ($action['fields'] as &$field) {
                if (($field['options']['source'] ?? '') === 'people') {
                    $field['options']['source'] = 'users';
                }
            }
        }
        unset($action,$field);
        $sections['account']['actions']['merge']['fields'] = [$f('userId', 'شناسه کاربر', 'number', null, true), $f('memberId', 'شماره عضویت', 'number', null, true), $f('reason', 'دلیل درخواست', 'multiline')];
        foreach ($sections['account']['actions']['decide-merge']['fields'] as &$field) {
            if ($field['key'] === 'reason') {
                $field['key'] = 'note';
            }
        }
        unset($field);
        $sections['finance']['actions']['offline']['fields'] = [$f('method', 'روش پرداخت', 'select', ['card_to_card' => 'کارت به کارت', 'pos' => 'کارت‌خوان'], true), $f('reference', 'شماره پیگیری', 'text', null, true), $f('payerName', 'نام پرداخت‌کننده', 'text', null, true), $f('bankCardType', 'بانک', 'select', ['melli' => 'ملی', 'sepah' => 'سپه', 'mellat' => 'ملت', 'tejarat' => 'تجارت', 'saderat' => 'صادرات', 'refah' => 'رفاه', 'keshavarzi' => 'کشاورزی', 'maskan' => 'مسکن', 'post' => 'پست بانک', 'tosee_saderat' => 'توسعه صادرات', 'sanat_madan' => 'صنعت و معدن', 'eghtesad_novin' => 'اقتصاد نوین', 'parsian' => 'پارسیان', 'pasargad' => 'پاسارگاد', 'saman' => 'سامان', 'sarmayeh' => 'سرمایه', 'sina' => 'سینا', 'shahr' => 'شهر', 'ayandeh' => 'آینده', 'gardeshgari' => 'گردشگری', 'iran_zamin' => 'ایران زمین', 'resalat' => 'رسالت', 'melal' => 'ملل', 'karafarin' => 'کارآفرین', 'dey' => 'دی', 'middle_east' => 'خاورمیانه', 'other' => 'سایر'], true), $f('paymentDate', 'روز پرداخت', 'date', null, true), $f('paymentTime', 'ساعت پرداخت', 'time', null, true), $f('description', 'توضیحات', 'multiline')];
        $sections['posts']['rows'] = 'posts';
        $sections['comments']['rows'] = 'comments';
        foreach (['create', 'update'] as $operation) {
            foreach ($sections['posts']['actions'][$operation]['fields'] as &$field) {
                if ($field['key'] === 'categories' || $field['key'] === 'cover') {
                    $field['type'] = 'text';
                    unset($field['options']);
                }
            }
        }unset($field);
        foreach (['create', 'update'] as $operation) {
            foreach ($sections['terms']['actions'][$operation]['fields'] as &$field) {
                if (in_array($field['key'], ['teachers', 'students'], true)) {
                    $field['options'] = ['source' => 'members', 'where' => ['type' => $field['key'] === 'teachers' ? 'teacher' : 'student'], 'match' => ['branchId' => 'branchId']];
                }
                if (in_array($field['key'], ['courseId', 'classroomId'], true)) {
                    $field['options']['match'] = ['branchId' => 'branchId'];
                }
            }
        }unset($field);
        $categoryFields = [$f('value', 'نام سیستمی انگلیسی', 'text', null, true), $f('fa', 'عنوان فارسی', 'text', null, true), $f('en', 'عنوان انگلیسی', 'text', null, true)];
        $sections['classroom-categories'] = $s('classroom-categories', 'دسته‌بندی انواع کلاس', 'Classroom categories', '/academy/admin/classrooms', 'typeOptions', [], 'management', false);
        $sections['classroom-categories']['actions'] += ['create' => $a('/academy/admin/classroom-type-categories', 'افزودن دسته', 'POST', $categoryFields, false), 'update' => $a('/academy/admin/classroom-type-categories/{value}/update', 'ویرایش دسته', 'POST', array_slice($categoryFields, 1)), 'delete' => $a('/academy/admin/classroom-type-categories/{value}/delete', 'حذف دسته')];
        foreach (['create', 'update'] as $op) {
            foreach ($sections['classroom-types']['actions'][$op]['fields'] as &$field) {
                if ($field['key'] === 'type') {
                    $field['type'] = 'select';
                    $field['options'] = ['source' => 'typeOptions', 'id' => 'value'];
                }
            }
        }unset($field);
        foreach (['create', 'update'] as $op) {
            foreach ($sections['polls']['actions'][$op]['fields'] as &$field) {
                if ($field['key'] === 'type') {
                    $field['type'] = 'select';
                    $field['options'] = ['single' => 'تک‌گزینه‌ای', 'multiple' => 'چندگزینه‌ای'];
                }
                if ($field['key'] === 'status') {
                    $field['type'] = 'select';
                    $field['options'] = ['active' => 'فعال', 'deactive' => 'غیرفعال', 'closed' => 'بسته'];
                }
            }
        }unset($field);
        $sections['dashboard']['filters'] = [$f('branchId', 'شعبه', 'select', ['source' => 'branches'])];
        $sections['reports']['filters'] = $sections['dashboard']['filters'];
        $sections['schedules']['filters'] = [$f('branch', 'شعبه', 'select', ['source' => 'branches']), $f('classroom', 'کلاس', 'select', ['source' => 'classrooms']), $f('lesson', 'درس', 'select', ['source' => 'lessons']), $f('date', 'تاریخ', 'date'), $f('timeFrom', 'از ساعت', 'time'), $f('timeTo', 'تا ساعت', 'time'), $f('status', 'وضعیت', 'select', ['approved' => 'تأییدشده', 'pending' => 'در انتظار', 'cancelled' => 'لغوشده']), $f('mode', 'نوع برگزاری', 'select', ['in-person' => 'حضوری', 'online' => 'آنلاین'])];
        $sections['posts']['filters'] = [$f('status', 'وضعیت', 'select', ['draft' => 'پیش‌نویس', 'published' => 'منتشرشده', 'pending' => 'در انتظار', 'trash' => 'زباله‌دان']), $f('type', 'نوع', 'select', ['post' => 'نوشته', 'page' => 'برگه', 'article' => 'مقاله']), $f('visibility', 'نمایش', 'select', ['public' => 'عمومی', 'private' => 'خصوصی'])];
        $sections['comments']['filters'] = [$f('post', 'نوشته', 'select', ['source' => 'posts']), $f('status', 'وضعیت', 'select', ['pending' => 'در انتظار', 'approved' => 'تأییدشده', 'rejected' => 'ردشده'])];
        foreach ($sections['comments']['actions']['update']['fields'] as &$field) {
            if ($field['key'] === 'status') {
                $field['options'] = ['pending' => 'در انتظار', 'approved' => 'تأییدشده', 'rejected' => 'ردشده'];
            }
        }
        unset($field);
        $sections['points']['rows'] = 'rules';
        $nativeAddressFields = [$f('province', 'استان', 'select', ['source' => 'provinces', 'id' => 'province_name', 'label' => 'province_name']), $f('city', 'شهر', 'select', ['source' => 'counties', 'id' => 'county_name', 'label' => 'county_name']), $f('address', 'نشانی', 'multiline'), $f('postal_code', 'کد پستی'), $f('is_main', 'نشانی اصلی', 'bool')];
        foreach (['create', 'update'] as $op) {
            $sections['members']['actions'][$op]['fields'][] = $f('type', 'نوع قرارداد', 'select', ['teacher' => 'مدرس', 'receptionist' => 'پذیرش', 'manager' => 'مدیر', 'other' => 'سایر'], true);
            $sections['branches']['actions'][$op]['fields'][] = $f('phone', 'شماره همراه', 'phone');
            $sections['branches']['actions'][$op]['fields'][] = $f('phones', 'تلفن‌های تماس', 'rows', [$f('number', 'شماره تلفن', 'phone'), $f('is_main', 'اصلی', 'bool')]);
            $sections['branches']['actions'][$op]['fields'][] = $f('links', 'پیوندها', 'rows', [$f('title', 'عنوان'), $f('url', 'نشانی'), $f('mode', 'نوع', 'select', ['email' => 'ایمیل', 'social' => 'شبکه اجتماعی']), $f('platform', 'شبکه اجتماعی'), $f('is_main', 'اصلی', 'bool')]);
            foreach ($sections['branches']['actions'][$op]['fields'] as &$field) {
                if ($field['key'] === 'physical_type') {
                    $field['options'] = ['physical' => 'حضوری', 'online' => 'آنلاین', 'hybrid' => 'ترکیبی'];
                }
            }
            unset($field);
            foreach (['branches', 'students'] as $entity) {
                foreach ($sections[$entity]['actions'][$op]['fields'] as &$field) {
                    if ($field['key'] === 'addresses') {
                        $field['options'] = $nativeAddressFields;
                    }
                }
            }
            unset($field);
        }
        $days = array_combine(['شنبه', 'یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه'], ['شنبه', 'یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه']);
        $repeats = array_combine(['هفتگی', 'دو هفته', 'سه هفته', 'چهار هفته', 'ماهانه', 'سالانه', 'بی‌تکرار'], ['هفتگی', 'دو هفته', 'سه هفته', 'چهار هفته', 'ماهانه', 'سالانه', 'بی‌تکرار']);
        $scheduleFields = [$organization, $f('day', 'روز هفته', 'select', $days), $f('repeatDate', 'تاریخ شروع', 'date'), $f('repeatPeriod', 'تکرار', 'select', $repeats), $f('timezone', 'منطقه زمانی'), $f('ranges', 'بازه‌های زمانی', 'rows', $ranges), ...$description];
        $sections['availabilities'] = $s('availabilities', 'برنامه زمانی سازمان', 'Organization availability', '/academy/admin/branch-offerings/schedules', 'schedules', $scheduleFields);
        $sections['availabilities']['actions']['list']['path'] = '/academy/admin/branch-offerings';
        foreach (['create', 'update'] as $operation) {
            foreach ($sections['member-schedules']['actions'][$operation]['fields'] as &$field) {
                if ($field['key'] === 'day') {
                    $field['options'] = $days;
                }
                if ($field['key'] === 'repeatPeriod') {
                    $field['options'] = $repeats;
                }
                if ($field['key'] === 'timezone') {
                    $field['type'] = 'text';
                    unset($field['options']);
                }
            }unset($field);
            foreach ($sections['availability-exceptions']['actions'][$operation]['fields'] as &$field) {
                if ($field['key'] === 'organizationUserId') {
                    $field['options']['source'] = 'staff_catalog.organizations';
                }
                if ($field['key'] === 'allDay') {
                    $field['initial'] = 'fullDay';
                }
                if ($field['key'] === 'type') {
                    $field['type'] = 'select';
                    $field['options'] = ['holiday' => 'تعطیلی', 'closed' => 'بسته', 'unavailable' => 'عدم حضور', 'busy' => 'مشغول', 'vacation' => 'مرخصی', 'blocked' => 'مسدود'];
                }
            }unset($field);
            foreach (['roles', 'permissions'] as $entity) {
                foreach ($sections[$entity]['actions'][$operation]['fields'] as &$field) {
                    if ($field['key'] === 'translations') {
                        $field['options'] = [$f('fa', 'فارسی', 'object', [$f('title', 'عنوان', 'text', null, true), $f('summary', 'خلاصه', 'multiline', null, true), $f('description', 'توضیحات', 'multiline', null, true)]), $f('en', 'English', 'object', [$f('title', 'Title', 'text', null, true), $f('summary', 'Summary', 'multiline', null, true), $f('description', 'Description', 'multiline', null, true)])];
                    }
                    if ($field['key'] === 'scope') {
                        $field['type'] = 'select';
                        $field['options'] = ['platform' => 'سامانه', 'website' => 'وب‌سایت', 'academy' => 'آموزشگاه', 'branch' => 'شعبه', 'self' => 'خود کاربر'];
                    }
                    if ($field['key'] === 'type') {
                        $field['type'] = 'select';
                        $field['options'] = $entity === 'roles' ? ['system' => 'سیستمی', 'academy' => 'آموزشگاه', 'other' => 'سایر'] : ['menu' => 'منو', 'sidebar' => 'منوی کناری', 'topbar' => 'نوار بالا', 'header' => 'سربرگ'];
                    }
                    if ($field['key'] === 'approval') {
                        $field['type'] = 'select';
                        $field['options'] = ['confirm' => 'تأییدشده', 'pending' => 'در انتظار'];
                    }
                }
            }unset($field);
        }
        $sections['account']['actions']['privacy']['fields'] = [...$sections['account']['actions']['privacy']['fields'], $f('showBranches', 'نمایش شعبه‌ها', 'bool'), $f('showTeachers', 'نمایش مدرسان', 'bool'), $f('showContact', 'نمایش اطلاعات تماس', 'bool'), $f('showStats', 'نمایش آمار', 'bool'), $f('indexable', 'نمایش در موتورهای جستجو', 'bool')];
        $contactFields = [$f('field', 'نوع تماس', 'select', ['email' => 'ایمیل', 'phone' => 'شماره تماس'], true), $f('destination', 'نشانی جدید', 'text', null, true)];
        $sections['account']['actions']['contact-send'] = $a('/analytics/mobile-contact/send', 'ارسال کد تغییر تماس', 'POST', $contactFields, false);
        $sections['account']['actions']['contact-verify'] = $a('/analytics/mobile-contact/verify', 'تأیید کد تغییر تماس', 'POST', [...$contactFields, $f('code', 'کد تأیید', 'text', null, true)], false);
        $sections['account']['actions']['contact-commit'] = $a('/analytics/mobile-contact/commit', 'ثبت نشانی تماس', 'POST', $contactFields, false);
        $sections['account']['actions']['backup']['access'] = 'founder';
        $sections['account']['actions']['download-backup']['access'] = 'founder';
        foreach ($sections['account']['actions']['document']['fields'] as &$field) {
            $field['key'] = ['document_type' => 'documentType', 'document_number' => 'documentNumber', 'issued_at' => 'issuedAt', 'expires_at' => 'expiresAt'][$field['key']] ?? $field['key'];
            if ($field['key'] === 'documentType') {
                $field['type'] = 'select';
                $field['options'] = ['license' => 'مجوز', 'identity' => 'هویتی', 'statute' => 'اساسنامه', 'tax' => 'مالیاتی', 'contract' => 'قرارداد', 'certificate' => 'گواهی', 'other' => 'سایر'];
            }
        }unset($field);
        foreach ($sections['settings']['actions']['save']['fields'] as &$field) {
            if ($field['key'] === 'primaryFont') {
                $field['type'] = 'select';
                $field['options'] = ['source' => 'fonts', 'id' => 'value'];
            }
            if ($field['key'] === 'fontScale') {
                $field['type'] = 'select';
                $field['options'] = ['-2' => 'کوچک‌تر', '-1' => 'کوچک', '0' => 'عادی', '1' => 'بزرگ', '2' => 'بزرگ‌تر'];
            }
        }unset($field);
        $sections['my-points'] = $s('my-points', 'امتیازهای من', 'My points', '/analytics/admin-points', 'recent', [], 'common', false);
        $sections['my-settings'] = $s('my-settings', 'تنظیمات من', 'My settings', '/analytics/personal-data/settings', '', [], 'common', false);
        $sections['my-settings']['actions']['save'] = $a('/analytics/personal-data/settings', 'ذخیره تنظیمات شخصی', 'POST', [
            $f('language', 'زبان', 'select', ['fa' => 'فارسی', 'en' => 'English']),
            $f('themeMode', 'ظاهر', 'select', ['light' => 'روشن', 'dark' => 'تیره']),
            $f('colorTheme', 'تم رنگی', 'select', ['indigo' => 'نیلی', 'emerald' => 'زمردی', 'rose' => 'رز', 'amber' => 'کهربایی']),
            $f('primaryFont', 'قلم', 'select', ['vazir' => 'وزیر', 'sahel' => 'ساحل', 'iran_yekan' => 'ایران‌یکان', 'iran_sansx' => 'ایران‌سنس X', 'kalameh' => 'کلمه', 'peyda' => 'پیدا']),
            $f('fontScale', 'اندازه قلم', 'number'), $f('fontWeight', 'وزن قلم', 'number'), $f('cornerRadius', 'گردی گوشه‌ها', 'number'),
        ], false);
        $galleryFields = [$f('file', 'فایل', 'file'), $f('collection', 'مجموعه', 'select', ['cover' => 'کاور', 'logo' => 'لوگو', 'intro_video' => 'ویدیو معرفی', 'gallery' => 'گالری']), $f('title', 'عنوان'), ...$description];
        $sections['my-gallery'] = $s('my-gallery', 'گالری من', 'My gallery', '/analytics/personal-data/gallery', 'items', $galleryFields, 'common');
        $lessonFields = [$f('lesson_id', 'درس', 'select', ['source' => 'lessons', 'id' => 'lesson_id']), $f('level_id', 'سطح', 'select', ['source' => 'levels', 'id' => 'level_id']), $f('start_date', 'تاریخ شروع', 'date'), $f('is_primary', 'درس اصلی', 'bool'), ...$description];
        $sections['my-lessons'] = $s('my-lessons', 'درس‌های من', 'My lessons', '/analytics/personal-offerings/lessons', 'items', $lessonFields, 'common');
        $sections['my-lessons']['actions']['list']['path'] = '/analytics/personal-data/lessons';
        $sections['my-lessons']['actions']['update']['path'] = '/analytics/personal-offerings/lessons/{id}';
        $sections['my-lessons']['actions']['delete']['path'] = '/analytics/personal-offerings/lesson/{id}/delete';
        $scheduleFields = [$f('day', 'روز', 'text'), $f('repeatPeriod', 'تکرار', 'select', ['هفتگی' => 'هفتگی', 'دو هفته' => 'دو هفته', 'سه هفته' => 'سه هفته', 'چهار هفته' => 'چهار هفته', 'ماهانه' => 'ماهانه', 'سالانه' => 'سالانه', 'بی‌تکرار' => 'بی‌تکرار']), $f('repeatDate', 'تاریخ شروع', 'date'), $f('timezone', 'منطقه زمانی'), $f('ranges', 'بازه‌ها', 'rows', [$f('start', 'شروع', 'time'), $f('end', 'پایان', 'time'), $f('status', 'وضعیت')]), ...$description];
        $sections['my-schedule'] = $s('my-schedule', 'برنامه زمانی من', 'My schedule', '/analytics/personal-offerings/schedules', 'items', $scheduleFields, 'common');
        $sections['my-schedule']['actions']['list']['path'] = '/analytics/personal-data/schedules';
        $sections['my-schedule']['actions']['update']['path'] = '/analytics/personal-offerings/schedules/{id}';
        $sections['my-schedule']['actions']['delete']['path'] = '/analytics/personal-offerings/schedule/{id}/delete';
        $sections['my-finance'] = $s('my-finance', 'امور مالی من', 'My finance', '/analytics/personal-data/finance', 'invoices', [], 'common', false);
        $sections['my-finance']['actions']['update'] = $a('/analytics/personal-invoices/{id}/update', 'ویرایش فاکتور خود', 'POST', [$f('amount', 'مبلغ', 'number', null, true), $f('statusCode', 'وضعیت', 'select', ['draft' => 'پیش‌نویس', 'issued' => 'صادرشده', 'canceled' => 'لغوشده']), $f('dueDate', 'سررسید', 'date'), $f('title', 'شرح', 'text', null, true), ...$description]);
        $sections = $this->completePanelContracts($sections, $webDialogs);
        $this->scopedExceptionContracts($sections);
        if ($webDialogs) $sections['availability-exceptions']['hidden'] = true;
        $this->financeAndGuidanceContracts($sections);
        $sections = $this->authorizedActions($sections);
        if ($webDialogs) {
            $presentation = require dirname(__DIR__) . '/Resources/mobile-panel-presentation.php';
            foreach ($presentation as $key => $contract) {
                if (!isset($sections[$key])) continue;
                if ($contract['rowActions'] === null) {
                    $contract['rowActions'] = ['details', ...array_keys(array_filter($sections[$key]['actions'], fn ($action, $name) => ($action['row'] ?? false) && !in_array($name, ['status', 'details'], true), ARRAY_FILTER_USE_BOTH))];
                }
                $contract['detailFields'] ??= array_values(array_reduce(
                    [...$contract['columns'], ...($sections[$key]['fields'] ?? [])],
                    function ($fields, $field) { $fields[$field['key']] = $field; return $fields; }, []
                ));
                $sections[$key]['presentation'] = $contract;
            }
        }
        $sections['students']['presentation']['detailFields'][] = ['key' => 'teacherNotes', 'label' => 'یادداشت‌های استاد', 'en' => 'Teacher notes'];
        $sections['pages']['detail'] = $sections['page-content'];
        return $sections;
    }

    private function financeAndGuidanceContracts(array &$sections): void
    {
        $statuses = ['draft' => 'پیش‌نویس', 'issued' => 'صادرشده', 'partial' => 'پرداخت جزئی', 'paid' => 'پرداخت‌شده', 'pending' => 'در انتظار پرداخت', 'canceled' => 'لغوشده'];
        foreach (['finance', 'my-finance'] as $key) {
            foreach ($sections[$key]['actions']['update']['fields'] as &$field) {
                if ($field['key'] === 'statusCode') {
                    $field = array_replace($field, ['type' => 'select', 'options' => $statuses, 'readOnly' => true]);
                }
            }
            unset($field);
        }
        $sections['page-guidance'] = $this->s('page-guidance', 'توضیحات صفحات', 'Page guidance', '/analytics/panel-guides', '', [], 'common', false);
        $sections['page-guidance']['actions']['save'] = $this->a('/analytics/admin-panel-guide', 'ذخیره توضیحات', 'POST', [], false) + ['access' => 'founder'];
        $sections['finance']['actions']['subscriptions'] = $this->a('/academy/admin/subscriptions', 'اشتراک‌ها', 'GET', [], false) + ['hidden' => true];
        $sections['finance']['actions']['subscription-pay'] = $this->a('/academy/admin/subscriptions/{id}/zarinpal', 'خرید اشتراک', 'POST', [$this->f('months', 'مدت اشتراک', 'number', null, true)]) + ['hidden' => true];
        $fields = [
            $this->f('organizationUserId', 'سازمان', 'select', ['source' => 'organizations', 'id' => 'user_id'], true) + ['defaultFirstOption' => true],
            $this->f('title', 'عنوان تخفیف', 'text', null, true) + ['initial' => 'name', 'maxLength' => 190],
            $this->f('type', 'نوع', 'select', ['percentage' => 'درصدی', 'fixed' => 'مبلغ ثابت'], true) + ['default' => 'percentage'],
            $this->f('value', 'مقدار', 'number', null, true) + ['min' => 0.01, 'max' => 999999999999],
        ];
        $sections['term-discounts'] = $this->s('term-discounts', 'تخفیف‌ها', 'Discounts', '/academy/admin/term-discounts', 'discounts', $fields);
        foreach (['create', 'update'] as $operation) $sections['term-discounts']['actions'][$operation]['formContractVersion'] = 2;
        foreach (['discounts', 'discount'] as $operation) $sections['terms']['actions'][$operation]['hidden'] = true;
    }

    private function completePanelContracts(array $sections, bool $webDialogs = false): array
    {
        $sections['terms']['actions']['attendance'] = $this->a('/academy/admin/class-schedules', 'حضور و غیاب', 'GET') + ['en' => 'Attendance'];
        $sections['lessons']['actions']['usage'] = $this->a('/academy/admin/branch-offerings/lessons/{id}/usage', 'محل‌های استفاده از درس', 'GET') + ['en' => 'Lesson usage'];
        $this->profileAndOrganizationContracts($sections);
        $this->userAccessContracts($sections);
        $this->classroomContracts($sections);
        $this->siteContentContracts($sections);
        $this->scheduleQueryContracts($sections);
        $this->holidayContracts($sections);
        $this->waitingApprovalContracts($sections);
        $this->waitingFinalizationContracts($sections);
        if ($webDialogs) $this->webDialogContracts($sections);
        $this->recentPanelContracts($sections, $webDialogs);
        foreach (['students', 'members'] as $key) {
            $sections[$key]['actions']['user-lookup'] = $this->a('/academy/admin/member-users/lookup', 'جستجوی کاربر موجود', 'POST', [], false) + ['en' => 'Find existing user', 'hidden' => true];
        }
        $sections['terms']['actions']['schedule-preview'] = $this->a('/academy/admin/terms/{id}/student-schedule-preview', 'پیش‌نمایش جلسات هنرجو', 'POST', [], true) + ['en' => 'Preview student sessions', 'hidden' => true];
        $registrationFields = [
            $this->f('academy_id', 'آموزشگاه', 'select', ['source' => 'academies'], true) + ['en' => 'Academy'],
            $this->f('academy_name', 'نام شعبه', 'text', null, true) + ['en' => 'Branch name'],
            $this->f('username', 'نام کاربری', 'text', null, true) + ['en' => 'Username'],
            $this->f('register_method', 'روش ثبت‌نام', 'select', ['email' => 'Email', 'phone' => 'Mobile'], true) + ['en' => 'Registration method', 'default' => 'email'],
            $this->f('email', 'ایمیل', 'email') + ['en' => 'Email', 'requiredWhen' => ['_form.register_method' => 'email']],
            $this->f('phone', 'موبایل', 'phone') + ['en' => 'Mobile', 'requiredWhen' => ['_form.register_method' => 'phone']],
            $this->f('password', 'رمز عبور', 'password', null, true) + ['en' => 'Password'],
            $this->f('password2', 'تکرار رمز عبور', 'password', null, true) + ['en' => 'Confirm password'],
            $this->f('terms', 'قوانین را می‌پذیرم', 'bool', null, true) + ['en' => 'I accept the terms'],
            $this->f('otp', 'کد تأیید', 'text') + ['en' => 'Verification code'],
        ];
        $sections['branches']['actions']['register-account'] = $this->a('/academy/admin/branches/registration', 'ثبت حساب شعبه', 'POST', $registrationFields, false) + ['en' => 'Register branch account', 'formContractVersion' => 2];
        $sections['branches']['actions']['registration-otp'] = $this->a('/academy/admin/branches/registration/send-otp', 'ارسال کد تأیید شعبه', 'POST', [], false) + ['en' => 'Send branch verification code', 'hidden' => true];
        return $sections;
    }

    private function recentPanelContracts(array &$sections, bool $webDialogs): void
    {
        $english = ['username' => 'Username', 'register_method' => 'Registration method', 'email' => 'Email', 'password' => 'Password', 'password2' => 'Confirm password', 'terms' => 'I accept the registration terms', 'otp' => 'Verification code', 'registration' => 'Employee account registration', 'repeatType' => 'Repeat period', 'weekday' => 'Weekday', 'timezoneId' => 'Time zone', 'rangeStart' => 'Range start', 'rangeEnd' => 'Range end', 'durationMinutes' => 'Session duration (minutes)', 'requestKey' => 'Request identifier', 'classroomId' => 'Classroom'];
        $f = fn (...$args) => $this->f(...$args) + ['en' => $english[$args[0]] ?? $args[1]];
        $this->staffRegistrationContracts($sections, $webDialogs, $f);
        $this->batchTermContracts($sections, $f);
        $this->termAssignmentContracts($sections, $f);
    }

    private function staffRegistrationContracts(array &$sections, bool $webDialogs, callable $f): void
    {
        $sections['members']['actions']['registration-otp'] = $this->a('/academy/admin/members/registration-otp', 'ارسال کد تأیید', 'POST', [], false) + ['hidden' => true];
        $registration = [
            $f('username', 'نام کاربری', 'text', null, true),
            $f('register_method', 'روش ثبت‌نام', 'select', ['phone' => 'موبایل', 'email' => 'ایمیل'], true) + ['default' => 'phone'],
            $f('email', 'ایمیل', 'email') + ['visibleWhen' => ['_form.register_method' => 'email'], 'requiredWhen' => ['_form.register_method' => 'email']],
            $f('password', 'رمز عبور', 'password', null, true), $f('password2', 'تکرار رمز عبور', 'password', null, true),
            $f('terms', 'قوانین ثبت‌نام را می‌پذیرم', 'bool', null, true),
            $f('otp', 'کد تأیید', 'text'),
        ];
        if ($webDialogs) {
            $sections['members']['actions']['create']['fields'][] = $f('registration', 'ثبت‌نام حساب کارمند', 'object', $registration) + ['visibleWhen' => ['_form.type' => ['in' => ['manager', 'receptionist']]]];
            $sections['members']['actions']['create']['formContractVersion'] = 2;
        }
    }

    private function batchTermContracts(array &$sections, callable $f): void
    {
        $canonical = $sections;
        $this->termDialogContracts($canonical);
        $source = array_column($canonical['terms']['actions']['create']['fields'], null, 'key');
        $fields = [];
        foreach (['name', 'organizationUserId', 'courseId', 'teachers', 'currencyId', 'cost', 'sessionCount', 'summary', 'description'] as $key) {
            $field = $source[$key];
            if ($key === 'classroomId') $field['required'] = false;
            if ($key === 'teachers') { unset($field['maxItemsFrom']); $field['maxItems'] = 1; }
            $fields[] = $field;
        }
        $fields = [...$fields, ...$this->batchScheduleFields($f)];
        $sections['terms']['actions']['batch'] = $this->a('/academy/admin/terms/batch', 'افزودن گروهی ترم‌های استاد', 'POST', $fields, false) + ['en' => 'Create teacher terms in batch', 'formContractVersion' => 2];
    }

    private function batchScheduleFields(callable $f): array
    {
        return [
            $f('repeatType', 'دوره تکرار', 'select', ['week' => 'هفتگی', '2-week' => 'دو هفته یک‌بار', '3-week' => 'سه هفته یک‌بار', '4-week' => 'چهار هفته یک‌بار'], true) + ['default' => 'week'],
            $f('weekday', 'روز هفته', 'select', ['6' => 'شنبه', '0' => 'یکشنبه', '1' => 'دوشنبه', '2' => 'سه‌شنبه', '3' => 'چهارشنبه', '4' => 'پنجشنبه', '5' => 'جمعه'], true),
            $f('timezoneId', 'منطقه زمانی', 'select', ['source' => 'timezones'], true) + ['defaultFirstOption' => true],
            $f('rangeStart', 'شروع بازه', 'time', null, true) + ['default' => '10:00'],
            $f('rangeEnd', 'پایان بازه', 'time', null, true) + ['default' => '18:00'],
            $f('durationMinutes', 'مدت جلسه (دقیقه)', 'number', null, true) + ['default' => 30, 'min' => 5, 'max' => 1440, 'step' => 5],
            $f('requestKey', 'شناسه درخواست', 'text', null, true) + ['hiddenWhen' => ['operation' => 'batch'], 'submitWhenHidden' => true],
        ];
    }

    private function termAssignmentContracts(array &$sections, callable $f): void
    {
        $canonical = $sections;
        $this->studentDialogContracts($canonical);
        $studentFields = array_values(array_filter($canonical['students']['actions']['create']['fields'], fn ($field) => !in_array($field['key'], ['organizationUserId', 'termId', 'availabilities', 'availabilitySummary', 'availabilityDescription'], true)));
        foreach ($studentFields as &$field) {
            if ($field['key'] === 'studentSchedule') $field['visibleWhen'] = ['record.status_code' => 'open', 'record.hasSessions' => ['not' => true]];
        }
        unset($field);
        $sections['terms']['actions']['assign-student'] = $this->a('/academy/admin/terms/{id}/student', 'تعیین هنرجو', 'POST', $studentFields) + ['en' => 'Assign student', 'formContractVersion' => 2];
        $sections['terms']['actions']['classroom'] = $this->a('/academy/admin/terms/{id}/classroom', 'تعیین کلاس برگزاری', 'POST', [
            $f('classroomId', 'کلاس برگزاری', 'select', ['source' => 'classrooms', 'termRooms' => true], true),
        ]) + ['en' => 'Assign classroom', 'formContractVersion' => 2];
    }

    /** Fields and dependencies of the active web forms (not the legacy demo templates). */
    private function webDialogContracts(array &$sections): void
    {
        $f = fn (...$args) => $this->f(...$args);
        $number = fn ($key, $label, $default, $min = 0) => $f($key, $label, 'number') + ['default' => $default, 'min' => $min];
        $select = fn ($key, $label, $source, $id = 'id', $required = false) => $f($key, $label, 'select', ['source' => $source, 'id' => $id], $required);
        $org = fn ($source = 'organizations') => $select('organizationUserId', 'سازمان', $source, 'user_id', true) + ['defaultFirstOption' => true, 'hiddenWhen' => ['permissions.isBranchContext' => true], 'submitWhenHidden' => true];
        $text = [$f('summary', 'خلاصه'), $f('description', 'توضیحات', 'multiline')];
        $active = ['active' => 'فعال', 'inactive' => 'غیرفعال'];
        $teacher = ['visibleWhen' => ['_form.type' => 'teacher'], 'dependsOn' => ['organizationUserId']];
        $fields = [
            $f('name', 'نام و نام خانوادگی', 'text', null, true),
            $f('gender', 'جنسیت', 'select', ['male' => 'مرد', 'female' => 'زن', 'other' => 'سایر'], true) + ['default' => 'male'],
            $f('nationalId', 'کد ملی', 'text', null, true), $f('birthDate', 'تاریخ تولد', 'date', null, true),
            $f('phone', 'شماره تماس', 'phone', null, true),
            $f('profileVisibility', 'نمایش پروفایل', 'select', ['public' => 'عمومی', 'private' => 'خصوصی']) + ['default' => 'public'],
            array_replace($org('staff_catalog.organizations'), ['hiddenWhen' => ['staff_catalog.organization_selection' => 'fixed']]),
            $f('type', 'نوع قرارداد', 'select', ['teacher' => 'مدرس', 'receptionist' => 'پذیرش', 'manager' => 'مدیر', 'other' => 'سایر'], true) + ['default' => 'teacher'],
            $f('roleId', 'نقش', 'select', ['source' => 'staff_catalog.roles', 'staffRoles' => true], true) + ['dependsOn' => ['organizationUserId', 'type']],
            $f('contractTitle', 'عنوان قرارداد', 'text', null, true),
            $f('startDate', 'تاریخ شروع قرارداد', 'date', null, true), $f('endDate', 'تاریخ خاتمه قرارداد', 'date', null, true),
            $select('currencyId', 'نوع پول پرداختی', 'staff_catalog.currencies', 'id', true) + ['defaultFirstOption' => true],
            $number('price', 'مبلغ قرارداد', 5000000),
            $f('lessonId', 'نوع درس', 'select', ['source' => 'staff_catalog.lessons', 'id' => 'lesson_id', 'match' => ['organization_user_id' => 'organizationUserId'], 'requireMatch' => true], true) + $teacher,
            $f('levelId', 'سطح درس', 'select', ['source' => 'staff_catalog.levels', 'allowedIdsFrom' => ['source' => 'staff_catalog.lessons', 'field' => 'level_ids', 'match' => ['organization_user_id' => 'organizationUserId', 'lesson_id' => 'lessonId']]], true) + array_replace($teacher, ['dependsOn' => ['organizationUserId', 'lessonId']]),
            $f('contractDescription', 'شرح قرارداد', 'multiline'),
        ];
        $this->setDialogFields($sections, 'members', $fields);
        $fields = [$f('name', 'نام کلاس', 'text', null, true),
            $f('deliveryMode', 'نوع برگزاری', 'select', ['in_person' => 'حضوری', 'online' => 'آنلاین']) + ['default' => 'in_person'],
            $f('onlineUrl', 'لینک کلاس آنلاین', 'url', null, true) + ['visibleWhen' => ['_form.deliveryMode' => 'online'], 'maxLength' => 2048],
            $select('typeId', 'نوع کلاس', 'classroomTypes', 'id', true) + ['defaultFirstOption' => true], $org(),
            $number('capacity', 'ظرفیت', 1, 1),
            $f('status', 'وضعیت', 'select', ['available' => 'در دسترس', 'unavailable' => 'خارج از دسترس']) + ['initial' => 'statusValue', 'default' => 'available', 'hiddenWhen' => ['permissions.isReceptionist' => true]],
            ...$text, $f('equipment', 'تجهیزات', 'rows', [$f('name', 'نام تجهیز'), $number('qty', 'تعداد', 1, 1)])];
        $this->setDialogFields($sections, 'classrooms', $fields);
        $this->setDialogFields($sections, 'courses', [
            $f('name', 'نام دوره', 'text', null, true), $org(),
            $f('lesson_id', 'درس', 'select', ['source' => 'lessons', 'match' => ['organizationUserId' => 'organizationUserId'], 'requireMatch' => true]) + ['dependsOn' => ['organizationUserId']],
            $number('teacher_capacity', 'ظرفیت اساتید', 1, 1), $number('student_capacity', 'ظرفیت هنرجوها', 1, 1),
            $f('status', 'وضعیت', 'select', ['open' => 'باز', 'ongoing' => 'در حال برگزاری', 'finished' => 'پایان یافته']) + ['default' => 'open', 'hiddenWhen' => ['permissions.isReceptionist' => true]], ...$text,
        ]);
        $this->setDialogFields($sections, 'branch-types', [$f('title', 'عنوان', 'text', null, true), ...$text]);
        $this->setDialogFields($sections, 'course-levels', [$f('name', 'عنوان سطح', 'text', null, true), ...$text]);
        // The website only creates lesson levels; retain the legacy route but hide it.
        if (isset($sections['course-levels']['actions']['update'])) $sections['course-levels']['actions']['update']['hidden'] = true;
        $this->setDialogFields($sections, 'classroom-types', [
            $f('type', 'دسته نوع کلاس', 'select', ['source' => 'typeOptions', 'id' => 'value'], true) + ['default' => 'other', 'hiddenWhen' => ['permissions.canSetType' => false]],
            $f('title', 'عنوان', 'text', null, true), $f('summary', 'خلاصه', 'multiline', null, true), $f('description', 'شرح', 'multiline', null, true),
        ]);
        $sections['classroom-types']['actions']['create-from-classroom']['fields'] = $sections['classroom-types']['actions']['create']['fields'];
        $sections['classroom-types']['actions']['create-from-classroom']['formContractVersion'] = 2;
        $this->setDialogFields($sections, 'points', [
            $f('title', 'عنوان', 'text', null, true), $number('points', 'امتیاز', 10, 1) + ['max' => 100000],
            $f('type', 'نوع امتیاز', 'select', ['general' => 'عمومی', 'professional' => 'حرفه‌ای']) + ['default' => 'general'],
            $f('category', 'دسته', 'select', ['attendance' => 'حضور', 'academic' => 'آموزشی', 'event' => 'رویداد', 'social' => 'اجتماعی', 'financial' => 'مالی', 'profile' => 'پروفایل', 'achievement' => 'دستاورد', 'engagement' => 'تعامل', 'management' => 'مدیریت']) + ['default' => 'engagement'],
            $f('source', 'منبع رویداد', 'select', ['database' => 'رویداد قطعی دیتابیس', 'tracking' => 'رفتار کاربر', 'manual' => 'اعطای دستی']) + ['default' => 'database'],
            $f('action', 'کد عملیات', 'text', null, true),
            $f('repeatMode', 'روش تکرار', 'select', ['event' => 'هر رویداد یکتا', 'daily' => 'دارای سقف روزانه', 'once' => 'فقط یک‌بار']) + ['default' => 'event'],
            $number('dailyCap', 'سقف روزانه (۰ = بدون سقف)', 0), $number('cooldownMinutes', 'فاصله تکرار (دقیقه)', 0),
            $select('branchId', 'سازمان', 'organizations') + ['defaultFirstOption' => true],
            $f('status', 'وضعیت', 'select', $active) + ['default' => 'active'], ...$text,
        ]);
        foreach (['roles', 'permissions'] as $key) {
            $fields = $sections[$key]['actions']['create']['fields'];
            $fields = array_values(array_filter($fields, fn ($field) => !in_array($field['key'], $key === 'roles' ? ['group', 'sortOrder'] : ['name', 'group'], true)));
            foreach ($fields as &$field) {
                if ($field['key'] === 'level') $field = $f('level', 'سطح', 'select', ['1' => '۱', '2' => '۲', '3' => '۳']) + ['default' => 1];
                if ($field['key'] === 'risk') $field = $f('risk', 'درجه حساسیت', 'select', ['1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵']) + ['default' => 1];
                if ($field['key'] === 'scope') $field['default'] = 'website';
                if ($field['key'] === 'type') $field['default'] = $key === 'roles' ? 'system' : 'sidebar';
                if ($field['key'] === 'approval') $field['default'] = 'confirm';
                if ($field['key'] === 'color') { $field['type'] = 'color'; $field['default'] = '#4f46e5'; }
                if ($field['key'] === 'parentId') $field['options']['excludeSource'] = '_form.id';
                if ($field['key'] === 'translations') foreach ($field['options'] as &$language) foreach ($language['options'] as &$translation) $translation['required'] = false;
            }
            unset($field, $language, $translation);
            $this->setDialogFields($sections, $key, $fields);
        }
        $this->studentDialogContracts($sections);
        $this->termDialogContracts($sections);
        $this->scheduleDialogContracts($sections);
        $this->profileDialogContracts($sections);
        $this->contentDialogContracts($sections);
    }

    private function scopedExceptionContracts(array &$sections): void
    {
        foreach (['organization-holidays' => ['organization', 'تعطیلات', 'Holidays'], 'employee-leaves' => ['member', 'مرخصی‌ها', 'Leaves']] as $key => [$target, $label, $english]) {
            $section = $sections['availability-exceptions'];
            $section['key'] = $key; $section['label'] = $label; $section['en'] = $english;
            $fields = [$this->f('organizationUserId', 'سازمان', 'select', ['source' => 'staff_catalog.organizations', 'id' => 'user_id'], true)];
            if ($target === 'member') {
                $fields[0]['initialLookup'] = ['source' => 'members', 'by' => 'membershipId', 'id' => 'id', 'field' => 'organizationUserId'];
                $fields[] = $this->f('membershipId', 'کارمند', 'select', ['source' => 'members', 'match' => ['organizationUserId' => 'organizationUserId'], 'requireMatch' => true], true) + ['dependsOn' => ['organizationUserId']];
            }
            foreach ($section['actions']['create']['fields'] as $field) {
                if (in_array($field['key'], ['organizationUserId', 'membershipId', 'targetType', 'targetValue'], true)) continue;
                if ($field['key'] === 'type') {
                    $field['type'] = 'select';
                    $types = ['holiday' => 'تعطیل رسمی', 'closed' => 'تعطیل', 'unavailable' => 'عدم دسترسی', 'busy' => 'مشغول', 'vacation' => 'مرخصی', 'blocked' => 'مسدود'];
                    $field['options'] = array_filter($types, static fn ($name) => $target === 'organization' ? $name !== 'vacation' : !in_array($name, ['holiday', 'closed'], true), ARRAY_FILTER_USE_KEY);
                    $field['default'] = $target === 'organization' ? 'closed' : 'vacation';
                }
                $fields[] = $field;
            }
            $path = '/academy/admin/availability-exceptions/targets/' . $target;
            foreach ($section['actions'] as $operation => &$action) $action['path'] = $path . (in_array($operation, ['list', 'create'], true) ? '' : '/{id}/' . $operation);
            unset($action);
            $sections[$key] = $section;
            $this->setDialogFields($sections, $key, $fields);
        }
    }

    private function setDialogFields(array &$sections, string $key, array $fields): void
    {
        foreach (['create', 'update'] as $operation) {
            if (!isset($sections[$key]['actions'][$operation])) continue;
            $sections[$key]['actions'][$operation]['fields'] = $fields;
            $sections[$key]['actions'][$operation]['formContractVersion'] = 2;
        }
        $sections[$key]['fields'] = $fields;
    }

    private function studentDialogContracts(array &$sections): void
    {
        $f = fn (...$args) => $this->f(...$args);
        $under18 = ['visibleWhen' => ['_form.birthDate' => ['underAge' => 18]]];
        $ongoing = ['visibleWhen' => ['_selected.termId.status' => 'ongoing']];
        $fields = [$f('organizationUserId', 'سازمان', 'select', ['source' => 'organizations', 'id' => 'user_id'], true) + ['defaultFirstOption' => true, 'hiddenWhen' => ['permissions.isBranchContext' => true], 'submitWhenHidden' => true]];
        foreach (['name' => 'نام و نام خانوادگی', 'nationalId' => 'کد ملی', 'fatherName' => 'نام پدر', 'birthDate' => 'تاریخ تولد', 'phone' => 'شماره تماس'] as $key => $label) $fields[] = $f($key, $label, $key === 'birthDate' ? 'date' : ($key === 'phone' ? 'phone' : 'text'), null, true);
        $fields[] = $f('registrationDate', 'تاریخ ثبت‌نام', 'date') + ['defaultToday' => true];
        $fields[] = $f('addresses', 'نشانی', 'rows', [
            $f('province', 'استان', 'select', ['source' => 'provinces', 'id' => 'province_name', 'label' => 'province_name']),
            $f('city', 'شهر', 'select', ['source' => 'counties', 'id' => 'county_name', 'label' => 'county_name', 'match' => ['province_id' => '_selected.province.province_id'], 'requireMatch' => true]) + ['dependsOn' => ['province']],
            $f('address', 'نشانی', 'multiline'), $f('postal_code', 'کد پستی'), $f('lat', 'عرض جغرافیایی', 'number'), $f('lng', 'طول جغرافیایی', 'number'),
            $f('is_main', 'نشانی اصلی', 'bool') + ['default' => true],
        ]) + ['maxItems' => 1];
        foreach (['parentName' => 'نام و نام خانوادگی والد', 'parentNationalId' => 'شماره ملی والد', 'parentFatherName' => 'نام پدر والد', 'parentBirthDate' => 'تاریخ تولد والد', 'parentPhone' => 'شماره تماس والد'] as $key => $label) $fields[] = $f($key, $label, $key === 'parentBirthDate' ? 'date' : ($key === 'parentPhone' ? 'phone' : 'text'), null, true) + $under18;
        $fields[] = $f('termId', 'ترم آموزشی', 'select', ['source' => 'terms', 'openTermsOnlyOnCreate' => true, 'match' => ['organizationUserId' => 'organizationUserId'], 'requireMatch' => true], true) + ['dependsOn' => ['organizationUserId']];
        $fields[] = $f('studentSchedule', 'زمان‌بندی جلسات ترم', 'object', [
            $f('classroomId', 'کلاس برگزاری', 'select', ['source' => 'classrooms', 'termRooms' => true]),
            $f('firstDate', 'تاریخ جلسه اول', 'date'),
        ]) + ['visibleWhen' => ['_selected.termId.status' => 'open', '_selected.termId.hasSessions' => ['not' => true]]];
        $days = ['saturday' => 'شنبه', 'sunday' => 'یکشنبه', 'monday' => 'دوشنبه', 'tuesday' => 'سه‌شنبه', 'wednesday' => 'چهارشنبه', 'thursday' => 'پنجشنبه', 'friday' => 'جمعه'];
        $fields[] = $f('availabilities', 'افزودن برنامه زمانی هنرجو', 'rows', [
            $f('day', 'روز', 'select', $days, true) + ['default' => 'saturday'],
            $f('timezoneId', 'منطقه زمانی', 'select', ['source' => 'timezones', 'id' => 'timezone_id', 'label' => 'timezone'], true),
            $f('startTime', 'ساعت شروع', 'time', null, true), $f('endTime', 'ساعت پایان', 'time', null, true),
        ], true) + $ongoing;
        $fields[] = $f('availabilitySummary', 'خلاصه') + $ongoing;
        $fields[] = $f('availabilityDescription', 'توضیحات', 'multiline') + $ongoing;
        $this->setDialogFields($sections, 'students', $fields);
    }

    private function termDialogContracts(array &$sections): void
    {
        $f = fn (...$args) => $this->f(...$args);
        $fields = [
            $f('name', 'نام ترم', 'text', null, true),
            $f('organizationUserId', 'سازمان', 'select', ['source' => 'organizations', 'id' => 'user_id'], true) + ['defaultFirstOption' => true, 'initialLookup' => ['source' => 'courses', 'by' => 'courseId', 'field' => 'organizationUserId'], 'hiddenWhen' => ['permissions.isBranchContext' => true], 'submitWhenHidden' => true],
            $f('courseId', 'دوره مرتبط', 'select', ['source' => 'courses', 'match' => ['organizationUserId' => 'organizationUserId'], 'requireMatch' => true], true) + ['dependsOn' => ['organizationUserId']],
            $f('classroomId', 'کلاس برگزاری', 'select', ['source' => 'classrooms', 'match' => ['organizationUserId' => 'organizationUserId'], 'requireMatch' => true], true) + ['dependsOn' => ['organizationUserId']],
            $f('currencyId', 'نوع پول', 'select', ['source' => 'currencies'], true) + ['defaultFirstOption' => true],
            $f('cost', 'هزینه ترم', 'number') + ['default' => 15000000, 'min' => 0],
            $f('installmentCount', 'تعداد اقساط', 'number') + ['default' => 1, 'min' => 1],
            $f('discountId', 'تخفیف', 'select', ['source' => 'discounts']),
            $f('status', 'وضعیت', 'select', ['open' => 'باز', 'ongoing' => 'در حال برگزاری']) + ['default' => 'open', 'hiddenWhen' => ['permissions.isReceptionist' => true], 'valueWhen' => ['when' => ['permissions.isReceptionist' => true], 'value' => 'pending'], 'submitWhenHidden' => true],
            $f('sessionCount', 'تعداد جلسات', 'number') + ['default' => 8, 'min' => 1, 'max' => 100],
            $f('repeatType', 'دوره تکرار', 'select', ['week' => 'هفتگی', '2-week' => 'دو هفته یک‌بار', '3-week' => 'سه هفته یک‌بار', '4-week' => 'چهار هفته یک‌بار', 'month' => 'ماهانه', 'year' => 'سالانه', 'no-period' => 'سایر (تاریخ دقیق)']) + ['default' => 'week'],
            $f('recurrence', 'زمان تکرار', 'object', [
                $f('weekday', 'روز هفته', 'select', ['6' => 'شنبه', '0' => 'یکشنبه', '1' => 'دوشنبه', '2' => 'سه‌شنبه', '3' => 'چهارشنبه', '4' => 'پنجشنبه', '5' => 'جمعه']) + ['visibleWhen' => ['_root.repeatType' => ['in' => ['week', '2-week', '3-week', '4-week']]]],
                $f('day', 'روز ماه شمسی', 'number') + ['min' => 1, 'max' => 31, 'visibleWhen' => ['_root.repeatType' => ['in' => ['month', 'year']]]],
                $f('month', 'ماه شمسی', 'select', array_combine(range(1, 12), ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'])) + ['visibleWhen' => ['_root.repeatType' => 'year']],
                $f('exactDate', 'تاریخ دقیق', 'date') + ['visibleWhen' => ['_root.repeatType' => 'no-period']],
            ]) + ['visibleWhen' => ['operation' => 'create', '_form.status' => ['in' => ['open', 'pending']]]],
            $f('sessions', 'جلسه‌ها', 'rows', [
                $f('date', 'تاریخ', 'date', null, true) + ['hiddenWhen' => ['operation' => 'create', '_root.status' => ['in' => ['open', 'pending']]]],
                $f('timezoneId', 'منطقه زمانی', 'select', ['source' => 'timezones'], true) + ['defaultFirstOption' => true],
                $f('startTime', 'ساعت شروع', 'time', null, true) + ['default' => '10:00'],
                $f('durationMinutes', 'مدت جلسه (دقیقه)', 'number', null, true) + ['default' => 90, 'durationFrom' => ['startTime', 'endTime'], 'min' => 5, 'max' => 1440, 'step' => 5],
            ], true) + ['rowsCountFrom' => 'sessionCount', 'singleWhen' => ['operation' => 'create', '_form.status' => ['in' => ['open', 'pending']]]],
            $f('summary', 'خلاصه ترم'), $f('description', 'شرح ترم', 'multiline'),
            $f('teachers', 'استادها', 'multi', ['source' => 'members', 'where' => ['type' => 'teacher'], 'match' => ['organizationUserId' => 'organizationUserId', 'lessonId' => '_selected.courseId.lessonId'], 'requireMatch' => true], true) + ['initial' => 'teachers', 'dependsOn' => ['organizationUserId', 'courseId', 'classroomId'], 'maxItemsFrom' => '_selected.courseId.teacher_capacity'],
            $f('students', 'هنرجویان', 'multi', ['source' => 'members', 'where' => ['type' => 'student'], 'match' => ['organizationUserId' => 'organizationUserId']]) + ['initial' => 'students', 'dependsOn' => ['organizationUserId', 'courseId', 'classroomId'], 'hiddenWhen' => ['_form.status' => ['in' => ['open', 'pending']]], 'maxItemsFrom' => '_selected.courseId.student_capacity'],
        ];
        foreach ($fields as &$field) {
            if ($field['key'] === 'status') $field['visibleWhen'] = ['operation' => ['not' => 'create']];
            if ($field['key'] === 'classroomId') { $field['options'] = ['source' => 'classrooms', 'termRooms' => true]; $field['hiddenWhen'] = ['operation' => 'create']; }
            if (in_array($field['key'], ['teachers', 'students'], true)) {
                $field['options']['termPeople'] = true;
                unset($field['options']['match']['organizationUserId']);
            }
        }
        unset($field);
        $teacherField = array_values(array_filter($fields, fn ($field) => $field['key'] === 'teachers'))[0];
        $ordered = [];
        foreach ($fields as $field) {
            if ($field['key'] === 'teachers') continue;
            $ordered[] = $field;
            if ($field['key'] === 'courseId') $ordered[] = $teacherField;
        }
        $fields = $ordered;
        $this->setDialogFields($sections, 'terms', $fields);
    }

    private function contentDialogContracts(array &$sections): void
    {
        foreach (['lessons', 'my-lessons'] as $key) {
            $fields = $sections[$key]['actions']['create']['fields'];
            foreach ($fields as &$field) {
                if ($field['key'] === 'organization_user_id') $field += ['defaultFirstOption' => true, 'initial' => 'user_id', 'hiddenWhen' => ['organization_selection' => 'fixed'], 'submitWhenHidden' => true];
                if (in_array($field['key'], ['lesson_id', 'start_date'], true)) $field['required'] = true;
                if ($field['key'] === 'level_id' && $key === 'lessons') $field['options']['id'] = 'level_id';
                if ($field['key'] === 'status') { $field['options'] = ['active' => 'فعال', 'inactive' => 'غیرفعال']; $field['default'] = 'active'; $field['hiddenWhen'] = ['lesson_status_mode' => 'pending']; }
            }
            unset($field);
            $this->setDialogFields($sections, $key, $fields);
        }
        foreach (['gallery', 'my-gallery'] as $key) {
            $fields = $sections[$key]['actions']['create']['fields'];
            foreach ($fields as &$field) if ($field['key'] === 'collection') { $field['default'] = 'gallery'; }
            unset($field);
            $this->setDialogFields($sections, $key, $fields);
            foreach ($sections[$key]['actions']['create']['fields'] as &$field) if ($field['key'] === 'file') $field['required'] = true;
            unset($field);
            // Website edit changes metadata, never requires re-uploading the existing file.
            $sections[$key]['actions']['update']['fields'] = array_values(array_filter($fields, fn ($field) => $field['key'] !== 'file'));
        }
        foreach (['create', 'update'] as $operation) {
            foreach ($sections['posts']['actions'][$operation]['fields'] as &$field) {
                if ($field['key'] === 'published_at') $field['type'] = 'datetime';
                if ($field['key'] === 'type') { $field['type'] = 'select'; $field['options'] = ['post' => 'نوشته', 'product' => 'محصول', 'music_theory' => 'تئوری موسیقی']; $field['default'] = 'post'; }
                if ($field['key'] === 'status') { $field['options'] = ['draft' => 'پیش‌نویس', 'published' => 'منتشرشده', 'pending' => 'در انتظار', 'private' => 'خصوصی', 'future' => 'زمان‌بندی‌شده', 'trash' => 'زباله‌دان']; $field['default'] = 'draft'; }
                if ($field['key'] === 'visibility') { $field['options'] = ['public' => 'عمومی', 'private' => 'خصوصی', 'followers' => 'دنبال‌کنندگان', 'premium' => 'ویژه']; $field['default'] = 'public'; }
            }
            unset($field);
            $sections['posts']['actions'][$operation]['fields'][] = $this->f('cover_media_id', 'شناسه رسانه تصویر', 'number');
            $sections['posts']['actions'][$operation]['fields'][] = $this->f('related_posts_id', 'نوشته‌های مرتبط (IDها)');
            $sections['posts']['actions'][$operation]['fields'][] = $this->f('guid', 'GUID / لینک دائمی');
            $sections['posts']['actions'][$operation]['formContractVersion'] = 2;
        }
        $sections['media']['actions']['update']['fields'] = array_values(array_filter($sections['media']['actions']['update']['fields'], fn ($field) => $field['key'] !== 'file'));
        $sections['media']['actions']['update']['formContractVersion'] = 2;
        $fields = $sections['notifications']['actions']['create']['fields'];
        $byKey = array_column($fields, null, 'key');
        $byKey['audience']['default'] = 'همه';
        $fields = [$byKey['audience'], $byKey['title'], $byKey['body']];
        $this->setDialogFields($sections, 'notifications', $fields);
        $sections['notifications']['actions']['create']['submitButtons'] = [
            ['label' => 'انتشار اعلان', 'en' => 'Publish notification', 'values' => ['asDraft' => false]],
            ['label' => 'ذخیره پیش‌نویس', 'en' => 'Save draft', 'values' => ['asDraft' => true]],
        ];
        $fields = $sections['account']['actions']['profile']['fields'];
        array_splice($fields, 1, 0, [$this->f('manager', 'مدیر اصلی') + ['readOnly' => true, 'submit' => false, 'hiddenWhen' => ['profile.accountType' => 'human']]]);
        $sections['account']['actions']['profile']['fields'] = $fields;
        $sections['account']['actions']['profile']['formContractVersion'] = 2;
    }

    private function profileDialogContracts(array &$sections): void
    {
        foreach (['awards', 'badges', 'experiences', 'certificates', 'educations', 'events', 'polls', 'publications'] as $key) {
            $fields = $sections[$key]['actions']['create']['fields'];
            foreach ($fields as &$field) {
                $name = $field['key'];
                $field['required'] = in_array($name, ['title', 'degree', 'question', 'verification_level_id', 'options'], true);
                if ($name === 'user_id') {
                    $field['key'] = $key === 'polls' ? 'owner_id' : 'user_id';
                    unset($field['initial']);
                    $field += ['defaultFirstOption' => true, 'hiddenWhen' => ['hideOrganizationField' => true], 'submitWhenHidden' => true];
                }
                if ($name === 'address_id' || $name === 'verification_level_id') {
                    $field['type'] = 'select';
                    $field['options'] = ['source' => $name === 'address_id' ? 'addresses' : 'levels'];
                    if ($name === 'address_id') { $field['options']['match'] = ['userId' => 'user_id']; $field['dependsOn'] = ['user_id']; }
                }
                if (in_array($name, ['granted_at', 'expires_at'], true)) $field['type'] = 'datetime';
                if ($name === 'status' && $key === 'badges') { $field['type'] = 'select'; $field['options'] = ['active' => 'فعال', 'expired' => 'منقضی', 'revoked' => 'لغوشده']; $field['default'] = 'active'; }
                if ($name === 'event_type') { $field['type'] = 'select'; $field['options'] = ['concert' => 'کنسرت', 'festival' => 'جشنواره', 'competition' => 'مسابقه', 'workshop' => 'کارگاه', 'other' => 'سایر']; $field['default'] = 'concert'; }
                if ($name === 'options') $field['type'] = 'strings';
                if ($key === 'polls' && $name === 'type') $field['default'] = 'single';
                if ($key === 'polls' && $name === 'status') $field['default'] = 'active';
            }
            unset($field);
            if ($key === 'polls') array_push($fields, $this->f('target_type', 'نوع هدف'), $this->f('target_id', 'شناسه هدف', 'number'));
            $orders = [
                'experiences' => ['user_id', 'title', 'organization', 'address_id', 'start_date', 'end_date', 'description'],
                'events' => ['user_id', 'title', 'event_type', 'event_date', 'address_id', 'description'],
                'polls' => ['owner_id', 'question', 'description', 'type', 'status', 'expires_at', 'target_type', 'target_id', 'is_anonymous', 'options'],
            ];
            if (isset($orders[$key])) { $byKey = array_column($fields, null, 'key'); $fields = array_map(fn ($name) => $byKey[$name], $orders[$key]); }
            $this->setDialogFields($sections, $key, $fields);
        }
    }

    private function scheduleDialogContracts(array &$sections): void
    {
        $timezones = ['Asia/Tehran', 'Asia/Dubai', 'Asia/Istanbul', 'Europe/London', 'Europe/Paris', 'Europe/Berlin', 'Europe/Rome', 'Europe/Amsterdam', 'America/New_York', 'America/Chicago', 'America/Los_Angeles', 'America/Toronto', 'UTC'];
        foreach (['availabilities', 'my-schedule', 'member-schedules', 'availability-exceptions'] as $key) {
            $fields = $sections[$key]['actions']['create']['fields'];
            if ($key === 'member-schedules') array_unshift($fields, $this->f('organizationUserId', 'سازمان', 'select', ['source' => 'staff_catalog.organizations', 'id' => 'user_id'], true) + ['defaultFirstOption' => true, 'initialLookup' => ['source' => 'members', 'by' => 'membershipId', 'id' => 'id', 'field' => 'organizationUserId'], 'hiddenWhen' => ['staff_catalog.organization_selection' => 'fixed'], 'submitWhenHidden' => true]);
            foreach ($fields as &$field) {
                $name = $field['key'];
                if ($name === 'timezone') { $field['type'] = 'select'; $field['options'] = array_combine($timezones, $timezones); $field['default'] = 'Asia/Tehran'; }
                if ($name === 'timezone' && $key === 'my-schedule') $field['options'] = ['source' => 'timezones', 'id' => 'timezone', 'label' => 'timezone'];
                if ($name === 'membershipId') { $field['options']['match'] = ['organizationUserId' => 'organizationUserId']; $field['dependsOn'] = ['organizationUserId']; }
                if ($name === 'ranges' && $key === 'availability-exceptions') $field['hiddenWhen'] = ['_form.allDay' => true];
                if ($name === 'repeatPeriod') $field['default'] = 'هفتگی';
                if ($name === 'day') { $field['default'] = 'شنبه'; $field['hiddenWhen'] = ['_form.repeatPeriod' => ['in' => ['ماهانه', 'سالانه', 'بی‌تکرار']]]; }
                if ($name === 'repeatDate') $field['visibleWhen'] = ['_form.repeatPeriod' => ['not' => 'هفتگی']];
            }
            unset($field);
            if ($key === 'availability-exceptions') {
                $fields = array_values(array_filter($fields, fn ($field) => !in_array($field['key'], ['membershipId', 'targetType'], true)));
                array_splice($fields, 1, 0, [$this->f('targetValue', 'شخص یا سازمان', 'select', ['sources' => [
                    ['source' => 'staff_catalog.organizations', 'id' => 'user_id', 'prefix' => 'organization:', 'match' => ['user_id' => 'organizationUserId'], 'requireMatch' => true],
                    ['source' => 'members', 'prefix' => 'member:', 'match' => ['organizationUserId' => 'organizationUserId'], 'requireMatch' => true],
                ]], true) + ['dependsOn' => ['organizationUserId']]]);
                array_splice($fields, 4, 0, [$this->f('timezone', 'منطقه زمانی', 'select', array_combine($timezones, $timezones)) + ['default' => 'Asia/Tehran']]);
            }
            $this->setDialogFields($sections, $key, $fields);
        }
        $fields = $sections['scheduling-rules']['actions']['create']['fields'];
        foreach ($fields as &$field) {
            if ($field['key'] === 'type') { $field['type'] = 'select'; $field['options'] = array_combine(['لغو', 'جبرانی', 'رزرو', 'زمان‌بندی'], ['لغو', 'جبرانی', 'رزرو', 'زمان‌بندی']); }
            if ($field['key'] === 'valueUnit') { $field['type'] = 'select'; $units = ['ساعت', 'دقیقه', 'روز', 'جلسه', 'غیبت', 'نفر', 'سال', 'بله/خیر', 'درصد', 'مبلغ']; $field['options'] = array_combine($units, $units); }
            if ($field['key'] === 'status') { $field['options'] = ['فعال' => 'فعال', 'غیرفعال' => 'غیرفعال']; $field['hiddenWhen'] = ['show_status_field' => false]; }
            if ($field['key'] === 'organizationKey') $field += ['defaultFirstOption' => true, 'hiddenWhen' => ['organization_selection' => 'fixed'], 'submitWhenHidden' => true];
            if ($field['key'] === 'title') $field['required'] = true;
        }
        unset($field);
        $this->setDialogFields($sections, 'scheduling-rules', $fields);
    }

    private function profileAndOrganizationContracts(array &$sections): void
    {
        foreach (['members', 'students', 'classrooms', 'classroom-types', 'courses', 'terms', 'lessons', 'availabilities', 'member-schedules', 'availability-exceptions', 'scheduling-rules', 'schedules', 'finance', 'gallery', 'points', 'awards', 'badges', 'experiences', 'certificates', 'educations', 'events', 'polls', 'publications'] as $key) {
            $sections[$key]['organizationFilter'] = true;
        }
        foreach (['awards', 'badges', 'experiences', 'certificates', 'educations', 'events', 'polls', 'publications'] as $key) {
            // The service scopes ordinary users to their own profile, just as on the website.
            $sections[$key]['access'] = 'common';
            $sections[$key]['actions']['status']['access'] = 'management';
            foreach (['create', 'update'] as $operation) foreach ($sections[$key]['actions'][$operation]['fields'] as &$field) {
                if ($key === 'polls' && $field['key'] === 'user_id') $field['initial'] = 'owner_id';
            }
            unset($field);
        }
        $sections['my-settings']['actions']['save']['initialSources'] = ['defaults', 'personal'];
        foreach (['my-settings', 'settings'] as $key) $sections[$key]['actions']['save']['strictFields'] = true;
        $sections['settings']['actions']['save']['fields'][] = $this->f('fontWeight', 'وزن قلم', 'select', ['0' => 'عادی', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵']);
        $sections['settings']['actions']['save']['fields'][] = $this->f('cornerRadius', 'گردی گوشه‌ها', 'number');
    }

    private function userAccessContracts(array &$sections): void
    {
        foreach (['roles', 'permissions'] as $key) {
            foreach (['create', 'update'] as $operation) {
                $sections[$key]['actions'][$operation]['fields'][] = $this->f('panelVisible', 'نمایش در پنل آموزشگاه و شعبه', 'bool') + ['en' => 'Visible in academy and branch panels'];
            }
        }
        // Save roles and direct grants independently, just like the web panel.
        // Keep the legacy operation callable by already installed clients.
        $sections['users']['actions']['update']['hidden'] = true;
        foreach (['roles' => ['roleIds', 'نقش‌ها', 'Roles', 'roles'], 'permissions' => ['permissionIds', 'دسترسی‌های مستقل', 'Direct permissions', 'permissions']] as $operation => [$key, $label, $en, $source]) {
            $options = ['source' => $source];
            if ($operation === 'permissions') $options['excludeSource'] = 'record.rolePermissionIds';
            $field = $this->f($key, $label, 'multi', $options) + ['initial' => $source, 'en' => $en];
            $sections['users']['actions'][$operation] = $this->a('/analytics/admin-user-access/{id}', $label, 'POST', [$field]) + ['en' => $en, 'permissionField' => 'canEdit'];
        }
        $sections['users']['filters'] = [
            $this->f('branchId', 'شعبه', 'select', ['source' => 'branches']),
            $this->f('organization', 'محدوده', 'select', ['academy' => 'خود آموزشگاه']),
            $this->f('status', 'وضعیت', 'select', ['pending' => 'در انتظار', 'approved' => 'تأییدشده', 'rejected' => 'ردشده', 'inactive' => 'غیرفعال', 'banned' => 'مسدود']),
        ];
    }

    private function classroomContracts(array &$sections): void
    {
        foreach (['create', 'update'] as $operation) {
            $fields = &$sections['classrooms']['actions'][$operation]['fields'];
            foreach ($fields as &$field) {
                if ($field['key'] === 'branchId') $field = $this->f('organizationUserId', 'آموزشگاه یا شعبه', 'select', ['source' => 'organizations', 'id' => 'user_id'], true);
            }
            unset($field, $fields);
        }
        $sections['classroom-types']['actions']['create-from-classroom'] = $this->a('/academy/admin/classroom-types/from-classroom', 'افزودن نوع کلاس هنگام ساخت کلاس', 'POST', $sections['classroom-types']['actions']['create']['fields'], false) + ['en' => 'Create classroom type from classroom', 'hidden' => true];
        $sections['lessons']['actions']['create-lesson'] = $this->a('/academy/admin/branch-offerings/lesson-catalog', 'افزودن درس به فهرست', 'POST', [$this->f('title', 'عنوان درس', 'text', null, true)], false) + ['en' => 'Add lesson to catalog'];
    }

    private function siteContentContracts(array &$sections): void
    {
        $sections['posts']['actions']['details'] = $this->a('/analytics/admin-posts/{id}', 'مشاهده نوشته', 'GET') + ['en' => 'View post'];
        $sections['posts']['actions']['update']['recordAction'] = 'details';
        $translationFields = [$this->f('key', 'کلید ترجمه', 'text', null, true), $this->f('fa', 'متن فارسی', 'multiline', null, true), $this->f('en', 'متن انگلیسی', 'multiline', null, true)];
        $sections['inline-translations'] = $this->s('inline-translations', 'ترجمه‌های رابط سایت', 'Website interface translations', '/analytics/admin-inline-translations', 'translations', [], 'admin', false);
        $sections['inline-translations']['rowId'] = 'key';
        $sections['inline-translations']['rowTitle'] = 'fa';
        $sections['inline-translations']['actions']['update'] = $this->a('/analytics/admin-inline-translations/save', 'ویرایش ترجمه', 'POST', $translationFields);
        $sections['inline-translations']['actions']['create'] = $this->a('/analytics/admin-inline-translations/save', 'افزودن ترجمه', 'POST', $translationFields, false);
    }

    private function scheduleQueryContracts(array &$sections): void
    {
        $sections['terms']['actions']['available-times'] = $this->a('/academy/admin/term-available-times', 'ساعت‌های آزاد', 'GET', [
            $this->f('branch', 'شعبه', 'select', ['source' => 'branches']) + ['initial' => 'branchId'],
            $this->f('classroom', 'کلاس', 'select', ['source' => 'classrooms']) + ['initial' => 'classroomId'],
            $this->f('date', 'تاریخ', 'date', null, true), $this->f('duration', 'مدت جلسه به دقیقه', 'number', null, true),
            $this->f('teacher', 'شناسه عضویت مدرس', 'number'), $this->f('excludeTerm', 'ترم مستثنا', 'number') + ['initial' => 'id'],
            $this->f('organizationKind', 'نوع سازمان', 'select', ['branch' => 'شعبه', 'academy' => 'آموزشگاه']),
            $this->f('organizationUserId', 'شناسه حساب آموزشگاه', 'number'),
        ]) + ['queryFields' => true, 'en' => 'Available session times', 'hidden' => true];
        $sections['schedules']['actions']['week-bounds'] = $this->a('/academy/admin/class-schedule-week-bounds', 'بازه هفتگی', 'GET', [$this->f('branch', 'شعبه', 'select', ['source' => 'branches'])], false) + ['queryFields' => true, 'en' => 'Weekly schedule bounds'];
        $sections['schedules']['actions']['week-bounds']['hidden'] = true;
        $sections['schedules']['actions']['cancel'] = $this->a('/academy/admin/terms/{termId}/sessions/{id}/cancel', 'لغو و جبرانی', 'POST', $sections['terms']['actions']['cancel']['fields']) + ['en' => 'Cancel and reschedule'];
        $sections['schedules']['actions']['term-context'] = $this->a('/academy/admin/terms', 'اطلاعات ترم', 'GET', [], false) + ['hidden' => true];
        $sections['schedules']['actions']['available-times'] = $sections['terms']['actions']['available-times'];
        $sections['schedules']['actions']['available-times']['row'] = false;
    }

    private function holidayContracts(array &$sections): void
    {
        $sections['national-holidays']['access'] = 'management';
        foreach (['create', 'update', 'delete'] as $operation) {
            $sections['national-holidays']['actions'][$operation]['access'] = 'admin';
        }
        foreach (['create', 'update'] as $operation) {
            $sections['national-holidays']['actions'][$operation]['fields'] = [
                $this->f('title', 'عنوان', 'text', null, true), $this->f('date', 'تاریخ', 'date', null, true),
                $this->f('description', 'توضیحات', 'multiline'), $this->f('status', 'وضعیت', 'select', ['active' => 'فعال', 'inactive' => 'غیرفعال']),
            ];
        }
        $sections['national-holidays']['actions']['status'] = $this->a('/academy/admin/national-holidays/{id}/status', 'تغییر وضعیت تعطیلی') + ['access' => 'admin'];
        $sections['national-holidays']['actions']['academy-setting'] = $this->a('/academy/admin/national-holiday-settings/{academyId}', 'برگزاری کلاس در تعطیلات رسمی', 'POST', [
            $this->f('academyId', 'آموزشگاه', 'select', ['source' => 'academies'], true), $this->f('allowClasses', 'اجازه برگزاری کلاس', 'bool'),
        ], false) + ['params' => ['academyId' => 'academyId'], 'en' => 'Classes on national holidays'];

    }

    private function waitingApprovalContracts(array &$sections): void
    {
        $approvalFields = [
            $this->f('name', 'نام و نام خانوادگی', 'text', null, true) + ['initial' => 'studentName'],
            $this->f('national_id', 'کد ملی', 'text', null, true) + ['initial' => 'nationalId'],
            $this->f('father_name', 'نام پدر', 'text', null, true) + ['initial' => 'fatherName'],
            $this->f('birth_date', 'تاریخ تولد', 'date', null, true) + ['initial' => 'birthDate'],
            $this->f('phone', 'شماره تماس', 'phone', null, true), $this->f('address', 'نشانی', 'multiline', null, true),
        ];
        foreach (['waiting-approvals' => ['تأیید ثبت‌نام هنرجویان', 'Student registration approvals', 'student_waiting'], 'waiting-schedules' => ['نهایی‌کردن ثبت‌نام هنرجویان', 'Finalize student registrations', 'student_pending_schedule']] as $key => [$label, $en, $type]) {
            $sections[$key] = $this->s($key, $label, $en, '/analytics/admin-dashboard', 'actionItems', [], 'management', false);
            $sections[$key]['where'] = ['type' => $type];
            $sections[$key]['rowId'] = 'requestId';
            $sections[$key]['rowTitle'] = 'studentName';
            $sections[$key]['rowSubtitle'] = 'termName';
            $sections[$key]['actions']['chat'] = $this->a('/analytics/admin-dashboard/waiting/{id}/chat', 'ایجاد گفتگوی هماهنگی') + ['en' => 'Create coordination conversation'];
        }
        $sections['waiting-approvals']['actions']['approve'] = $this->a('/analytics/admin-dashboard/waiting/{id}/approve', 'تأیید و ثبت هنرجو', 'POST', $approvalFields) + ['en' => 'Approve student registration'];
        $sections['my-waiting'] = $this->s('my-waiting', 'درخواست‌های ثبت‌نام من', 'My registration requests', '/analytics/personal-data/waiting', 'items', [], 'common', false);
        $sections['my-waiting']['actions']['update'] = $this->a('/analytics/my-waiting/{id}', 'ویرایش درخواست ثبت‌نام', 'POST', [...$approvalFields, $this->f('note', 'توضیح درخواست', 'multiline')]);
    }

    private function waitingFinalizationContracts(array &$sections): void
    {
        $sections['waiting-schedules']['actions']['finalize'] = $this->a('/analytics/admin-dashboard/waiting/{id}/finalize', 'فعال‌سازی کلاس و صدور فاکتور', 'POST', [
            $this->f('teacher_id', 'مدرس', 'select', ['source' => 'record.teachers'], true),
            $this->f('session_id', 'جلسه شروع', 'select', ['source' => 'record.sessions'], true) + ['visibleWhen' => ['record.needsFirstDate' => false]],
            $this->f('first_date', 'روز شروع', 'date', null, true) + ['visibleWhen' => ['record.needsFirstDate' => true]],
            $this->f('start_time', 'ساعت شروع', 'time', null, true) + ['visibleWhen' => ['record.needsFirstDate' => true]],
        ]) + ['en' => 'Activate class and issue invoice'];
    }

    private function authorizedActions(array $sections): array
    {
        // Only registered operations are exposed; no synthetic CRUD or HTML routes.
        foreach ($sections as &$section) {
            $path = $section['actions']['create']['path'] ?? $section['actions']['list']['path'];
            if ($section['access'] === 'management' && isset($section['actions']['update']) && !isset($section['actions']['status'])) {
                $section['actions']['status'] = $this->a($path . '/{id}/status', 'تغییر وضعیت');
            }
            $section['actions'] = array_filter($section['actions'], fn ($action) => Router::dispatch($action['method'], preg_replace('/\{\w+\}/', '1', $action['path'])) !== null);
            foreach ($section['actions'] as &$action) {
                $route = Router::dispatch($action['method'], preg_replace('/\{\w+\}/', '1', $action['path']));
                if (in_array('site-admin', $route['middlewares'], true)) {
                    $action['admin'] = true;
                }
            }unset($action);
        } unset($section);
        foreach (['posts', 'post-categories', 'comments', 'media', 'pages', 'page-content', 'settings'] as $key) {
            $sections[$key]['access'] = 'admin';
        }
        return $sections;
    }

    private function profileField(string $key): array
    {
        $labels = ['title' => 'عنوان', 'issuer' => 'صادرکننده', 'date' => 'تاریخ', 'description' => 'توضیحات', 'issue_date' => 'تاریخ صدور', 'expire_date' => 'تاریخ انقضا', 'certificate_url' => 'نشانی گواهی', 'file_path' => 'مسیر فایل', 'organization' => 'سازمان', 'start_date' => 'شروع', 'end_date' => 'پایان', 'address_id' => 'نشانی', 'degree' => 'مدرک', 'institution' => 'مؤسسه', 'field' => 'رشته', 'event_date' => 'زمان رویداد', 'event_type' => 'نوع رویداد', 'publisher' => 'ناشر', 'published_date' => 'تاریخ انتشار', 'url' => 'نشانی اینترنتی', 'content' => 'متن', 'is_peer_reviewed' => 'داوری علمی', 'verification_level_id' => 'نشان', 'granted_at' => 'تاریخ اعطا', 'expires_at' => 'تاریخ انقضا', 'status' => 'وضعیت', 'question' => 'پرسش', 'type' => 'نوع', 'is_anonymous' => 'ناشناس', 'options' => 'گزینه‌ها'];
        $type = in_array($key, ['is_peer_reviewed', 'is_anonymous'], true) ? 'bool' : (str_contains($key, 'date') || str_ends_with($key, '_at') ? 'date' : (in_array($key, ['content', 'description'], true) ? 'multiline' : 'text'));
        $options = null;
        if ($key === 'options') {
            $type = 'strings';
        }
        if ($key === 'address_id' || $key === 'verification_level_id') {
            $type = 'select';
            $options = ['source' => $key === 'address_id' ? 'addresses' : 'levels'];
        }
        if ($key === 'event_type') {
            $type = 'select';
            $options = ['concert' => 'کنسرت', 'festival' => 'جشنواره', 'competition' => 'مسابقه', 'workshop' => 'کارگاه', 'other' => 'سایر'];
        }
        return $this->f($key, $labels[$key] ?? $key, $type, $options);
    }
}
