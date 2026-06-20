<?php

trait AdminUpdateQueriesTrait {


// ==========================================
//              ACADEMY MODULE - EDIT
// ==========================================

    // ===================== academies =====================
    public function edit_academy_by_id(int $academy_id) {
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';
        
        $user_id         = post('user_id');
        $title           = post('title');
        $brief           = post('brief');
        $description     = post('description');
        $updated_by      = post('manager_id') ?: post('updated_by');

        $table_name = 'academies';
        $model = new pageModel();
        
        $model->update_academy($academy_id, $user_id, $updated_by);
        $model->update_new_translation($table_name, $academy_id, $locale, $title, $brief, $description, $updated_by);
        
        $this->academies();
    }

    // ===================== academy_branches =====================
    public function edit_academy_branch_by_id(int $branch_id) {
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';
        
        $academy_id = post('academy_id');
        $user_id    = post('user_id');
        $is_main    = post('is_main') ?? 0;
        $mode       = post('mode');
        $timezone   = post('timezone');
        $title      = post('title');
        $brief      = post('brief');
        $description= post('description');
        $updated_by = post('manager_id') ?: post('updated_by');

        $table_name = 'academy_branches';
        $model = new pageModel();
        
        $model->update_academy_branch($branch_id, $academy_id, $user_id, $is_main, $mode, $timezone, $updated_by);
        $model->update_new_translation($table_name, $branch_id, $locale, $title, $brief, $description, $updated_by);
        
        $this->academyBranches();
    }

    // ===================== academy_branch_phones =====================
    public function edit_academy_branch_phone_by_id(int $id) {
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';
        
        $branch_id     = post('branch_id');
        $type          = post('type');
        $country_code  = post('country_code');
        $phone         = post('phone');
        $title         = post('title');
        $brief         = post('brief');
        $description   = post('description');
        $updated_by    = post('manager_id') ?: post('updated_by');

        $table_name = 'academy_branch_phones';
        $model = new pageModel();
        
        $model->update_academy_branch_phone($id, $branch_id, $type, $country_code, $phone, $updated_by);
        $model->update_new_translation($table_name, $id, $locale, $title, $brief, $description, $updated_by);
        
        $this->academyBranchPhones();
    }

    // ===================== academy_branch_classrooms =====================
    public function edit_academy_branch_classroom_by_id(int $id) {
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';
        
        $branch_id  = post('branch_id');
        $type_id    = post('type_id');
        $capacity   = post('capacity');
        $is_active  = post('is_active') ?? 1;
        $status     = post('status') ?? 'available';
        $title      = post('title');
        $brief      = post('brief');
        $description= post('description');
        $updated_by = post('manager_id') ?: post('updated_by');

        $table_name = 'academy_branch_classrooms';
        $model = new pageModel();
        
        $model->update_academy_branch_classroom($id, $branch_id, $type_id, $capacity, $is_active, $status, $updated_by);
        $model->update_new_translation($table_name, $id, $locale, $title, $brief, $description, $updated_by);
        
        $this->academyBranchClassrooms();
    }

    // ===================== academy_branch_courses =====================
    public function edit_academy_branch_course_by_id(int $id) {
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';
        
        $branch_id  = post('branch_id');
        $lesson_id  = post('lesson_id');
        $level_id   = post('level_id');
        $capacity   = post('capacity');
        $title      = post('title');
        $brief      = post('brief');
        $description= post('description');
        $updated_by = post('manager_id') ?: post('updated_by');

        $table_name = 'academy_branch_courses';
        $model = new pageModel();
        
        $model->update_academy_branch_course($id, $branch_id, $lesson_id, $level_id, $capacity, $updated_by);
        $model->update_new_translation($table_name, $id, $locale, $title, $brief, $description, $updated_by);
        
        $this->academyBranchCourses();
    }

    // ===================== academy_branch_course_terms =====================
    public function edit_academy_branch_course_term_by_id(int $id) {
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';
        
        $course_id     = post('course_id');
        $branch_id     = post('branch_id');
        $start_date    = post('start_date');
        $end_date      = post('end_date');
        $session_count = post('session_count');
        $price         = post('price');
        $currency_id   = post('currency_id');
        $status        = post('status');
        $title         = post('title');
        $brief         = post('brief');
        $description   = post('description');
        $updated_by    = post('manager_id') ?: post('updated_by');

        $table_name = 'academy_branch_course_terms';
        $model = new pageModel();
        
        $model->update_academy_branch_course_term($id, $course_id, $branch_id, $start_date, $end_date, $session_count, $price, $currency_id, $status, $updated_by);
        $model->update_new_translation($table_name, $id, $locale, $title, $brief, $description, $updated_by);
        
        $this->academyBranchCourseTerms();
    }

// ==========================================
//              USER MODULE - EDIT
// ==========================================

    public function edit_user_by_id(int $user_id) {
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';
        
        $email      = post('email');
        $username   = post('username');
        $phone      = post('phone');
        $national_code = post('national_code');
        $gender     = post('gender');
        $status     = post('status');
        $title      = post('title');
        $brief      = post('brief');
        $description= post('description');
        $updated_by = post('manager_id') ?: post('updated_by');

        $table_name = 'users';
        $model = new pageModel();
        
        $model->update_user($user_id, $email, $username, $phone, $national_code, $gender, $status, $updated_by);
        $model->update_new_translation($table_name, $user_id, $locale, $title, $brief, $description, $updated_by);
        
        $this->users();
    }

