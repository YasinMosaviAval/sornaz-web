<?php

trait AdminCreateQueriesTrait {

/*
    public function add_new_branch() {

        // dump($_POST);
        // exit();

        $email = post('email');
        // show registration form if email not provided
        if ($email == null) {
            $this->academyBranches();
            return;
        }
        
        // check registration info and register if is valid information
        $username = post('username');
        $password1 = post('password1');
        $password2 = post('password2');add_setting
        $time = getCurrentDateTime();
        
        // $record = AccountModel::fetch_by_email($email);
        $record = PageModel::fetch_by_email_in_users($email);
        if ($record != null){
            message('fail', 'شما پیشتر ثبت نام کرده اید، کافیست وارد سایت شوید', true);
        }

        if (strlen($password1)<3 || strlen($password2)<3){
            message('fail', 'پسورد به اندازه کافی قوی نمی باشد', true);
        }

        if ($password1 != $password2){
            message('fail', 'پسورد ها با هم مطابقت ندارند', true);
        }

        $password = encryptPassword($password1);


        global $config;
        $locale = $config['app']['lang'] ?? 'fa';

        $title = post('fullname');
        $brief = post('brief');
        $description = post('biography');
        

        $created_by = post('manager_id');
        $academy_branch_type_id = post('academy_branch_type_id');
        $timezone = post('timezone');
        $is_main = post('is_main') == 'on' ? 1 : 0;
        $mode = post('mode');
        $academy_id = $this->get_academy_id_by_user_id(session_get('user_id'));
        $gender = 'branch';
        $table_name = 'users';

        $model = new pageModel();
        $user_id = $model->insert_new_users($email, $username, $password, $gender, $time);
        $model->insert_new_translation($table_name, $user_id, $locale, $title, $brief, $description);
        $branch_id = $model->insert_new_academy_branch($academy_id, $user_id, $timezone, $is_main, $academy_branch_type_id, $mode);

        $this->academyBranches();
    }
*/







// ===================== academies =====================
// public function add_new_academy() {
//     global $config;
//     $locale = $config['app']['lang'] ?? 'fa';
    
//     $user_id         = post('user_id');
//     $academy_type_id = post('academy_type_id');
//     $title           = post('title');
//     $brief           = post('brief');
//     $description     = post('description');

//     $table_name = 'academies';
//     $model = new pageModel();
    
//     $id = $model->insert_new_academy($user_id, $academy_type_id);
//     $model->insert_new_translation($table_name, $id, $locale, $title, $brief, $description);
    
//     $this->academies();
// }

// ===================== academy_branches =====================
// public function add_new_academy_branch() {
//     global $config;
//     $locale = $config['app']['lang'] ?? 'fa';
    
//     $academy_id = post('academy_id');
//     $user_id    = post('user_id');
//     $is_main    = post('is_main') ?? 0;
//     $mode       = post('mode');
//     $timezone   = post('timezone');
//     $title      = post('title');
//     $brief      = post('brief');
//     $description= post('description');

//     $table_name = 'academy_branches';
//     $model = new pageModel();
    
//     $id = $model->insert_new_academy_branch($academy_id, $user_id, $is_main, $mode, $timezone);
//     $model->insert_new_translation($table_name, $id, $locale, $title, $brief, $description);
    
//     $this->academyBranches();
// }

// ===================== academy_branches =====================
public function add_new_academy_branch_type() {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';
    
    $type        = post('type');
    $title       = post('title');
    $brief       = post('brief');
    $description = post('description');

    $table_name = 'academy_branch_types';
    $model = new pageModel();
    
    $id = $model->insert_new_academy_branch_type($type);
    $model->insert_new_translation($table_name, $id, $locale, $title, $brief, $description);
    
    $this->academyTypes();
}

// ===================== academy_branch_bookings =====================
// public function add_new_academy_branch_booking() {
//     global $config;
//     $locale = $config['app']['lang'] ?? 'fa';
    
//     $student_id     = post('student_id');
//     $teacher_id     = post('teacher_id');
//     $branch_id      = post('branch_id');
//     $requested_date = post('requested_date');
//     $start_time     = post('start_time');
//     $end_time       = post('end_time');
//     $status         = post('status') ?? 'pending';
//     $source         = post('source');
//     $note           = post('note');
//     $title          = post('title');
//     $brief          = post('brief');
//     $description    = post('description');

//     $table_name = 'academy_branch_bookings';
//     $model = new pageModel();
    
//     $id = $model->insert_new_academy_branch_booking($student_id, $teacher_id, $branch_id, $requested_date, $start_time, $end_time, $status, $source, $note);
//     $model->insert_new_translation($table_name, $id, $locale, $title, $brief, $description);
    
//     $this->academyBranchBookings();
// }

// ===================== academy_branch_classroom_types =====================
public function add_new_academy_branch_classroom_type() {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';
    
    $branch_id  = $this->get_branch_id_by_user_id(post('branch_id'));
    $title      = post('title');
    $brief      = post('brief');
    $description= post('description');
    
    $table_name = 'academy_branch_classroom_types';
    $model = new pageModel();

    $id = $model->insert_new_academy_branch_classroom_types($branch_id);
    $model->insert_new_translation($table_name, $id, $locale, $title, $brief, $description);
    
    $this->academyClassroomTypes();
}

// ===================== academy_branch_classrooms =====================
public function add_new_academy_branch_classroom() {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';
    
    $branch_id  = $this->get_branch_id_by_user_id(post('branch_id'));
    $type_id    = post('type_id');
    $capacity   = post('capacity');
    $is_active  = post('is_active') ?? 0;
    $status     = post('status') ?? 'pending';
    $title      = post('title');
    $brief      = post('brief');
    $description= post('description');

    $table_name = 'academy_branch_classrooms';
    $model = new pageModel();
    
    $id = $model->insert_new_academy_branch_classroom($branch_id, $type_id, $capacity, $is_active, $status);
    $model->insert_new_translation($table_name, $id, $locale, $title, $brief, $description);
    
    $this->academyClassrooms();
}

// ===================== academy_branch_classroom_assets =====================
public function add_new_academy_branch_classroom_asset() {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';
    
    $classroom_id = post('classroom_id');
    $quantity     = post('quantity');
    $title        = post('title');
    $brief        = post('brief');
    $description  = post('description');

    $table_name   = 'academy_branch_classroom_assets';
    $model = new pageModel();

    $id = $model->insert_new_academy_branch_classroom_asset($classroom_id, $quantity);
    $model->insert_new_translation($table_name, $id, $locale, $title, $brief, $description);
    
    $this->academyClassroomAssets();
}

// ===================== academy_branch_courses =====================
public function add_new_academy_branch_course() {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';
    
    $branch_id  = post('branch_id');
    $level_id   = post('level_id');
    $capacity   = post('capacity');
    $title      = post('title');
    $brief      = post('brief');
    $description= post('description');

    $table_name = 'academy_branch_courses';
    $model = new pageModel();
    
    $id = $model->insert_new_academy_branch_course($branch_id, $level_id, $capacity);
    $model->insert_new_translation($table_name, $id, $locale, $title, $brief, $description);
    
    $this->academyCourses();
}

// ===================== academy_branch_course_terms =====================
public function add_new_academy_branch_course_term() {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';
    
    $course_id     = post('course_id');
    $start_date    = post('start_date');
    $end_date      = post('end_date');
    $session_count = post('session_count');
    $price         = post('price');
    $currency_id   = post('currency_id');
    // $status        = post('status') ?? 'open';

    $title      = post('title');
    $brief      = post('brief');
    $description= post('description');

    $table_name = 'academy_branch_course_terms';
    $model = new pageModel();

    $id = $model->insert_new_academy_branch_course_term($course_id, $start_date, $end_date, $session_count, $price, $currency_id);
    $model->insert_new_translation($table_name, $id, $locale, $title, $brief, $description);


    $this->academyTerms();
}

// ===================== academy_branch_course_term_enrollments =====================
public function add_new_academy_branch_course_term_enrollment() {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';
    
    $term_id        = post('term_id');
    $member_id      = post('member_id');
    $type           = post('type');
    $joined_at      = post('joined_at');
    
    $discount_id    = post('discount_id');

    $payable_amount = post('payable_amount');
    $currency_id    = post('currency_id');
    $due_date       = post('due_date');
    $issued_at      = post('issued_at');

    $requested_date = post('requested_date');
    $start_time     = post('start_time');
    $end_time       = post('end_time');

    $title      = post('title');
    $brief      = post('brief');
    $description= post('description');

    $table_name = 'academy_branch_course_term_enrollments';
    $model = new pageModel();

    $enrollment_id = $model->insert_new_academy_branch_course_term_enrollment($term_id, $member_id, $type, $joined_at);
    $invoice_id = $model->insert_new_academy_branch_course_term_invoice($term_id, $member_id, $discount_id, $payable_amount, $currency_id, $issued_at, $due_date);

    $model->insert_new_academy_branch_booking($requested_date, $start_time, $end_time);

    $model->insert_new_translation($table_name, $enrollment_id, $locale, $title, $brief, $description);

    $this->academyEnrollStudents();
}

// ===================== academy_branch_course_term_enrollments =====================
public function add_new_academy_branch_course_term_enrollment_teacher() {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';
    
    $term_id        = post('term_id');
    $member_id      = post('member_id');
    $type           = post('type');
    $joined_at      = post('joined_at');

    $requested_date = post('requested_date');
    $start_time     = post('start_time');
    $end_time       = post('end_time');

    $title      = post('title');
    $brief      = post('brief');
    $description= post('description');

    $table_name = 'academy_branch_course_term_enrollments';
    $model = new pageModel();

    $enrollment_id = $model->insert_new_academy_branch_course_term_enrollment($term_id, $member_id, $type, $joined_at);
    $model->insert_new_academy_branch_booking($requested_date, $start_time, $end_time);

    $model->insert_new_translation($table_name, $enrollment_id, $locale, $title, $brief, $description);

    $this->academyEnrollTeachers();
}

// ===================== financial_system_discounts =====================
public function add_new_financial_system_discount() {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';

    $discount_type  = post('discount_type');
    $value          = post('value');
    $max_usage      = post('max_usage');
    // $used_count     = post('used_count');
    $start_date     = post('start_date');
    $end_date       = post('end_date');

    $title      = post('title');
    $brief      = post('brief');
    $description= post('description');

    $table_name = 'financial_system_discounts';
    $model = new pageModel();

    $discount_id = $model->insert_new_financial_system_discount($discount_type, $value, $max_usage, $start_date, $end_date);
    $model->insert_new_translation($table_name, $discount_id, $locale, $title, $brief, $description);

    
    $this->academyDiscounts();
}



// ===================== academy_branch_course_term_invoices =====================
// public function add_new_academy_branch_course_term_invoice() {
//     global $config;
//     $locale = $config['app']['lang'] ?? 'fa';
    
//     $user_id        = post('user_id');
//     $branch_id      = post('branch_id');
//     $term_id        = post('term_id');
//     $total_amount   = post('total_amount');
//     $payable_amount = post('payable_amount');
//     $currency_id    = post('currency_id');
//     $status         = post('status') ?? 'draft';
//     $due_date       = post('due_date');
//     $title          = post('title');
//     $brief          = post('brief');
//     $description    = post('description');

//     $table_name = 'academy_branch_course_term_invoices';
//     $model = new pageModel();
    
//     $id = $model->insert_new_academy_branch_course_term_invoice($user_id, $branch_id, $term_id, $total_amount, $payable_amount, $currency_id, $status, $due_date);
//     $model->insert_new_translation($table_name, $id, $locale, $title, $brief, $description);
    
//     $this->academyBranchCourseTermInvoices();
// }

// ===================== academy_branch_course_term_invoice_installments =====================
public function add_new_academy_branch_course_term_invoice_installment() {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';

    $invoice_id         = post('invoice_id');
    $installment_number = post('installment_number');
    $amount             = post('amount');
    $due_date           = post('due_date');
    $status             = post('status') ?? 'pending';

    $title              = post('title');
    $brief              = post('brief');
    $description        = post('description');

    $table_name = 'academy_branch_course_term_invoice_installments';
    $model = new pageModel();

    $term_invoice_installment_id = $model->insert_new_academy_branch_course_term_invoice_installment($invoice_id, $installment_number, $amount, $due_date, $status);
    $model->insert_new_translation($table_name, $term_invoice_installment_id, $locale, $title, $brief, $description);

    $this->invoiceInstallments($invoice_id);
}



// ===================== academy_branch_course_term_sessions =====================
public function add_new_academy_branch_course_term_session() {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';

    $term_id        = post('term_id');
    $classroom_id   = post('classroom_id');
    $branch_url_id  = post('branch_url_id');
    // $status         = post('status') ?? 'scheduled';

    $requested_date = post('requested_date');
    $start_time     = post('start_time');
    $end_time       = post('end_time');

    $title          = post('title');
    $brief          = post('brief');
    $description    = post('description');

    $table_name = 'academy_branch_course_term_sessions';
    $model = new pageModel();

    $booking_id = $model->insert_new_academy_branch_booking($requested_date, $start_time, $end_time);
    $term_session_id = $model->insert_new_academy_branch_course_term_session($term_id, $booking_id, $classroom_id, $branch_url_id);

    $model->insert_new_translation($table_name, $term_session_id, $locale, $title, $brief, $description);

    $this->academySessions();
}

// ===================== academy_branch_course_term_session_attendances =====================
public function add_new_academy_branch_course_term_session_attendance() {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';

    $session_id        = post('session_id');

    $table_name = 'academy_branch_course_term_session_attendances';
    $model = new pageModel();

    foreach ($_POST as $key => $value) {
        if (strpos($key, 'term_enrollment_id_') === 0) {
            $index = str_replace('term_enrollment_id_', '', $key);
            $term_enrollment_id = post('term_enrollment_id_' . $index);
            $member_id = post('member_id_' . $index);
            $status = post('status_' . $index);
            $title = post('title_' . $index);
            $brief = post('brief_' . $index);
            $description = post('description_' . $index);

            $session_attendance_id = $model->insert_new_academy_branch_course_term_session_attendance($session_id, $term_enrollment_id, $member_id, $status);
            $model->insert_new_translation($table_name, $session_attendance_id, $locale, $title, $brief, $description);
        }
    }

    $this->sessionAttendances(post('session_id'));
}

// ===================== academy_branch_course_term_session_classrooms =====================
public function add_new_academy_branch_course_term_session_classroom() {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';

    $session_id   = post('session_id');
    $classroom_id = post('classroom_id');

    $title        = post('title');
    $brief        = post('brief');
    $description  = post('description');

    $table_name = 'academy_branch_course_term_session_classrooms';
    $model = new pageModel();

    $term_session_classroom_id = $model->insert_new_academy_branch_course_term_session_classroom($session_id, $classroom_id);
    $model->insert_new_translation($table_name, $term_session_classroom_id, $locale, $title, $brief, $description);

    $this->sessionClassrooms(post('session_id'));
}

// ===================== academy_branch_course_term_teachers =====================
// public function add_new_academy_branch_course_term_teacher() {
//     $term_id           = post('term_id');
//     $academy_member_id = post('academy_member_id');

//     $model = new pageModel();
//     $model->insert_new_academy_branch_course_term_teacher($term_id, $academy_member_id);
    
//     $this->academyBranchCourseTermTeachers();
// }

// ===================== user_availabilities =====================
public function add_new_user_availability() {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';

    $user_id       = post('member_user_id');
    $date          = post('date');
    $day_of_week   = post('day_of_week');
    $timezone      = post('timezone');
    $start_time    = post('start_time');
    $end_time      = post('end_time');
    $type          = post('type');
    $is_repeating  = post('is_repeating') == "on" ? 1 : 0;;
    $repeat_period = post('repeat_period');

    $title         = post('title');
    $brief         = post('brief');
    $description   = post('description');

    $table_name = 'user_availabilities';
    $model = new pageModel();

    $id = $model->insert_new_user_availability($user_id, $date, $day_of_week, $timezone, $start_time, $end_time, $type, $is_repeating, $repeat_period);
    $model->insert_new_translation($table_name, $id, $locale, $title, $brief, $description);

    $this->academyUserSchedulings();
}

// ===================== user_availability_exceptions =====================
public function add_new_user_availability_exception() {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';
    
    $user_id       = post('member_user_id');
    $date          = post('date');
    $start_time    = post('start_time');
    $end_time      = post('end_time');
    $type          = post('type');

    $title         = post('title');
    $brief         = post('brief');
    $description   = post('description');

    $table_name = 'user_availability_exceptions';
    $model = new pageModel();

    $id = $model->insert_new_user_availability_exceptions($user_id, $date, $start_time, $end_time, $type);
    $model->insert_new_translation($table_name, $id, $locale, $title, $brief, $description);

    $this->userAvailabilityExceptions();
}

// ===================== academy_branch_course_term_waiting_list =====================
public function add_new_academy_branch_course_term_waiting_list() {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';

    $term_id    = post('term_id');
    $member_id  = post('member_id');

    $title         = post('title');
    $brief         = post('brief');
    $description   = post('description');

    $table_name = 'academy_branch_course_term_waiting_list';
    $model = new pageModel();
    $id = $model->insert_new_academy_branch_course_term_waiting_list($term_id, $member_id);
    $model->insert_new_translation($table_name, $id, $locale, $title, $brief, $description);

    $this->academyWaitingList();
}

// ===================== academy_branch_members =====================
public function add_new_academy_branch_member() {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';
    
    $branch_id  = post('branch_id');
    // $user_id    = post('user_id');
    $role_id    = post('role_id');
    $status     = post('status') ?? 'pending';
    $joined_at  = post('joined_at');
    $title      = post('title');
    $brief      = post('brief');
    $description= post('description');
    $gender     = post('gender');

    $table_name = 'users';
    $model = new pageModel();

    $user_count = self::get_last_id_from_users_table()[0]['last_id'] + 1;
    $username = 'user-' . $user_count;
    
    $user_id = $model->insert_new_user($username, $gender);
    $member_id = $model->insert_new_academy_branch_member($branch_id, $user_id, $role_id, $status, $joined_at);
    $model->insert_new_translation($table_name, $user_id, $locale, $title, $brief, $description);
    
    $this->academyUsers();
}

// ===================== academy_branch_member_contracts =====================
public function add_new_academy_branch_member_contract() {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';
    
    $member_id   = post('member_id');
    $type        = post('type');
    $start_date  = post('start_date');
    $end_date    = post('end_date');
    $price       = post('price');
    $currency_id = post('currency_id');
    $title       = post('title');
    $brief       = post('brief');
    $description = post('description');

    $table_name = 'academy_branch_member_contracts';
    $model = new pageModel();
    
    $id = $model->insert_new_academy_branch_contract($member_id, $type, $start_date, $end_date, $price, $currency_id);
    $model->insert_new_translation($table_name, $id, $locale, $title, $brief, $description);
    
    $this->academyContracts();
}

// ===================== access_system_roles =====================
public function add_new_access_system_role() {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';
    
    $name       = post('name');
    $type       = post('type');
    $color      = post('color');
    $sort_order = post('sort_order');
    $title      = post('title');
    $title_en   = post('title_en');

    $table_name = 'access_system_roles';
    $model = new pageModel();
    
    $role_id = $model->insert_new_access_system_role($name, $type, $color, $sort_order);
    $model->insert_new_title_translation($table_name, $role_id, $locale, $title);
    $model->insert_new_title_translation($table_name, $role_id, 'en', $title_en);
    
    
    $this->addRole();
}

// ===================== access_system_permissions =====================
public function add_new_access_system_permission() {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';
    
    $group_name     = post('group_name');
    $name           = post('name');
    $title          = post('title');
    $title_en       = post('title_en');
    $name_1         = post('name_1');
    $title_1        = post('title_1');
    $title_en_1     = post('title_en_1');
    $name_2         = post('name_2');
    $title_2        = post('title_2');
    $title_en_2     = post('title_en_2');
    $name_3         = post('name_3');
    $title_3        = post('title_3');
    $title_en_3     = post('title_en_3');

    $table_name = 'access_system_permissions';
    $model = new pageModel();
    
    $permission_id = $model->insert_new_access_system_permission($name, $group_name);
    $permission_id_1 = $model->insert_new_access_system_permission($name_1, $group_name);
    $permission_id_2 = $model->insert_new_access_system_permission($name_2, $group_name);
    $permission_id_3 = $model->insert_new_access_system_permission($name_3, $group_name);
    $model->insert_new_title_translation($table_name, $permission_id, $locale, $title);
    $model->insert_new_title_translation($table_name, $permission_id, 'en', $title_en);
    $model->insert_new_title_translation($table_name, $permission_id_1, $locale, $title_1);
    $model->insert_new_title_translation($table_name, $permission_id_1, 'en', $title_en_1);
    $model->insert_new_title_translation($table_name, $permission_id_2, $locale, $title_2);
    $model->insert_new_title_translation($table_name, $permission_id_2, 'en', $title_en_2);
    $model->insert_new_title_translation($table_name, $permission_id_3, $locale, $title_3);
    $model->insert_new_title_translation($table_name, $permission_id_3, 'en', $title_en_3);
    
    
    $this->addPermission();
}


// ===================== access_system_setting_permissions =====================
public function add_new_access_system_setting_permission() {

    // dump($_POST);
    // exit();

    global $config;
    $locale = $config['app']['lang'] ?? 'fa';
    
    $setting_id  = post('setting_id');
    $permissions = post('permissions');

    $title       = post('title') ?? '';
    $brief       = post('brief') ?? '';
    $description = post('description') ?? '';

    $table_name = 'access_system_setting_permissions';
    $model = new pageModel();

    foreach ($permissions as $permission_id) {
        $setting_permission_id = $model->insert_new_access_system_setting_permission($setting_id, $permission_id);
        $model->insert_new_translation($table_name, $setting_permission_id, $locale, $title, $brief, $description);
    }

    $this->addSettingPermission();
}


// ===================== access_system_role_permissions =====================
public function add_new_access_system_role_permission() {

    // dump($_POST);
    // exit();

    global $config;
    $locale = $config['app']['lang'] ?? 'fa';
    
    $role_id     = post('role_id');
    $permissions = post('permissions');

    $title       = post('title') ?? '';
    $brief       = post('brief') ?? '';
    $description = post('description') ?? '';

    $table_name = 'access_system_role_permissions';
    $model = new pageModel();

    foreach ($permissions as $permission_id) {
        $role_permission_id = $model->insert_new_access_system_role_permission($role_id, $permission_id);
        $model->insert_new_translation($table_name, $role_permission_id, $locale, $title, $brief, $description);
    }

    $this->addRolePermission();
}


// ===================== academy_branch_member_permissions =====================
// public function add_new_academy_branch_member_permission() {
//     $academy_member_id = post('academy_member_id');
//     $permission_id     = post('permission_id');

//     $model = new pageModel();
//     $model->insert_new_academy_branch_member_permission($academy_member_id, $permission_id);
    
//     $this->academyBranchMemberPermissions();
// }

// ===================== academy_branch_member_schedules =====================
// public function add_new_academy_branch_member_schedule() {
//     $academy_member_id = post('academy_member_id');
//     $branch_id         = post('branch_id');
//     $day_of_week       = post('day_of_week');
//     $start_time        = post('start_time');
//     $end_time          = post('end_time');
//     $availability_type = post('availability_type') ?? 'available';

//     $model = new pageModel();
//     $model->insert_new_academy_branch_member_schedule($academy_member_id, $branch_id, $day_of_week, $start_time, $end_time, $availability_type);
    
//     $this->academyBranchMemberSchedules();
// }

// ===================== user_contacts =====================
public function add_new_user_contact() {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';
    
    // $branch_id   = $this->get_branch_id_by_user_id(post('branch_id'));
    $user_id     = post('user_id');
    $value       = post('value');
    $mode        = post('mode');
    $platform    = post('platform');
    $priority    = post('priority');
    $is_main     = post('is_main') == 'on' ? 1 : 0;

    $title       = post('title');
    $brief       = post('brief');
    $description = post('description');

    $table_name = 'user_contacts';
    $model = new pageModel();
    
    $id = $model->insert_new_user_contact($user_id, $value, $mode, $platform, $priority, $is_main);
    $model->insert_new_translation($table_name, $id, $locale, $title, $brief, $description);
    
    $this->academyBranchUrls();
}


// ===================== user_addresses =====================
public function add_new_user_address() {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';
    
    // $branch_id            = $this->get_branch_id_by_user_id(post('branch_id'));
    $addresses_table_id   = post('addresses_table_id');
    $addresses_table_name = post('addresses_table_name');
    $country_id           = post('country_id');
    $state_id             = post('state_id');
    $city_id              = post('city_id');
    $latitude             = post('latitude') ?? 0.00;
    $longitude            = post('longitude') ?? 0.00;
    $postal_code          = post('postal_code');
    $is_main              = post('is_main') == 'on' ? 1 : 0;

    $title                = post('title');
    $brief                = post('brief');
    $description          = post('description');
    

    $text_1               = post('text_1');
    $subject_1            = post('subject_1');

    $table_name = 'user_addresses';
    $model = new pageModel();
    
    $id = $model->insert_new_user_address($addresses_table_id, $addresses_table_name, $country_id, $state_id, $city_id, $latitude, $longitude, $postal_code, $is_main);
    $model->insert_new_plus1_translation($table_name, $id, $locale, $title, $brief, $description, $subject_1, $text_1);
    
    $this->academyAddresses();
}


// ===================== user_experiences =====================
public function add_new_user_experience() {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';
    
    $user_id              = post('user_id');
    $start_date           = post('start_date');
    $end_date             = post('end_date');
    // $is_current           = post('is_current') == 'on' ? 1 : 0;
    
    $addresses_table_id   = post('user_id');
    $addresses_table_name = post('addresses_table_name');
    $country_id           = post('country_id');
    $state_id             = post('state_id');
    $city_id              = post('city_id');
    $latitude             = post('latitude') ?? 00.00;
    $longitude            = post('longitude') ?? 00.00;
    $postal_code          = post('postal_code');
    $is_main              = post('is_main') == 'on' ? 1 : 0;
    
    $text_1               = post('text_1');
    $subject_1            = post('subject_1');
    $text_2               = post('text_2');
    $subject_2            = post('subject_2');
    $title                = post('title');
    $brief                = post('brief');
    $description          = post('description');
    

    $table_name = 'user_addresses';
    $model = new pageModel();

    $address_id = $model->insert_new_user_address($addresses_table_id, $addresses_table_name, $country_id, $state_id, $city_id, $latitude, $longitude, $postal_code, $is_main);
    $model->insert_new_address_translation($table_name, $address_id, $locale, $subject_2, $text_2);
    
    $table_name = 'user_experiences';

    if($end_date == null) {
        $user_experience_id = $model->insert_new_user_experience_without_end_date($user_id, $address_id, $start_date);
    } else {
        $user_experience_id = $model->insert_new_user_experience_with_end_date($user_id, $address_id, $start_date, $end_date);
    }
    $model->insert_new_plus1_translation($table_name, $user_experience_id, $locale, $title, $brief, $description, $subject_1, $text_1);

    $this->userExperiences();
}


// ===================== user_events =====================
public function add_new_user_event() {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';

    dump($_POST);
    $user_id              = post('user_id');
    $event_type           = post('event_type');
    $event_date           = post('event_date');

    $addresses_table_id   = post('user_id');
    $addresses_table_name = post('addresses_table_name');
    $country_id           = post('country_id');
    $state_id             = post('state_id');
    $city_id              = post('city_id');
    $latitude             = post('latitude');
    $longitude            = post('longitude');
    $postal_code          = post('postal_code');
    $is_main              = post('is_main') == 'on' ? 1 : 0;

    $text_1               = post('text_1');
    $subject_1            = post('subject_1');
    $text_2               = post('text_2');
    $subject_2            = post('subject_2');
    $title                = post('title');
    $brief                = post('brief');
    $description          = post('description');
    

    $table_name = 'user_addresses';
    $model = new pageModel();

    if($longitude == null || $latitude == null) {
        $address_id = $model->insert_new_user_address_without_lat_long($addresses_table_id, $addresses_table_name, $country_id, $state_id, $city_id, $postal_code, $is_main);
    } else {
        $address_id = $model->insert_new_user_address_with_lat_long($addresses_table_id, $addresses_table_name, $country_id, $state_id, $city_id, $latitude, $longitude, $postal_code, $is_main);
    }
    $model->insert_new_address_translation($table_name, $address_id, $locale, $subject_2, $text_2);

    $table_name = 'user_events';
    $user_event_id = $model->insert_new_user_event($user_id, $address_id, $event_type, $event_date);
    $model->insert_new_plus1_translation($table_name, $user_event_id, $locale, $title, $brief, $description, $subject_1, $text_1);

    $this->userEvents();
}


// ===================== user_awards =====================
public function add_new_user_award() {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';

    $user_id              = post('user_id');
    $date                 = post('date');

    $title                = post('title');
    $brief                = post('brief');
    $description          = post('description');
    

    $text_1               = post('text_1');
    $subject_1            = post('subject_1');

    $table_name = 'user_awards';
    $model = new pageModel();

    $user_award_id = $model->insert_new_user_award($user_id, $date);
    $model->insert_new_plus1_translation($table_name, $user_award_id, $locale, $title, $brief, $description, $subject_1, $text_1);

    $this->userAwards();
}



// ===================== user_certificates =====================
public function add_new_user_certificate() {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';

    $user_id         = post('user_id');
    $issue_date      = post('issue_date');
    $expire_date     = post('expire_date');
    $certificate_url = post('certificate_url');
    $file_path       = $_FILES['file_path']['name'];

    $title           = post('title');
    $brief           = post('brief');
    $description     = post('description');

    $text_1          = post('text_1');
    $subject_1       = post('subject_1');

    $table_name = 'user_certificates';
    $model = new pageModel();

    if($expire_date == null) {
        $user_certificate_id = $model->insert_new_user_certificate_without_expire_date($user_id, $issue_date, $certificate_url, $file_path);
    } else {
        $user_certificate_id = $model->insert_new_user_certificate_with_expire_date($user_id, $issue_date, $expire_date, $certificate_url, $file_path);
    }
    $model->insert_new_plus1_translation($table_name, $user_certificate_id, $locale, $title, $brief, $description, $subject_1, $text_1);

    $this->userCertificates();
}


// ===================== user_educations =====================
public function add_new_user_education() {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';

    $user_id     = post('user_id');
    $start_date  = post('start_date');
    $end_date    = post('end_date');

    $title       = post('title');
    $brief       = post('brief');
    $description = post('description');

    $text_1      = post('text_1');
    $subject_1   = post('subject_1');
    $text_2      = post('text_2');
    $subject_2   = post('subject_2');
    $text_3      = post('text_3');
    $subject_3   = post('subject_3');

    $table_name = 'user_educations';
    $model = new pageModel();

    if($end_date == null) {
        $user_education_id = $model->insert_new_user_education_without_end_date($user_id, $start_date);
    } else {
        $user_education_id = $model->insert_new_user_education_with_end_date($user_id, $start_date, $end_date);
    }
    $model->insert_new_plus3_translation($table_name, $user_education_id, $locale, $title, $brief, $description, $subject_1, $text_1, $subject_2, $text_2, $subject_3, $text_3);

    $this->userEducations();
}


// ===================== user_instruments =====================
public function edit_user_instrument(int $user_instrument_id) {
    $this->soft_delete_user_instrument_by_id($user_instrument_id);
    $this->soft_delete_instrument_by_id($user_instrument_id);
    $this->add_new_user_instrument();
}


public function add_new_user_instrument() {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';

    $created_at          = post('created_at') ?? '';
    $created_by          = post('created_by') ?? 0;

    $user_id             = post('user_id');
    $level_id            = post('level_id');
    $years_of_experience = post('years_of_experience');
    $is_primary          = post('is_primary') == 'on' ? 1 : 0;

    $title               = post('title');
    $brief               = post('brief');
    $description         = post('description');

    $table_name = 'user_instruments';
    $model = new pageModel();

    if($created_at == '') {
        $instrument_id = $model->insert_new_instrument();
        $user_instrument_id = $model->insert_new_user_instrument($user_id, $instrument_id, $level_id, $years_of_experience, $is_primary);
        $model->insert_new_translation($table_name, $user_instrument_id, $locale, $title, $brief, $description);
    } else {
        $instrument_id = $model->insert_new_version_instrument($created_at, $created_by);
        $user_instrument_id = $model->insert_new_version_user_instrument($user_id, $instrument_id, $level_id, $years_of_experience, $is_primary, $created_at, $created_by);
        $model->insert_new_version_translation($table_name, $user_instrument_id, $locale, $title, $brief, $description, $created_at, $created_by);
    }

    $this->userInstruments();
}


// ===================== user_lessons =====================
public function edit_user_lesson(int $user_lesson_id) {
    $this->soft_delete_user_lesson_by_id($user_lesson_id);
    $this->soft_delete_lesson_by_id($user_lesson_id);
    $this->add_new_user_lesson();
}


public function add_new_user_lesson() {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';

    $created_at          = post('created_at') ?? '';
    $created_by          = post('created_by') ?? 0;

    $user_id             = post('user_id');
    $level_id            = post('level_id');
    $years_of_experience = post('years_of_experience');
    $is_primary          = post('is_primary') == 'on' ? 1 : 0;

    $title               = post('title');
    $brief               = post('brief');
    $description         = post('description');

    $table_name = 'user_lessons';
    $model = new pageModel();

    if($created_at == '') {
        $lesson_id = $model->insert_new_lesson();
        $user_lesson_id = $model->insert_new_user_lesson($user_id, $lesson_id, $level_id, $years_of_experience, $is_primary);
        $model->insert_new_translation($table_name, $user_lesson_id, $locale, $title, $brief, $description);
    } else {
        $lesson_id = $model->insert_new_version_lesson($created_at, $created_by);
        $user_lesson_id = $model->insert_new_version_user_lesson($user_id, $lesson_id, $level_id, $years_of_experience, $is_primary, $created_at, $created_by);
        $model->insert_new_version_translation($table_name, $user_lesson_id, $locale, $title, $brief, $description, $created_at, $created_by);
    }

    $this->userLessons();
}


// ===================== user_polls =====================
public function edit_user_poll(int $user_poll_id) {
    $this->soft_delete_user_poll_by_id($user_poll_id);
    $this->add_new_user_poll();
}

public function add_new_user_poll() {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';

    dump($_POST);
    
    $owner_id     = post('owner_id');
    $target_type  = post('target_type');
    $target_id    = post('target_id');
    $type         = post('type');
    $status       = post('status');
    $expires_at   = post('expires_at');
    $is_anonymous = post('is_anonymous') == 'on' ? 1 : 0;

    $title        = post('title');
    $brief        = post('brief');
    $description  = post('description');

    $text_1       = post('text_1');
    $subject_1    = post('subject_1');

    $table_name = 'user_polls';
    $model = new pageModel();

    $user_poll_id = $model->insert_new_user_poll($owner_id, $target_type, $target_id, $type, $status, $expires_at, $is_anonymous);
    $model->insert_new_plus1_translation($table_name, $user_poll_id, $locale, $title, $brief, $description, $subject_1, $text_1);

    $this->userPolls();
}


// ===================== user_poll_options =====================
public function add_new_user_poll_option() {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';

    $poll_id      = post('poll_id');
    $sort_order   = post('sort_order');

    $title        = post('title');
    $brief        = post('brief');
    $description  = post('description');
    
    $table_name = 'user_poll_options';
    $model = new pageModel();

    $user_poll_option_id = $model->insert_new_user_poll_option($poll_id, $sort_order);
    $model->insert_new_translation($table_name, $user_poll_option_id, $locale, $title, $brief, $description);

    $this->userPollOptions($poll_id);
}



// ===================== user_poll_votes =====================
public function add_new_user_poll_vote() {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';

    $poll_id     = post('poll_id');
    $option_id   = post('option_id');
    $user_id     = post('user_id');

    $title       = post('title');
    $brief       = post('brief');
    $description = post('description');

    $poll_option_votes_counter = $this->get_votes_count_of_user_poll_options_by_poll_id($option_id)['votes_count'] + 1;
    $poll_votes_counter = $this->get_votes_count_of_user_polls_by_poll_id($poll_id)['votes_count'] + 1;

    $table_name = 'user_poll_votes';
    $model = new pageModel();

    $user_poll_vote_id = $model->insert_new_user_poll_vote($poll_id, $option_id, $user_id);
    $user_poll_id = $model->update_vote_counts_of_user_poll($poll_id, $poll_votes_counter);
    $user_poll_option_id = $model->update_vote_counts_of_user_poll_option($option_id, $poll_option_votes_counter);
    $model->insert_new_translation($table_name, $user_poll_vote_id, $locale, $title, $brief, $description);

    $this->userPollVotes($poll_id);
}



// ===================== media_files =====================
public function add_new_media_file() {
    // duration
    // width
    // height
    // checksum

    // dump($_POST);
    // dump($_FILES);

    global $config;
    $locale = $config['app']['lang'] ?? 'fa';

    $user_id           = post('user_id');
    $disk              = post('disk');
    $fileable_type     = post('fileable_type');
    $fileable_id       = post('fileable_id');
    // $sort_order        = post('sort_order');
    $sort_order        = 0;
    $directory         = post('directory');
    $visibility        = post('visibility');

    $original_filename = $_FILES['media_file']['name'];
    $filename          = $_FILES['media_file']['name'];
    $mime_type         = $_FILES['media_file']['type'];
    $extension         = pathinfo($filename, PATHINFO_EXTENSION);
    $type              = explode('/', $mime_type)[0];
    $size              = $_FILES['media_file']['size'];
    $full_path         = $_FILES['media_file']['full_path'];
    $temp_name         = $_FILES['media_file']['tmp_name'];
    $path              = $directory . '/original/' . $filename;
    $thumbnail_path    = $directory . '/thumbnail/' . $filename;

    $title       = post('title');
    $brief       = post('brief');
    $description = post('description');

    $table_name = 'media_files';
    $model = new pageModel();

    $media_file_id = $model->insert_new_media_file($user_id, $disk, $directory, $filename, $extension, $mime_type, $type, $path, $thumbnail_path, $size, $visibility, $original_filename, $fileable_type, $fileable_id, $sort_order);
    // $media_file_id = $model->insert_new_media_file($user_id, $disk, $directory, $filename, $extension, $mime_type, $type, $path, $thumbnail_path, $size, $duration, $width, $height, $checksum, $visibility, $original_filename, $fileable_type, $fileable_id, $sort_order);
    $model->insert_new_translation($table_name, $media_file_id, $locale, $title, $brief, $description);

    $this->userMedia();
}




// ===================== academy_branch_scheduling_queue =====================
// public function add_new_academy_branch_scheduling_queue() {
//     $reference_id = post('reference_id');
//     $type         = post('type') ?? 'booking';
//     $priority     = post('priority') ?? 0;

//     $model = new pageModel();
//     $model->insert_new_academy_branch_scheduling_queue($reference_id, $created_by, $type, $priority);
    
//     $this->academySchedulingQueue();
// }

// ===================== academy_branch_scheduling_rules =====================
// public function add_new_academy_branch_scheduling_rule() {
//     $branch_id            = post('branch_id');
//     $max_sessions_per_day = post('max_sessions_per_day');
//     $min_break_minutes    = post('min_break_minutes');
//     $session_duration     = post('session_duration');
//     $allow_overlap        = post('allow_overlap') ?? 0;

//     $model = new pageModel();
//     $model->insert_new_academy_branch_scheduling_rule($branch_id, $max_sessions_per_day, $min_break_minutes, $session_duration, $allow_overlap);
    
//     $this->academyBranchSchedulingRules();
// }
















// ==========================================================
// ==========================================================
// ==========================================================
// ==========================================================