    public function edit_user_profile_by_id(int $user_id) {
        $student_level_id = post('student_level_id');
        $start_career_date = post('start_career_date');
        $picture_media_id = post('picture_media_id');
        $updated_by = post('manager_id') ?: post('updated_by');

        $model = new pageModel();
        $model->update_user_profile($user_id, $student_level_id, $start_career_date, $picture_media_id, $updated_by);
        
        $this->userProfiles();
    }

// ==========================================
//              OTHER IMPORTANT TABLES
// ==========================================

    public function edit_tag_by_id(int $id) {
        $name = post('name');
        $slug = post('slug');
        $updated_by = post('manager_id') ?: post('updated_by');

        $model = new pageModel();
        $model->update_tag($id, $name, $slug, $updated_by);
        
        $this->tags();
    }

    public function edit_setting_by_id(int $id) {
        $key   = post('key');
        $group = post('group');
        $type  = post('type');
        $value = post('value');
        $is_active = post('is_active') ?? 1;
        $updated_by = post('manager_id') ?: post('updated_by');

        $model = new pageModel();
        $model->update_setting($id, $key, $group, $type, $value, $is_active, $updated_by);
        
        $this->settings();
    }

    public function edit_verification_level_by_id(int $id) {
        $code      = post('code');
        $priority  = post('priority');
        $icon      = post('icon');
        $color     = post('color');
        $is_public = post('is_public') ?? 1;
        $updated_by = post('manager_id') ?: post('updated_by');

        $model = new pageModel();
        $model->update_verification_level($id, $code, $priority, $icon, $color, $is_public, $updated_by);
        
        $this->verificationLevels();
    }





// ===========================================================================================================================
// ===========================================================================================================================
// ===========================================================================================================================
// ===========================================================================================================================


// ==========================================
//              ACADEMY BRANCH - EDIT
// ==========================================

// ===================== academy_branch_bookings =====================
public function edit_academy_branch_booking_by_id(int $id) {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';
    
    $student_id     = post('student_id');
    $teacher_id     = post('teacher_id');
    $branch_id      = post('branch_id');
    $requested_date = post('requested_date');
    $start_time     = post('start_time');
    $end_time       = post('end_time');
    $status         = post('status') ?? 'pending';
    $source         = post('source');
    $note           = post('note');
    $title          = post('title');
    $brief          = post('brief');
    $description    = post('description');
    $updated_by     = post('manager_id') ?: post('updated_by');

    $table_name = 'academy_branch_bookings';
    $model = new pageModel();
    
    $model->update_academy_branch_booking($id, $student_id, $teacher_id, $branch_id, $requested_date, $start_time, $end_time, $status, $source, $note, $updated_by);
    $model->update_new_translation($table_name, $id, $locale, $title, $brief, $description, $updated_by);
    
    $this->academyBranchBookings();
}

// ===================== academy_branch_booking_enrollments =====================
public function edit_academy_branch_booking_enrollment_by_id(int $id) {
    $booking_id = post('booking_id');
    $student_id = post('student_id');
    $status     = post('status');
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_academy_branch_booking_enrollment($id, $booking_id, $student_id, $status, $updated_by);
    
    $this->academyBranchBookingEnrollments();
}

// ===================== academy_branch_classroom_assets =====================
public function edit_academy_branch_classroom_asset_by_id(int $id) {
    $classroom_id = post('classroom_id');
    $quantity     = post('quantity');
    $updated_by   = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_academy_branch_classroom_asset($id, $classroom_id, $quantity, $updated_by);
    
    $this->academyBranchClassroomAssets();
}

// ===================== academy_branch_classroom_types =====================
public function edit_academy_branch_classroom_type_by_id(int $id) {
    $code = post('code');
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_academy_branch_classroom_type($id, $code, $updated_by);
    
    $this->academyBranchClassroomTypes();
}

// ===================== academy_branch_course_term_enrollments =====================
public function edit_academy_branch_course_term_enrollment_by_id(int $id) {
    $term_id    = post('term_id');
    $student_id = post('student_id');
    $status     = post('status');
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_academy_branch_course_term_enrollment($id, $term_id, $student_id, $status, $updated_by);
    
    $this->academyBranchCourseTermEnrollments();
}

// ===================== academy_branch_course_term_invoices =====================
public function edit_academy_branch_course_term_invoice_by_id(int $id) {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';
    
    $user_id        = post('user_id');
    $branch_id      = post('branch_id');
    $term_id        = post('term_id');
    $total_amount   = post('total_amount');
    $payable_amount = post('payable_amount');
    $currency_id    = post('currency_id');
    $status         = post('status');
    $due_date       = post('due_date');
    $title          = post('title');
    $brief          = post('brief');
    $description    = post('description');
    $updated_by     = post('manager_id') ?: post('updated_by');

    $table_name = 'academy_branch_course_term_invoices';
    $model = new pageModel();
    
    $model->update_academy_branch_course_term_invoice($id, $user_id, $branch_id, $term_id, $total_amount, $payable_amount, $currency_id, $status, $due_date, $updated_by);
    $model->update_new_translation($table_name, $id, $locale, $title, $brief, $description, $updated_by);
    
    $this->academyBranchCourseTermInvoices();
}

// ===================== academy_branch_course_term_invoice_discounts =====================
public function edit_academy_branch_course_term_invoice_discount_by_id(int $id) {
    $invoice_id  = post('invoice_id');
    $discount_id = post('discount_id');
    $amount      = post('amount');
    $updated_by  = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_academy_branch_course_term_invoice_discount($id, $invoice_id, $discount_id, $amount, $updated_by);
    
    $this->academyBranchCourseTermInvoiceDiscounts();
}

// ===================== academy_branch_course_term_invoice_installments =====================
public function edit_academy_branch_course_term_invoice_installment_by_id(int $id) {
    $invoice_id        = post('invoice_id');
    $installment_number= post('installment_number');
    $amount            = post('amount');
    $due_date          = post('due_date');
    $status            = post('status');
    $updated_by        = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_academy_branch_course_term_invoice_installment($id, $invoice_id, $installment_number, $amount, $due_date, $status, $updated_by);
    
    $this->academyBranchCourseTermInvoiceInstallments();
}

// ===================== academy_branch_course_term_sessions =====================
public function edit_academy_branch_course_term_session_by_id(int $id) {
    $term_id          = post('term_id');
    $booking_id       = post('booking_id');
    $classroom_id     = post('classroom_id');
    $branch_url_id    = post('branch_url_id');
    $teacher_id       = post('teacher_id');
    $status           = post('status');
    $session_number   = post('session_number');
    $date             = post('date');
    $start_time       = post('start_time');
    $end_time         = post('end_time');
    $updated_by       = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_academy_branch_course_term_session($id, $term_id, $booking_id, $classroom_id, $branch_url_id, $teacher_id, $status, $session_number, $date, $start_time, $end_time, $updated_by);
    
    $this->academyBranchCourseTermSessions();
}

// ===================== academy_branch_course_term_session_attendances =====================
public function edit_academy_branch_course_term_session_attendance_by_id(int $session_id, int $academy_member_id) {
    $status     = post('status');
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_academy_branch_course_term_session_attendance($session_id, $academy_member_id, $status, $updated_by);
    
    $this->academyBranchCourseTermSessionAttendances();
}

// ===================== academy_branch_course_term_session_changes =====================
public function edit_academy_branch_course_term_session_change_by_id(int $id) {
    $new_classroom_id = post('new_classroom_id');
    $new_teacher_id   = post('new_teacher_id');
    $new_date         = post('new_date');
    $reason           = post('reason');
    $status           = post('status');
    $updated_by       = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_academy_branch_course_term_session_change($id, $new_classroom_id, $new_teacher_id, $new_date, $reason, $status, $updated_by);
    
    $this->academyBranchCourseTermSessionChanges();
}

// ===================== academy_branch_course_term_session_classrooms =====================
public function edit_academy_branch_course_term_session_classroom_by_id(int $session_id, int $classroom_id) {
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_academy_branch_course_term_session_classroom($session_id, $classroom_id, $updated_by);
    
    $this->academyBranchCourseTermSessionClassrooms();
}

// ===================== academy_branch_course_term_session_exceptions =====================
public function edit_academy_branch_course_term_session_exception_by_id(int $id) {
    $type        = post('type');
    $new_date    = post('new_date');
    $description = post('description');
    $updated_by  = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_academy_branch_course_term_session_exception($id, $type, $new_date, $description, $updated_by);
    
    $this->academyBranchCourseTermSessionExceptions();
}

// ===================== academy_branch_course_term_waiting_list =====================
public function edit_academy_branch_course_term_waiting_list_by_id(int $id) {
    $term_id    = post('term_id');
    $user_id    = post('user_id');
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_academy_branch_course_term_waiting_list($id, $term_id, $user_id, $updated_by);
    
    $this->academyBranchCourseTermWaitingList();
}

// ===================== academy_branch_members =====================
public function edit_academy_branch_member_by_id(int $id) {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';
    
    $branch_id  = post('branch_id');
    $user_id    = post('user_id');
    $role_id    = post('role_id');
    $status     = post('status');
    $title      = post('title');
    $brief      = post('brief');
    $description= post('description');
    $updated_by = post('manager_id') ?: post('updated_by');

    $table_name = 'academy_branch_members';
    $model = new pageModel();
    
    $model->update_academy_branch_member($id, $branch_id, $user_id, $role_id, $status, $updated_by);
    $model->update_new_translation($table_name, $id, $locale, $title, $brief, $description, $updated_by);
    
    $this->academyBranchMembers();
}

// ===================== academy_branch_member_contracts =====================
public function edit_academy_branch_member_contract_by_id(int $id) {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';
    
    $member_id  = post('member_id');
    $branch_id  = post('branch_id');
    $type       = post('type');
    $start_date = post('start_date');
    $end_date   = post('end_date');
    $terms      = post('terms');
    $title      = post('title');
    $brief      = post('brief');
    $description= post('description');
    $updated_by = post('manager_id') ?: post('updated_by');

    $table_name = 'academy_branch_member_contracts';
    $model = new pageModel();
    
    $model->update_academy_branch_member_contract($id, $member_id, $branch_id, $type, $start_date, $end_date, $terms, $updated_by);
    $model->update_new_translation($table_name, $id, $locale, $title, $brief, $description, $updated_by);
    
    $this->academyBranchMemberContracts();
}

// ===================== academy_branch_member_permissions =====================
public function edit_academy_branch_member_permission_by_id(int $academy_member_id, int $permission_id) {
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_academy_branch_member_permission($academy_member_id, $permission_id, $updated_by);
    
    $this->academyBranchMemberPermissions();
}

// ===================== academy_branch_member_schedules =====================
public function edit_academy_branch_member_schedule_by_id(int $id) {
    $academy_member_id = post('academy_member_id');
    $branch_id         = post('branch_id');
    $day_of_week       = post('day_of_week');
    $start_time        = post('start_time');
    $end_time          = post('end_time');
    $availability_type = post('availability_type');
    $updated_by        = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_academy_branch_member_schedule($id, $academy_member_id, $branch_id, $day_of_week, $start_time, $end_time, $availability_type, $updated_by);
    
    $this->academyBranchMemberSchedules();
}

// ===================== academy_branch_scheduling_queues =====================
public function edit_academy_branch_scheduling_queue_by_id(int $id) {
    $reference_id = post('reference_id');
    $status       = post('status');
    $type         = post('type');
    $priority     = post('priority');
    $updated_by   = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_academy_branch_scheduling_queue($id, $reference_id, $status, $type, $priority, $updated_by);
    
    $this->academyBranchSchedulingQueues();
}

// ===================== academy_branch_scheduling_rules =====================
public function edit_academy_branch_scheduling_rule_by_id(int $id) {
    $branch_id            = post('branch_id');
    $max_sessions_per_day = post('max_sessions_per_day');
    $min_break_minutes    = post('min_break_minutes');
    $session_duration     = post('session_duration');
    $allow_overlap        = post('allow_overlap') ?? 0;
    $updated_by           = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_academy_branch_scheduling_rule($id, $branch_id, $max_sessions_per_day, $min_break_minutes, $session_duration, $allow_overlap, $updated_by);
    
    $this->academyBranchSchedulingRules();
}

// ===================== academy_branch_types =====================
public function edit_academy_branch_type_by_id(int $id) {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';
    
    $title      = post('title');
    $brief      = post('brief');
    $description= post('description');
    $updated_by = post('manager_id') ?: post('updated_by');

    $table_name = 'academy_branch_types';
    $model = new pageModel();
    
    $model->update_academy_branch_type($id, $updated_by);
    $model->update_new_translation($table_name, $id, $locale, $title, $brief, $description, $updated_by);
    
    $this->academyBranchTypes();
}

// ==========================================
//              ACCESS & FINANCIAL & COMMON
// ==========================================

public function edit_access_system_permission_by_id(int $id) {
    $name       = post('name');
    $group_name = post('group_name');
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_access_system_permission($id, $name, $group_name, $updated_by);
    
    $this->accessSystemPermissions();
}

public function edit_access_system_role_by_id(int $id) {
    $name        = post('name');
    $description = post('description');
    $color       = post('color');
    $sort_order  = post('sort_order');
    $updated_by  = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_access_system_role($id, $name, $description, $color, $sort_order, $updated_by);
    
    $this->accessSystemRoles();
}

public function edit_conversation_by_id(int $id) {
    $title      = post('title');
    $type       = post('type');
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_conversation($id, $title, $type, $updated_by);
    
    $this->conversations();
}

public function edit_financial_system_account_by_id(int $id) {
    $user_id    = post('user_id');
    $branch_id  = post('branch_id');
    $type       = post('type');
    $balance    = post('balance');
    $status     = post('status');
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_financial_system_account($id, $user_id, $branch_id, $type, $balance, $status, $updated_by);
    
    $this->financialSystemAccounts();
}


// ==================================================================================================================
// ==================================================================================================================
// ==================================================================================================================
// ==================================================================================================================
// ==================================================================================================================
// ==================================================================================================================
// ==================================================================================================================
// ==================================================================================================================
// ==================================================================================================================


// ==========================================
//              ACCESS SYSTEM
// ==========================================

public function edit_access_system_role_permission_by_id(int $role_id, int $permission_id) {
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_access_system_role_permission($role_id, $permission_id, $updated_by);
    
    $this->accessSystemRolePermissions();
}

// ==========================================
//              CONVERSATION
// ==========================================

public function edit_conversation_member_by_id(int $id) {
    $conversation_id = post('conversation_id');
    $user_id         = post('user_id');
    $role            = post('role');
    $is_muted        = post('is_muted') ?? 0;
    $updated_by      = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_conversation_member($id, $conversation_id, $user_id, $role, $is_muted, $updated_by);
    
    $this->conversationMembers();
}

// ==========================================
//              FINANCIAL SYSTEM
// ==========================================

public function edit_financial_system_currency_by_id(int $id) {
    $brief      = post('brief');
    $icon_path  = post('icon_path');
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_financial_system_currency($id, $brief, $icon_path, $updated_by);
    
    $this->financialSystemCurrency();
}

public function edit_financial_system_discount_by_id(int $id) {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';
    
    $code       = post('code');
    $type       = post('type');
    $value      = post('value');
    $max_usage  = post('max_usage');
    $title      = post('title');
    $brief      = post('brief');
    $description= post('description');
    $updated_by = post('manager_id') ?: post('updated_by');

    $table_name = 'financial_system_discounts';
    $model = new pageModel();
    
    $model->update_financial_system_discount($id, $code, $type, $value, $max_usage, $updated_by);
    $model->update_new_translation($table_name, $id, $locale, $title, $brief, $description, $updated_by);
    
    $this->financialSystemDiscounts();
}

public function edit_financial_system_ledger_entry_by_id(int $id) {
    $account_id     = post('account_id');
    $reference_id   = post('reference_id');
    $transaction_id = post('transaction_id');
    $type           = post('type');
    $amount         = post('amount');
    $description    = post('description');
    $updated_by     = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_financial_system_ledger_entry($id, $account_id, $reference_id, $transaction_id, $type, $amount, $description, $updated_by);
    
    $this->financialSystemLedgerEntries();
}

public function edit_financial_system_payment_by_id(int $id) {
    $invoice_id   = post('invoice_id');
    $payer_id     = post('payer_id');
    $amount       = post('amount');
    $currency_id  = post('currency_id');
    $method       = post('method');
    $status       = post('status');
    $updated_by   = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_financial_system_payment($id, $invoice_id, $payer_id, $amount, $currency_id, $method, $status, $updated_by);
    
    $this->financialSystemPayments();
}

public function edit_financial_system_refund_by_id(int $id) {
    $payment_id = post('payment_id');
    $amount     = post('amount');
    $reason     = post('reason');
    $status     = post('status');
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_financial_system_refund($id, $payment_id, $amount, $reason, $status, $updated_by);
    
    $this->financialSystemRefunds();
}

public function edit_financial_system_transaction_by_id(int $id) {
    $type       = post('type');
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_financial_system_transaction($id, $type, $updated_by);
    
    $this->financialSystemTransactions();
}

// ==========================================
//              COMMON TABLES
// ==========================================

public function edit_instrument_by_id(int $id) {
    $updated_by = post('manager_id') ?: post('updated_by');
    $model = new pageModel();
    $model->update_instrument($id, $updated_by);
    $this->instruments();
}

public function edit_language_by_id(string $code) {
    $name       = post('name');
    $direction  = post('direction');
    $is_active  = post('is_active') ?? 1;
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_language($code, $name, $direction, $is_active, $updated_by);
    
    $this->languages();
}

public function edit_lesson_by_id(int $lesson_id) {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';
    
    $name_fa        = post('name_fa');
    $name_en        = post('name_en');
    $description_fa = post('description_fa');
    $description_en = post('description_en');
    $reigion        = post('reigion');
    $updated_by     = post('manager_id') ?: post('updated_by');

    $table_name = 'sor_lessons';
    $model = new pageModel();
    
    $model->update_lesson($lesson_id, $name_fa, $name_en, $description_fa, $description_en, $reigion, $updated_by);
    $model->update_new_translation($table_name, $lesson_id, $locale, $name_fa, null, $description_fa, $updated_by);
    
    $this->lessons();
}

public function edit_level_by_id(int $id) {
    $type       = post('type');
    $sort_order = post('sort_order');
    $is_active  = post('is_active') ?? 1;
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_level($id, $type, $sort_order, $is_active, $updated_by);
    
    $this->levels();
}

public function edit_media_file_by_id(int $id) {
    $title         = post('title');
    $type          = post('type');
    $is_public     = post('is_public') ?? 1;
    $updated_by    = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_media_file($id, $title, $type, $is_public, $updated_by);
    
    $this->mediaFiles();
}

public function edit_otp_code_by_id(int $id) {
    $target     = post('target');
    $type       = post('type');
    $code       = post('code');
    $purpose    = post('purpose');
    $expires_at = post('expires_at');
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_otp_code($id, $target, $type, $code, $purpose, $expires_at, $updated_by);
    
    $this->otpCodes();
}

public function edit_password_reset_by_email(string $email) {
    $token       = post('token');
    $expires_at  = post('expires_at');
    $updated_by  = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_password_reset($email, $token, $expires_at, $updated_by);
    
    // $this->passwordResets();
}

// ==========================================
//              USER RELATED - EDIT
// ==========================================

public function edit_user_address_by_id(int $id) {
    $user_id     = post('user_id');
    $country_id  = post('country_id');
    $state_id    = post('state_id');
    $city_id     = post('city_id');
    $address     = post('address');
    $postal_code = post('postal_code');
    $updated_by  = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_user_address($id, $user_id, $country_id, $state_id, $city_id, $address, $postal_code, $updated_by);
    
    $this->userAddresses();
}

public function edit_user_audit_log_by_id(int $id) {
    // معمولاً Audit Log ویرایش نمی‌شود، اما اگر لازم باشد:
    $action     = post('action');
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_user_audit_log($id, $action, $updated_by);
    
    $this->userAuditLogs();
}

public function edit_user_auth_provider_by_id(int $id) {
    $provider         = post('provider');
    $provider_user_id = post('provider_user_id');
    $updated_by       = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_user_auth_provider($id, $provider, $provider_user_id, $updated_by);
    
    $this->userAuthProviders();
}

// ... (بقیه جداول user_ مثل user_availabilities, user_badges, user_certificates و غیره به همین شکل)

public function edit_user_availability_by_id(int $id) {
    $user_id      = post('user_id');
    $day_of_week  = post('day_of_week');
    $start_time   = post('start_time');
    $end_time     = post('end_time');
    $type         = post('type');
    $updated_by   = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_user_availability($id, $user_id, $day_of_week, $start_time, $end_time, $type, $updated_by);
    
    $this->userAvailabilities();
}

public function edit_user_badge_by_id(int $id) {
    $user_id              = post('user_id');
    $verification_level_id= post('verification_level_id');
    $status               = post('status');
    $updated_by           = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_user_badge($id, $user_id, $verification_level_id, $status, $updated_by);
    
    $this->userBadges();
}




// ==========================================================================================================================
// ==========================================================================================================================
// ==========================================================================================================================
// ==========================================================================================================================
// ==========================================================================================================================
// ==========================================================================================================================
// ==========================================================================================================================
// ==========================================================================================================================
// ==========================================================================================================================
// ==========================================================================================================================
// ==========================================================================================================================



// ==========================================
//              SYSTEM & COMMON
// ==========================================

public function edit_system_event_by_id(int $id) {
    $reference_id = post('reference_id');
    $type         = post('type');
    $data         = post('data');
    $updated_by   = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_system_event($id, $reference_id, $type, $data, $updated_by);
    
    $this->systemEvents();
}

public function edit_taggable_by_id(int $tag_id, int $entity_id, string $entity_type) {
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_taggable($tag_id, $entity_id, $entity_type, $updated_by);
    
    $this->taggables();
}

public function edit_translation_by_id(int $id) {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';
    
    $title       = post('title');
    $brief       = post('brief');
    $description = post('description');
    $updated_by  = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_translation($id, $locale, $title, $brief, $description, $updated_by);
    
    $this->translations();
}

// ==========================================
//              USER TABLES - EDIT
// ==========================================

public function edit_user_approval_by_id(int $id) {
    $by_user_id   = post('by_user_id');
    $entity_id    = post('entity_id');
    $entity_type  = post('entity_type');
    $action       = post('action');
    $note         = post('note');
    $updated_by   = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_user_approval($id, $by_user_id, $entity_id, $entity_type, $action, $note, $updated_by);
    
    $this->userApprovals();
}

public function edit_user_availability_exception_by_id(int $id) {
    $user_id     = post('user_id');
    $date        = post('date');
    $start_time  = post('start_time');
    $end_time    = post('end_time');
    $type        = post('type');
    $title       = post('title');
    $reason      = post('reason');
    $updated_by  = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_user_availability_exception($id, $user_id, $date, $start_time, $end_time, $type, $title, $reason, $updated_by);
    
    $this->userAvailabilityExceptions();
}

public function edit_user_award_by_id(int $id) {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';
    
    $user_id      = post('user_id');
    $title        = post('title');
    $organization = post('organization');
    $description  = post('description');
    $date         = post('date');
    $updated_by   = post('manager_id') ?: post('updated_by');

    $table_name = 'user_awards';
    $model = new pageModel();
    
    $model->update_user_award($id, $user_id, $title, $organization, $description, $date, $updated_by);
    $model->update_new_translation($table_name, $id, $locale, $title, null, $description, $updated_by);
    
    $this->userAwards();
}

public function edit_user_certificate_by_id(int $id) {
    $user_id        = post('user_id');
    $title          = post('title');
    $issuer         = post('issuer');
    $issue_date     = post('issue_date');
    $expire_date    = post('expire_date');
    $certificate_url= post('certificate_url');
    $updated_by     = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_user_certificate($id, $user_id, $title, $issuer, $issue_date, $expire_date, $certificate_url, $updated_by);
    
    $this->userCertificates();
}

public function edit_user_comment_by_id(int $id) {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';
    
    $post_id    = post('post_id');
    $user_id    = post('user_id');
    $content    = post('content');
    $status     = post('status');
    $updated_by = post('manager_id') ?: post('updated_by');

    $table_name = 'user_comments';
    $model = new pageModel();
    
    $model->update_user_comment($id, $post_id, $user_id, $content, $status, $updated_by);
    $model->update_new_translation($table_name, $id, $locale, null, null, $content, $updated_by);
    
    $this->userComments();
}

public function edit_user_contact_by_id(int $contact_id) {
    $user_id     = post('user_id');
    $post_id     = post('post_id');
    $content     = post('content');
    $approved    = post('approved');
    $updated_by  = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_user_contact($contact_id, $user_id, $post_id, $content, $approved, $updated_by);
    
    $this->userContacts();
}

public function edit_user_education_by_id(int $id) {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';
    
    $user_id      = post('user_id');
    $institution  = post('institution');
    $field_of_study = post('field_of_study');
    $degree       = post('degree');
    $start_date   = post('start_date');
    $end_date     = post('end_date');
    $is_current   = post('is_current') ?? 0;
    $description  = post('description');
    $updated_by   = post('manager_id') ?: post('updated_by');

    $table_name = 'user_educations';
    $model = new pageModel();
    
    $model->update_user_education($id, $user_id, $institution, $field_of_study, $degree, $start_date, $end_date, $is_current, $description, $updated_by);
    $model->update_new_translation($table_name, $id, $locale, $institution, null, $description, $updated_by);
    
    $this->userEducations();
}

public function edit_user_event_by_id(int $id) {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';
    
    $user_id    = post('user_id');
    $title      = post('title');
    $event_type = post('event_type');
    $location   = post('location');
    $event_date = post('event_date');
    $description= post('description');
    $updated_by = post('manager_id') ?: post('updated_by');

    $table_name = 'user_events';
    $model = new pageModel();
    
    $model->update_user_event($id, $user_id, $title, $event_type, $location, $event_date, $description, $updated_by);
    $model->update_new_translation($table_name, $id, $locale, $title, null, $description, $updated_by);
    
    $this->userEvents();
}

public function edit_user_experience_by_id(int $id) {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';
    
    $user_id    = post('user_id');
    $title      = post('title');
    $company    = post('company');
    $location   = post('location');
    $start_date = post('start_date');
    $end_date   = post('end_date');
    $is_current = post('is_current') ?? 0;
    $description= post('description');
    $updated_by = post('manager_id') ?: post('updated_by');

    $table_name = 'user_experiences';
    $model = new pageModel();
    
    $model->update_user_experience($id, $user_id, $title, $company, $location, $start_date, $end_date, $is_current, $description, $updated_by);
    $model->update_new_translation($table_name, $id, $locale, $title, null, $description, $updated_by);
    
    $this->userExperiences();
}

public function approved_user_experience(int $user_experience_id) {
    $model = new pageModel();
    $model->update_approval_user_experience_by_id($user_experience_id);
    
    $this->userExperiences();
}

public function approved_user_award(int $user_award_id) {
    $model = new pageModel();
    $model->update_approval_user_award_by_id($user_award_id);
    
    $this->userAwards();
}

public function approved_user_certificate(int $user_certificate_id) {
    $model = new pageModel();
    $model->update_approval_user_certificate_by_id($user_certificate_id);
    
    $this->userCertificates();
}

public function approved_user_education(int $user_education_id) {
    $model = new pageModel();
    $model->update_approval_user_education_by_id($user_education_id);
    
    $this->userEducations();
}

public function approved_user_event(int $user_event_id) {
    $model = new pageModel();
    $model->update_approval_user_event_by_id($user_event_id);
    
    $this->userEvents();
}

public function approved_user_instrument(int $user_instrument_id) {
    $model = new pageModel();
    $model->update_approval_user_instrument_by_id($user_instrument_id);
    
    $this->userInstruments();
}

public function approved_user_lesson(int $user_lesson_id) {
    $model = new pageModel();
    $model->update_approval_user_lesson_by_id($user_lesson_id);
    
    $this->userLessons();
}

public function approved_media_file(int $media_file_id) {
    $model = new pageModel();
    $model->update_approval_media_file_by_id($media_file_id);
    
    $this->userMedia();
}

public function approved_user_poll(int $user_poll_id) {
    $model = new pageModel();
    $model->update_approval_user_poll_by_id($user_poll_id);
    
    $this->userPolls();
}

public function status_closed_user_poll(int $user_poll_id) {
    $model = new pageModel();
    $model->update_status_closed_user_poll_by_id($user_poll_id);
    
    $this->userPolls();
}

public function status_active_user_poll(int $user_poll_id) {
    $model = new pageModel();
    $is_approved = $this->get_user_poll_by_poll_id($user_poll_id)[0]['approved_by'];
    if($is_approved == null){
        $this->approved_user_poll($user_poll_id);
    }
    $model->update_status_active_user_poll_by_id($user_poll_id);
    
    $this->userPolls();
}

public function edit_user_favorite_by_id(int $id) {
    $user_id   = post('user_id');
    $item_id   = post('item_id');
    $item_type = post('item_type');
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_user_favorite($id, $user_id, $item_id, $item_type, $updated_by);
    
    $this->userFavorites();
}

public function edit_user_instrument_by_id(int $user_id, int $instrument_id) {
    $level               = post('level');
    $years_of_experience = post('years_of_experience');
    $is_primary          = post('is_primary') ?? 0;
    $updated_by          = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_user_instrument($user_id, $instrument_id, $level, $years_of_experience, $is_primary, $updated_by);
    
    $this->userInstruments();
}

public function edit_user_lesson_by_id(int $user_id, int $lesson_id) {
    $level               = post('level');
    $years_of_experience = post('years_of_experience');
    $is_primary          = post('is_primary') ?? 0;
    $updated_by          = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_user_lesson($user_id, $lesson_id, $level, $years_of_experience, $is_primary, $updated_by);
    
    $this->userLessons();
}





// public function edit_user_media_by_id(int $id) {
//     $title      = post('title');
//     $caption    = post('caption');
//     $visibility = post('visibility');
//     $updated_by = post('manager_id') ?: post('updated_by');

//     $model = new pageModel();
//     $model->update_user_media($id, $title, $caption, $visibility, $updated_by);
    
//     $this->userMedia();
// }

public function edit_user_merge_by_id(int $id) {
    $from_user_id = post('from_user_id');
    $to_user_id   = post('to_user_id');
    $reason       = post('reason');
    $updated_by   = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_user_merge($id, $from_user_id, $to_user_id, $reason, $updated_by);
    
    $this->userMerges();
}

public function edit_user_message_by_id(int $id) {
    $content    = post('content');
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_user_message($id, $content, $updated_by);
    
    $this->userMessages();
}

public function edit_user_notification_by_id(int $id) {
    $title      = post('title');
    $message    = post('message');
    $is_read    = post('is_read') ?? 0;
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_user_notification($id, $title, $message, $is_read, $updated_by);
    
    $this->userNotifications();
}

public function edit_user_permission_by_id(int $user_id, int $permission_id) {
    $expires_at = post('expires_at');
    $note       = post('note');
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_user_permission($user_id, $permission_id, $expires_at, $note, $updated_by);
    
    $this->userPermissions();
}

public function edit_user_permission_cache_by_id(int $user_id, string $permission_name) {
    $source     = post('source');
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_user_permission_cache($user_id, $permission_name, $source, $updated_by);
    
    $this->userPermissionCache();
}

public function edit_user_point_by_id(int $id) {
    $points     = post('points');
    $action     = post('action');
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_user_point($id, $points, $action, $updated_by);
    
    $this->userPoints();
}

// public function edit_user_poll(int $id) {
//     global $config;
//     $locale = $config['app']['lang'] ?? 'fa';
    
//     $title      = post('title');
//     $description= post('description');
//     $question   = post('question');
//     $status     = post('status');
//     $updated_by = post('manager_id') ?: post('updated_by');

//     $table_name = 'user_polls';
//     $model = new pageModel();
    
//     $model->update_user_poll($id, $title, $description, $question, $status, $updated_by);
//     $model->update_new_translation($table_name, $id, $locale, $title, null, $description, $updated_by);
    
//     $this->userPolls();
// }

public function edit_user_poll_option_by_id(int $id) {
    $text       = post('text');
    $sort_order = post('sort_order');
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_user_poll_option($id, $text, $sort_order, $updated_by);
    
    $this->userPollOptions();
}

public function edit_user_poll_vote_by_id(int $id) {
    $option_id  = post('option_id');
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_user_poll_vote($id, $option_id, $updated_by);
    
    $this->userPollVotes();
}

public function edit_user_post_by_id(int $id) {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';
    
    $title      = post('title');
    $content    = post('content');
    $excerpt    = post('excerpt');
    $status     = post('status');
    $visibility = post('visibility');
    $updated_by = post('manager_id') ?: post('updated_by');

    $table_name = 'user_posts';
    $model = new pageModel();
    
    $model->update_user_post($id, $title, $content, $excerpt, $status, $visibility, $updated_by);
    $model->update_new_translation($table_name, $id, $locale, $title, $excerpt, $content, $updated_by);
    
    $this->userPosts();
}

public function edit_user_publication_by_id(int $id) {
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';
    
    $user_id   = post('user_id');
    $title     = post('title');
    $publisher = post('publisher');
    $url       = post('url');
    $published_date = post('published_date');
    $content   = post('content');
    $updated_by= post('manager_id') ?: post('updated_by');

    $table_name = 'user_publications';
    $model = new pageModel();
    
    $model->update_user_publication($id, $user_id, $title, $publisher, $url, $published_date, $content, $updated_by);
    $model->update_new_translation($table_name, $id, $locale, $title, null, $content, $updated_by);
    
    $this->userPublications();
}

public function edit_user_rating_by_id(int $id) {
    $rating     = post('rating');
    $review     = post('review');
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_user_rating($id, $rating, $review, $updated_by);
    
    $this->userRatings();
}

public function edit_user_rating_summary_by_id(int $target_id, string $target_type) {
    $avg_rating  = post('avg_rating');
    $total_votes = post('total_votes');
    $updated_by  = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_user_rating_summary($target_id, $target_type, $avg_rating, $total_votes, $updated_by);
    
    $this->userRatingSummaries();
}

public function edit_user_relationship_by_id(int $id) {
    $status     = post('status');
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_user_relationship($id, $status, $updated_by);
    
    $this->userRelationships();
}

public function edit_user_report_by_id(int $id) {
    $status     = post('status');
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_user_report($id, $status, $updated_by);
    
    $this->userReports();
}

public function edit_user_reputation_by_id(int $user_id) {
    $general_score     = post('general_score');
    $professional_score= post('professional_score');
    $updated_by        = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_user_reputation($user_id, $general_score, $professional_score, $updated_by);
    
    $this->userReputation();
}

public function edit_user_reputation_log_by_id(int $id) {
    $score      = post('score');
    $description= post('description');
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_user_reputation_log($id, $score, $description, $updated_by);
    
    $this->userReputationLogs();
}

public function edit_user_review_by_id(int $id) {
    $rating     = post('rating');
    $comment    = post('comment');
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_user_review($id, $rating, $comment, $updated_by);
    
    $this->userReviews();
}

public function edit_user_role_by_id(int $user_id, int $role_id) {
    $expires_at = post('expires_at');
    $note       = post('note');
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_user_role($user_id, $role_id, $expires_at, $note, $updated_by);
    
    $this->userRoles();
}

public function edit_user_session_by_id(int $id) {
    $expires_at = post('expires_at');
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_user_session($id, $expires_at, $updated_by);
    
    $this->userSessions();
}

public function edit_user_setting_by_id(int $id) {
    $value      = post('value');
    $visibility = post('visibility');
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_user_setting($id, $value, $visibility, $updated_by);
    
    $this->userSettings();
}

public function edit_user_specialty_by_id(int $id) {
    $skill_name      = post('skill_name');
    $level           = post('level');
    $years_experience= post('years_experience');
    $updated_by      = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_user_specialty($id, $skill_name, $level, $years_experience, $updated_by);
    
    $this->userSpecialties();
}

public function edit_user_verification_by_id(int $id) {
    $status     = post('status');
    $notes      = post('notes');
    $updated_by = post('manager_id') ?: post('updated_by');

    $model = new pageModel();
    $model->update_user_verification($id, $status, $notes, $updated_by);
    
    $this->userVerifications();
}




}