    public function delete_article(int $post_id) {
        $delete_status = AdminModel::delete_post($post_id);
        $this->showArticleList('all', 'sor_posts.modified');
    }









    public function add_item_to_settings_table() {
        $page               = $_POST['page'];
        $variable_name      = $_POST['variable_name'];
        $setting_table_name = $_POST['table_name'];
        $value              = $_POST['value'];
        $url                = $_POST['url'];
        $source             = $_POST['source'];
        $status             = $_POST['status'];
        $icon               = $_POST['icon'];

        $title_fa           = post('title_fa') ?? '';
        $title_en           = post('title_en') ?? '';
        $brief_fa           = post('brief_fa') ?? '';
        $brief_en           = post('brief_en') ?? '';
        $description_fa     = post('description_fa') ?? '';
        $description_en     = post('description_en') ?? '';

        $table_name = 'sor_settings';
        $model = new pageModel();
        $admin_model = new AdminModel();

        $setting_id = $admin_model->add_setting($page, $variable_name, $setting_table_name, $value, $url, $source, $status, $icon);
        $model->insert_new_translation($table_name, $setting_id, 'fa', $title_fa, $brief_fa, $description_fa);
        $model->insert_new_translation($table_name, $setting_id, 'en', $title_en, $brief_en, $description_en);

        $this->settings();
    }





















    public function add_setting() {
        $this->add_item_to_settings_table();
        $this->settings();
    }

    public function add_category() {
        $this->add_item_to_settings_table();
        $this->categories();
    }

    public function edit_article(int $post_id) {
        global $config;

        if($config['lang'] === 'fa') {
        $title_fa = $_POST['title'];
        $excerpt_fa = $_POST['excerpt'];
        $content_fa = $_POST['content'];
        
        $title_en = $_POST['title_en'];
        $excerpt_en = $_POST['excerpt_en'];
        $content_en = $_POST['content_en'];
        } else {
        $title_en = $_POST['title'];
        $excerpt_en = $_POST['excerpt'];
        $content_en = $_POST['content'];
        
        $title_fa = $_POST['title_fa'];
        $excerpt_fa = $_POST['excerpt_fa'];
        $content_fa = $_POST['content_fa'];
        
        }

        $post_id = $_POST['post_id'];
        $author_id = $_POST['author_id'];
        $tags = $_POST['tags'];
        $keywords = $_POST['keywords'];
        $type = $_POST['type'];
        $status = $_POST['status'];
        $password = $_POST['password'];
        $comment_count = $_POST['comment_count'];
        $name = $_POST['name'];
        $pinged = $_POST['pinged'];
        $guid = $_POST['guid'];
        $related_posts_id = $_POST['related_posts_id'];
        $date = str_replace('T', ' ', $_POST['date']);
        $cover = $_FILES['post_image']['name'] ? substr($_FILES['post_image']['name'], 0, strlen($_FILES['post_image']['name']) - 6) : $_POST['cover'];
        // $cover = $_FILES['post_image']['name'] ? explode('.', $_FILES['post_image']['name'])[0] : $_POST['cover'];

        $categories = '';
        foreach($_POST as $key => $value) {
        if(strhas($key, 'article_category_')) {
            $categories = $categories === '' ? $value : $categories . ',' . $value;
        }
        }
        $insert_status = AdminModel::add_post(
        $post_id, $author_id, $title_fa, $title_en, $categories, $tags, $keywords, $cover, $date, $type, 
        $status, $password, $comment_count, $name, $pinged, $guid, $related_posts_id, $excerpt_fa, $excerpt_en, $content_fa, $content_en
        );
        $update_status = AdminModel::update_post($post_id, 'inherit', $date);
        $this->showArticleList('all', 'sor_posts.modified');
    }


    public function add_user_from_admin_panel(){
        // dump($_POST);
        // exit();

        $email = post('email');
        
        $record = AccountModel::fetch_by_email($email);
        if ($record != null){
            $this->userManagement();
            return;
        }
        
        $username = post('username');
        $fullname_fa = post('fullname_fa');
        $fullname_en = post('fullname_en');
        $mobile = post('mobile');
        
        $role = '|' . explode('-', post('role'))[0] . '|';
        $job = explode('-', post('role'))[1];
        $academy_job = '|' . $job . '|';
        
        $activity_status = post('activity_status') == "on" ? 1 : 0;
        $active_user_id = post('activity_status') == "on" ? session_get('user_id') : null;
        // $picture_type = $_FILES['picture_type']['name'];
        
        $picture_type = $_FILES['picture_type']['name'] == '' ? null : explode('.', $_FILES['picture_type']['name'])[1];
        // $picture_type = substr($_FILES['picture_type']['name'], strlen($_FILES['picture_type']['name']) - 3);
        
        $gender = post('gender');
        $birthday = post('birthday');
        $national_code = post('national_code');

        // echo $time_sheet;
        // exit();

        $new_user_id = AdminModel::add_user($email, $username, $fullname_fa, $fullname_en, $mobile, $role, $academy_job, $activity_status, $picture_type, $gender, $birthday, $national_code, $active_user_id);

        $user_id = session_get('user_id');
        $author_email = $email ;
        $author = $username ;
        $post_id = 6;
        $parent = 0;
        $type = 'message';
        $agent = $_SERVER['HTTP_USER_AGENT'];
        
        $receiver_user_id = session_get('user_id');
        $content = "New User Registered in Sornaz Application, " . $author_email . " with User id " . $new_user_id . " by " . session_get('username') . " with User id " . $user_id;
        PageModel::add_register_contact($user_id, $author_email, $author, $post_id, $content, $parent, $receiver_user_id, $agent, $type);

        // setPageTitle("New User Register Succeed");
        $this->userManagement();
    }

    /*-----------------------------*/
    /*-----------------------------*/
    /*-----------------------------*/

    public function edit_user_from_admin_panel(int $user_id) {
        // dump($_POST);
        // exit();
        
        $email = post('email');
        
        // $record = AccountModel::fetch_by_email($email);
        // if ($record != null){
        //   $this->userManagement();
        //   return;
        // }
        $job = explode('-', post('role'))[1];
        
        $username = post('username');
        $fullname_fa = post('fullname_fa');
        $fullname_en = post('fullname_en');
        $mobile = post('mobile');
        $academy_id = post('academy_id');
        $role = '|' . explode('-', post('role'))[0] . '|';
        $academy_job = '|' . $job . '|';
        $instruments_id = post('instruments_id');
        $lessons_id = post('lessons_id');
        $start_career_date = post('start_career_date');
        $activity_status = post('activity_status');
        // $picture_type = $_FILES['picture_type']['name'];
        
        $picture_type = $_FILES['picture_type']['name'] == '' ? null : explode('.', $_FILES['picture_type']['name'])[1];
        // $picture_type = substr($_FILES['picture_type']['name'], strlen($_FILES['picture_type']['name']) - 3);
        
        $gender = post('gender');
        $student_level = post('student_level');
        $parent_name_fa = post('parent_name_fa');
        $parent_name_en = post('parent_name_en');
        $parent_phone = post('parent_phone');
        $birthday = post('birthday');
        $academy_register_date = post('academy_register_date');
        $national_code = post('national_code');
        $biography_fa = post('biography_fa');
        $biography_en = post('biography_en');

        $time_sheet = '';
        foreach($_POST as $key => $value){
            if(strhas($key, 'time_sheet')) {
                $time_sheet = $value . ',' . $time_sheet;
            }
        }

        // echo $time_sheet;
        // exit();

        $new_user_id = AdminModel::update_user($user_id, $email, $username, $fullname_fa, $fullname_en, $mobile, $academy_id, $role, $academy_job, $instruments_id, $lessons_id, $start_career_date, $activity_status, $picture_type, $gender, $student_level, $parent_name_fa, $parent_name_en, $parent_phone, $birthday, $academy_register_date, $national_code, $biography_fa, $biography_en, $time_sheet);
        

        $user_id = session_get('user_id');
        $author_email = $email ;
        $author = $username ;
        $post_id = 8;
        $parent = 0;
        $type = 'message';

        $agent = $_SERVER['HTTP_USER_AGENT'];
        
        $receiver_user_id = '1';
        $content = "User " . $email . " with User id " . $user_id . " Updated by " . session_get('username') . " with User id " . $user_id;
        PageModel::add_register_contact(session_get('user_id'), $author_email, $author, $post_id, $content, $parent, $receiver_user_id, $agent, $type);
        

        // setPageTitle("New User Register Succeed");
        $this->userManagement();
    }


    public function delete_user_from_admin_panel (int $user_id) {
        $delete_status = AdminModel::delete_user($user_id);
        $this->userManagement();
        return $delete_status;
    }



}