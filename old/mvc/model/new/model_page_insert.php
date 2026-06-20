<?php

trait ModelPageInsertTrait {


    // ===================== academies =====================
    public static function insert_new_academy(int $user_id){
        return Db::getInstance()->insert("INSERT INTO academies (user_id) VALUES (:user_id)", array(
            'user_id' => $user_id,
        ));
    }

    // ===================== academy_branches =====================
    public static function insert_new_academy_branch(int $academy_id, int $user_id, string $timezone, bool $is_main, string $academy_branch_type_id, string $mode){
        return Db::getInstance()->insert("INSERT INTO academy_branches (academy_id, user_id, timezone, is_main, academy_branch_type_id, mode) VALUES (:academy_id, :user_id, :timezone, :is_main, :academy_branch_type_id, :mode)", array(
            'academy_id' => $academy_id,
            'user_id' => $user_id,
            'timezone' => $timezone,
            'is_main' => $is_main,
            'academy_branch_type_id' => $academy_branch_type_id,
            'mode' => $mode,
        ));
    }

    // ===================== academy_branches =====================
    public static function insert_new_academy_branch_type(string $type){
        return Db::getInstance()->insert("INSERT INTO academy_branch_types (type, created_by, updated_by) VALUES (:type, :created_by, :updated_by)", array(
            'type' => $type,
            'created_by' => session_get('user_id'),
            'updated_by' => session_get('user_id'),
        ));
    }


    public static function insert_new_users(string $email, string $username, string $password, string $gender, string $time){
        return Db::getInstance()->insert("INSERT INTO users 
            (email, username, password, gender, register_time, last_visit_time, created_by, updated_by) VALUES 
            (:email, :username, :password, :gender, :register_time, :last_visit_time, :created_by, :updated_by)", 
        array(
            'email' => $email,
            'username' => $username,
            'password' => $password,
            'gender' => $gender,
            'register_time' => $time,
            'last_visit_time' => $time,
            'created_by' => session_get('user_id'),
            'updated_by' => session_get('user_id'),
        ));
    }


    public static function insert_new_title_translation(string $table_name, int $table_id, string $locale, string $title){
        return Db::getInstance()->insert("INSERT INTO translations 
            (table_name, table_id, locale, title, created_by, updated_by) VALUES 
            (:table_name, :table_id, :locale, :title, :created_by, :updated_by)", 
        array(
            'table_name' => $table_name,
            'table_id' => $table_id,
            'locale' => $locale,
            'title' => $title,
            'created_by' => session_get('user_id'),
            'updated_by' => session_get('user_id'),
        ));
    }


    public static function insert_new_translation(string $table_name, int $table_id, string $locale, string $title, string $brief, string $description){
        return Db::getInstance()->insert("INSERT INTO translations 
            (table_name, table_id, locale, title, brief, description, created_by, updated_by) VALUES 
            (:table_name, :table_id, :locale, :title, :brief, :description, :created_by, :updated_by)", 
        array(
            'table_name' => $table_name,
            'table_id' => $table_id,
            'locale' => $locale,
            'title' => $title,
            'brief' => $brief,
            'description' => $description,
            'created_by' => session_get('user_id'),
            'updated_by' => session_get('user_id'),
        ));
    }


    public static function insert_new_version_translation(string $table_name, int $table_id, string $locale, string $title, string $brief, string $description, string $created_at, int $created_by){
        return Db::getInstance()->insert("INSERT INTO translations 
            (table_name, table_id, locale, title, brief, description, created_at, created_by, updated_by) VALUES 
            (:table_name, :table_id, :locale, :title, :brief, :description, :created_at, :created_by, :updated_by)", 
        array(
            'table_name'  => $table_name,
            'table_id'    => $table_id,
            'locale'      => $locale,
            'title'       => $title,
            'brief'       => $brief,
            'description' => $description,
            'created_at'  => $created_at,
            'created_by'  => $created_by,
            'updated_by'  => session_get('user_id'),
        ));
    }


    public static function insert_new_plus1_translation(string $table_name, int $table_id, string $locale, string $title, string $brief, string $description, string $subject_1, string $text_1){
        return Db::getInstance()->insert("INSERT INTO translations 
            (table_name, table_id, locale, title, brief, description, subject_1, text_1, created_by, updated_by) VALUES 
            (:table_name, :table_id, :locale, :title, :brief, :description, :subject_1, :text_1, :created_by, :updated_by)", 
        array(
            'table_name'  => $table_name,
            'table_id'    => $table_id,
            'locale'      => $locale,
            'title'       => $title,
            'brief'       => $brief,
            'description' => $description,
            'subject_1'   => $subject_1,
            'text_1'      => $text_1,
            'created_by'  => session_get('user_id'),
            'updated_by'  => session_get('user_id'),
        ));
    }


    public static function insert_new_plus2_translation(string $table_name, int $table_id, string $locale, string $title, string $brief, string $description, string $subject_1, string $text_1, string $subject_2, string $text_2){
        return Db::getInstance()->insert("INSERT INTO translations 
            (table_name, table_id, locale, title, brief, description, subject_1, text_1, subject_2, text_2, created_by, updated_by) VALUES 
            (:table_name, :table_id, :locale, :title, :brief, :description, :subject_1, :text_1, :subject_2, :text_2, :created_by, :updated_by)", 
        array(
            'table_name'  => $table_name,
            'table_id'    => $table_id,
            'locale'      => $locale,
            'title'       => $title,
            'brief'       => $brief,
            'description' => $description,
            'subject_1'   => $subject_1,
            'text_1'      => $text_1,
            'subject_2'   => $subject_2,
            'text_2'      => $text_2,
            'created_by'  => session_get('user_id'),
            'updated_by'  => session_get('user_id'),
        ));
    }


    public static function insert_new_plus3_translation(string $table_name, int $table_id, string $locale, string $title, string $brief, string $description, string $subject_1, string $text_1, string $subject_2, string $text_2, string $subject_3, string $text_3){
        return Db::getInstance()->insert("INSERT INTO translations 
            (table_name, table_id, locale, title, brief, description, subject_1, text_1, subject_2, text_2, subject_3, text_3, created_by, updated_by) VALUES 
            (:table_name, :table_id, :locale, :title, :brief, :description, :subject_1, :text_1, :subject_2, :text_2, :subject_3, :text_3, :created_by, :updated_by)", 
        array(
            'table_name'  => $table_name,
            'table_id'    => $table_id,
            'locale'      => $locale,
            'title'       => $title,
            'brief'       => $brief,
            'description' => $description,
            'subject_1'   => $subject_1,
            'text_1'      => $text_1,
            'subject_2'   => $subject_2,
            'text_2'      => $text_2,
            'subject_3'   => $subject_3,
            'text_3'      => $text_3,
            'created_by'  => session_get('user_id'),
            'updated_by'  => session_get('user_id'),
        ));
    }


    public static function insert_new_address_translation(string $table_name, int $table_id, string $locale, string $subject_1, string $text_1){
        return Db::getInstance()->insert("INSERT INTO translations 
            (table_name, table_id, locale, subject_1, text_1, created_by, updated_by) VALUES 
            (:table_name, :table_id, :locale, :subject_1, :text_1, :created_by, :updated_by)", 
        array(
            'table_name' => $table_name,
            'table_id'   => $table_id,
            'locale'     => $locale,
            'subject_1'  => $subject_1,
            'text_1'     => $text_1,
            'created_by' => session_get('user_id'),
            'updated_by' => session_get('user_id'),
        ));
    }



    public static function fetch_by_email_in_users(string $email){
        $record = Db::getInstance()->first("SELECT * FROM users WHERE email=:email", array(
            'email' => $email,
        ));
        return $record;
    }



    // ===================== academy_branch_phones =====================
    public static function insert_new_academy_branch_phones(int $branch_id, string $type, string $country_code, string $phone) {
        return Db::getInstance()->insert("INSERT INTO academy_branch_phones
            (branch_id, type, country_code, phone, created_by, updated_by) VALUES
            (:branch_id, :type, :country_code, :phone, :created_by, :updated_by)",
        [
            'branch_id'    => $branch_id,
            'type'         => $type,
            'country_code' => $country_code,
            'phone'        => $phone,
            'created_by'   => session_get('user_id'),
            'updated_by'   => session_get('user_id'),
        ]);
    }




// ==========================================================
// ==========================================================
// ==========================================================
// ==========================================================

    // ===================== academy_branch_bookings =====================
    public static function insert_new_academy_branch_booking(string $requested_date, string $start_time, string $end_time) {
        return Db::getInstance()->insert("INSERT INTO academy_branch_bookings 
            (requested_date, start_time, end_time, created_by, updated_by) 
            VALUES (:requested_date, :start_time, :end_time, :created_by, :updated_by)",
            [
                'requested_date' => $requested_date,
                'start_time'     => $start_time,
                'end_time'       => $end_time,
                'created_by'     => session_get('user_id'),
                'updated_by'     => session_get('user_id'),
            ]);
    }

    // ===================== academy_branch_classroom types =====================
    // public static function insert_new_academy_branch_classroom_types(int $branch_id, string $code)
    public static function insert_new_academy_branch_classroom_types(int $branch_id) {
        return Db::getInstance()->insert("INSERT INTO academy_branch_classroom_types 
            (branch_id, created_by, updated_by) 
            VALUES (:branch_id, :created_by, :updated_by)",
            [
                'branch_id' => $branch_id,
                'created_by' => session_get('user_id'),
                'updated_by' => session_get('user_id'),
            ]);
    }

    // ===================== academy_branch_classrooms =====================
    public static function insert_new_academy_branch_classroom(int $branch_id, int $type_id, ?int $capacity = null, bool $is_active = true, string $status = 'available') {
        return Db::getInstance()->insert("INSERT INTO academy_branch_classrooms 
            (branch_id, type_id, capacity, is_active, status, created_by, updated_by) 
            VALUES (:branch_id, :type_id, :capacity, :is_active, :status, :created_by, :updated_by)",
            [
                'branch_id'  => $branch_id,
                'type_id'    => $type_id,
                'capacity'   => $capacity,
                'is_active'  => $is_active,
                'status'     => $status,
                'created_by' => session_get('user_id'),
                'updated_by' => session_get('user_id'),
            ]);
    }

    // ===================== academy_branch_classroom_assets =====================
    public static function insert_new_academy_branch_classroom_asset(int $classroom_id, int $quantity) {
        return Db::getInstance()->insert("INSERT INTO academy_branch_classroom_assets (classroom_id, quantity, created_by, updated_by) 
            VALUES (:classroom_id, :quantity, :created_by, :updated_by)",
            [
                'classroom_id' => $classroom_id,
                'quantity'     => $quantity,
                'created_by'   => session_get('user_id'),
                'updated_by'   => session_get('user_id')
            ]
        );
    }

    // ===================== academy_branch_contracts =====================
    public static function insert_new_academy_branch_contract(int $member_id, string $type, string $start_date, ?string $end_date = null, float $price, int $currency_id) {
        return Db::getInstance()->insert("INSERT INTO academy_branch_member_contracts 
            (member_id, type, start_date, end_date, price, currency_id, created_by, updated_by) 
            VALUES (:member_id, :type, :start_date, :end_date, :price, :currency_id, :created_by, :updated_by)",
            [
                'member_id'   => $member_id,
                'type'        => $type,
                'start_date'  => $start_date,
                'end_date'    => $end_date,
                'price'       => $price,
                'currency_id' => $currency_id,
                'created_by'  => session_get('user_id'),
                'updated_by'  => session_get('user_id'),
            ]
        );
    }

    // ===================== academy_branch_courses =====================
    public static function insert_new_academy_branch_course(int $branch_id, ?int $level_id = null, ?int $capacity = null) {
        return Db::getInstance()->insert("INSERT INTO academy_branch_courses 
            (branch_id, level_id, capacity, created_by, updated_by) 
            VALUES (:branch_id, :level_id, :capacity, :created_by, :updated_by)",
            [
                'branch_id'  => $branch_id,
                'level_id'   => $level_id,
                'capacity'   => $capacity,
                'created_by' => session_get('user_id'),
                'updated_by' => session_get('user_id'),
            ]);
    }

    // ===================== academy_branch_course_terms =====================
    public static function insert_new_academy_branch_course_term(int $course_id, string $start_date, string $end_date, int $session_count, ?string $price = null, ?int $currency_id = null) {
        return Db::getInstance()->insert("INSERT INTO academy_branch_course_terms 
            (course_id, start_date, end_date, session_count, price, currency_id, created_by, updated_by) 
            VALUES (:course_id, :start_date, :end_date, :session_count, :price, :currency_id, :created_by, :updated_by)",
            [
                'course_id'     => $course_id,
                'start_date'    => $start_date,
                'end_date'      => $end_date,
                'session_count' => $session_count,
                'price'         => $price,
                'currency_id'   => $currency_id,
                // 'status'        => $status,
                'created_by'    => session_get('user_id'),
                'updated_by'    => session_get('user_id'),
            ]);
    }

    // ===================== academy_branch_course_term_enrollments =====================
    public static function insert_new_academy_branch_course_term_enrollment(int $term_id, int $member_id, string $type, string $joined_at) {
        return Db::getInstance()->insert("INSERT INTO academy_branch_course_term_enrollments 
            (term_id, member_id, type, joined_at, created_by, updated_by) 
            VALUES (:term_id, :member_id, :type, :joined_at, :created_by, :updated_by)",
            [
                'term_id'    => $term_id,
                'member_id' => $member_id,
                'type'       => $type,
                'joined_at'  => $joined_at,
                'created_by' => session_get('user_id'),
                'updated_by' => session_get('user_id'),
            ]);
    }

    // ===================== academy_branch_course_term_invoices =====================
    public static function insert_new_academy_branch_course_term_invoice(int $term_id, int $member_id,  int $discount_id, string $payable_amount, ?int $currency_id = null, string $issued_at, ?string $due_date = null) {
        return Db::getInstance()->insert("INSERT INTO academy_branch_course_term_invoices 
            (term_id, member_id, discount_id, payable_amount, currency_id, issued_at, due_date, created_by, updated_by) 
            VALUES (:term_id, :member_id, :discount_id, :payable_amount, :currency_id, :issued_at, :due_date, :created_by, :updated_by)",
            [
                'term_id'        => $term_id,
                'member_id'      => $member_id,
                'discount_id' => $discount_id,
                'payable_amount' => $payable_amount,
                'currency_id'    => $currency_id,
                'issued_at'      => $issued_at,
                'due_date'       => $due_date,
                'created_by'     => session_get('user_id'),
                'updated_by'     => session_get('user_id'),
            ]);
    }

    // ===================== financial_system_discounts =====================
    public static function insert_new_financial_system_discount(string $type, float $value, int $max_usage, string $start_date, string $end_date) {
        return Db::getInstance()->insert("INSERT INTO financial_system_discounts 
            (type, value, max_usage, used_count, start_date, end_date, created_by, updated_by) 
            VALUES (:type, :value, :max_usage, :used_count, :start_date, :end_date, :created_by, :updated_by)",
            [
                'type'           => $type,
                'value'          => $value,
                'max_usage'      => $max_usage,
                'used_count'     => 0,
                'start_date'     => $start_date,
                'end_date'       => $end_date,
                'created_by'     => session_get('user_id'),
                'updated_by'     => session_get('user_id'),
            ]);
    }

    // ===================== academy_branch_course_term_invoice_installments =====================
    public static function insert_new_academy_branch_course_term_invoice_installment(int $invoice_id, int $installment_number, float $amount, string $due_date, string $status) {
        return Db::getInstance()->insert("INSERT INTO academy_branch_course_term_invoice_installments 
            (invoice_id, installment_number, amount, due_date, status, created_by, updated_by) 
            VALUES (:invoice_id, :installment_number, :amount, :due_date, :status, :created_by, :updated_by)",
            [
                'invoice_id'         => $invoice_id,
                'installment_number' => $installment_number,
                'amount'             => $amount,
                'due_date'           => $due_date,
                'status'             => $status,
                'created_by'         => session_get('user_id'),
                'updated_by'         => session_get('user_id'),
            ]);
    }

/*
    term_invoice_installment_id
	invoice_id
	installment_number
	amount
	due_date
	status 	        (pending,approved,rejected,paid,underpaid)
	paid_at
*/

    // ===================== academy_branch_course_term_sessions =====================
    public static function insert_new_academy_branch_course_term_session(int $term_id, ?int $booking_id = null, ?int $classroom_id = null, ?int $branch_url_id = null) {
        return Db::getInstance()->insert("INSERT INTO academy_branch_course_term_sessions 
            (term_id, booking_id, classroom_id, branch_url_id, created_by, updated_by) 
            VALUES (:term_id, :booking_id, :classroom_id, :branch_url_id, :created_by, :updated_by)",
            [
                'term_id'         => $term_id,
                'booking_id'      => $booking_id,
                'classroom_id'    => $classroom_id,
                'branch_url_id'   => $branch_url_id,
                'created_by'      => session_get('user_id'),
                'updated_by'      => session_get('user_id'),
            ]);
    }



    // ===================== academy_branch_course_term_session_attendance =====================
    public static function insert_new_academy_branch_course_term_session_attendance(int $session_id, int $term_enrollment_id, int $member_id, string $status) {
        return Db::getInstance()->insert("INSERT INTO academy_branch_course_term_session_attendances 
            (session_id, term_enrollment_id, member_id, status, created_by, updated_by) 
            VALUES (:session_id, :term_enrollment_id, :member_id, :status, :created_by, :updated_by)",
            [
                'session_id'         => $session_id,
                'term_enrollment_id' => $term_enrollment_id,
                'member_id'          => $member_id,
                'status'             => $status,
                'created_by'         => session_get('user_id'),
                'updated_by'         => session_get('user_id'),
            ]);
    }

    // ===================== academy_branch_course_term_session_classrooms =====================
    public static function insert_new_academy_branch_course_term_session_classroom(int $session_id, int $classroom_id) {
        return Db::getInstance()->insert("INSERT INTO academy_branch_course_term_session_classrooms 
            (session_id, classroom_id, created_by, updated_by) VALUES (:session_id, :classroom_id, :created_by, :updated_by)",
            [
                'session_id'   => $session_id,
                'classroom_id' => $classroom_id,
                'created_by'   => session_get('user_id'),
                'updated_by'   => session_get('user_id')
            ]);
    }

    // ===================== academy_branch_course_term_teachers =====================
    public static function insert_new_academy_branch_course_term_teacher(int $term_id, int $academy_member_id) {
        return Db::getInstance()->insert("INSERT INTO academy_branch_course_term_teachers 
            (term_id, academy_member_id) VALUES (:term_id, :academy_member_id)",
            ['term_id' => $term_id, 'academy_member_id' => $academy_member_id]);
    }

    // ===================== academy_branch_course_term_waiting_list =====================
    public static function insert_new_academy_branch_course_term_waiting_list(int $term_id, int $member_id) {
        return Db::getInstance()->insert("INSERT INTO academy_branch_course_term_waiting_list 
            (term_id, member_id, created_by, updated_by) VALUES (:term_id, :member_id, :created_by, :updated_by)",
            [
                'term_id'    => $term_id,
                'member_id'  => $member_id,
                'created_by' => session_get('user_id'),
                'updated_by' => session_get('user_id'),
            ]);
    }

    // ==========================================
    //              USER MAIN TABLE
    // ==========================================

    // ===================== users =====================
    public static function insert_new_user(string $username, string $gender) {
        return Db::getInstance()->insert("INSERT INTO users 
            (username, gender, created_by, updated_by) 
            VALUES (:username, :gender, :created_by, :updated_by)",
            [
                'username' => $username,
                'gender'   => $gender,
                'created_by' => session_get('user_id'),
                'updated_by' => session_get('user_id'),
            ]);
    }

    // ===================== user_availabilities =====================
    public static function insert_new_user_availability(int $user_id, string $date, string $day_of_week, string $timezone, string $start_time, string $end_time, string $type, bool $is_repeating, string $repeat_period) {
        return Db::getInstance()->insert("INSERT INTO user_availabilities 
            (user_id, date, day_of_week, timezone, start_time, end_time, type, is_repeating, repeat_period, created_by, updated_by) 
            VALUES (:user_id, :date, :day_of_week, :timezone, :start_time, :end_time, :type, :is_repeating, :repeat_period, :created_by, :updated_by)",
            [
                'user_id' => $user_id,
                'date'   => $date,
                'day_of_week'   => $day_of_week,
                'timezone'   => $timezone,
                'start_time'   => $start_time,
                'end_time'   => $end_time,
                'type'   => $type,
                'is_repeating'   => $is_repeating,
                'repeat_period'   => $repeat_period,
                'created_by' => session_get('user_id'),
                'updated_by' => session_get('user_id'),
            ]);
    }

    // ===================== user_availability_exception =====================
    public static function insert_new_user_availability_exceptions(int $user_id, string $date, string $start_time, string $end_time, string $type) {
        return Db::getInstance()->insert("INSERT INTO user_availability_exceptions
            (user_id, date, start_time, end_time, type, created_by, updated_by) 
            VALUES (:user_id, :date, :start_time, :end_time, :type, :created_by, :updated_by)",
            [
                'user_id'    => $user_id,
                'date'       => $date,
                'start_time' => $start_time,
                'end_time'   => $end_time,
                'type'       => $type,
                'created_by' => session_get('user_id'),
                'updated_by' => session_get('user_id'),
            ]);
    }

    // ===================== academy_branch_members =====================
    public static function insert_new_academy_branch_member(int $branch_id, int $user_id, ?int $role_id = null, string $status = 'pending', string $joined_at = null) {
        return Db::getInstance()->insert("INSERT INTO academy_branch_members 
            (branch_id, user_id, role_id, status, joined_at, created_by, updated_by) 
            VALUES (:branch_id, :user_id, :role_id, :status, :joined_at, :created_by, :updated_by)",
            [
                'branch_id'  => $branch_id,
                'user_id'    => $user_id,
                'role_id'    => $role_id,
                'status'     => $status,
                'joined_at'  => $joined_at,
                'created_by' => session_get('user_id'),
                'updated_by' => session_get('user_id'),
            ]);
    }

    // ===================== academy_branch_member_permissions =====================
    public static function insert_new_academy_branch_member_permission(int $academy_member_id, int $permission_id) {
        return Db::getInstance()->insert("INSERT INTO academy_branch_member_permissions 
            (academy_member_id, permission_id) VALUES (:academy_member_id, :permission_id)",
            ['academy_member_id' => $academy_member_id, 'permission_id' => $permission_id]);
    }

    // ===================== academy_branch_member_schedules =====================
    public static function insert_new_academy_branch_member_schedule(int $academy_member_id, int $branch_id, int $day_of_week, string $start_time, string $end_time, string $availability_type = 'available') {
        return Db::getInstance()->insert("INSERT INTO academy_branch_member_schedules 
            (academy_member_id, branch_id, day_of_week, start_time, end_time, availability_type, source, created_by) 
            VALUES (:academy_member_id, :branch_id, :day_of_week, :start_time, :end_time, :availability_type, 'user', :created_by)",
            [
                'academy_member_id' => $academy_member_id,
                'branch_id'         => $branch_id,
                'day_of_week'       => $day_of_week,
                'start_time'        => $start_time,
                'end_time'          => $end_time,
                'availability_type' => $availability_type,
                'created_by'        => session_get('user_id'),
            ]);
    }

    // ===================== academy_branch_scheduling_queue =====================
    public static function insert_new_academy_branch_scheduling_queue(int $reference_id, string $type = 'booking', int $priority = 0) {
        return Db::getInstance()->insert("INSERT INTO academy_branch_scheduling_queue 
            (reference_id, created_by, type, priority, status) 
            VALUES (:reference_id, :created_by, :type, :priority, 'pending')",
            [
                'reference_id' => $reference_id,
                'created_by'   => session_get('user_id'),
                'type'         => $type,
                'priority'     => $priority,
            ]);
    }

    // ===================== academy_branch_scheduling_rules =====================
    public static function insert_new_academy_branch_scheduling_rule(int $branch_id, ?int $max_sessions_per_day = null, ?int $min_break_minutes = null, ?int $session_duration = null, bool $allow_overlap = false) {
        return Db::getInstance()->insert("INSERT INTO academy_branch_scheduling_rules 
            (branch_id, max_sessions_per_day, min_break_minutes, session_duration, allow_overlap) 
            VALUES (:branch_id, :max_sessions_per_day, :min_break_minutes, :session_duration, :allow_overlap)",
            [
                'branch_id'            => $branch_id,
                'max_sessions_per_day' => $max_sessions_per_day,
                'min_break_minutes'    => $min_break_minutes,
                'session_duration'     => $session_duration,
                'allow_overlap'        => $allow_overlap,
            ]);
    }

    // ===================== academy_branch_urls =====================
    public static function insert_new_user_contact(int $user_id, string $value, string $mode, string $platform, string $priority, bool $is_main) {
        return Db::getInstance()->insert("INSERT INTO user_contacts 
            (user_id, value, mode, platform, priority, is_main, status, created_by, updated_by) 
            VALUES (:user_id, :value, :mode, :platform, :priority, :is_main, 'active', :created_by, :updated_by)",
            [
                'user_id'    => $user_id,
                'value'      => $value,
                'mode'       => $mode,
                'platform'   => $platform,
                'priority'   => $priority,
                'is_main'    => $is_main,
                'created_by' => session_get('user_id'),
                'updated_by' => session_get('user_id'),
            ]);
    }

    // ===================== user_addresses =====================
    public static function insert_new_user_address_without_lat_long(int $addresses_table_id, string $addresses_table_name, int $country_id, int $state_id, int $city_id, string $postal_code, bool $is_main) {
        return Db::getInstance()->insert("INSERT INTO user_addresses 
            (addresses_table_id, addresses_table_name, country_id, state_id, city_id, postal_code, is_main, created_by, updated_by) 
            VALUES (:addresses_table_id, :addresses_table_name, :country_id, :state_id, :city_id, :postal_code, :is_main, :created_by, :updated_by)",
            [
                'addresses_table_id'   => $addresses_table_id,
                'addresses_table_name' => $addresses_table_name,
                'country_id'           => $country_id,
                'state_id'             => $state_id,
                'city_id'              => $city_id,
                'postal_code'          => $postal_code,
                'is_main'              => $is_main,
                'created_by'           => session_get('user_id'),
                'updated_by'           => session_get('user_id'),
            ]);
    }

    public static function insert_new_user_address_with_lat_long(int $addresses_table_id, string $addresses_table_name, int $country_id, int $state_id, int $city_id, float $latitude, float $longitude, string $postal_code, bool $is_main) {
        return Db::getInstance()->insert("INSERT INTO user_addresses 
            (addresses_table_id, addresses_table_name, country_id, state_id, city_id, latitude, longitude, postal_code, is_main, created_by, updated_by) 
            VALUES (:addresses_table_id, :addresses_table_name, :country_id, :state_id, :city_id, :latitude, :longitude, :postal_code, :is_main, :created_by, :updated_by)",
            [
                'addresses_table_id'   => $addresses_table_id,
                'addresses_table_name' => $addresses_table_name,
                'country_id'           => $country_id,
                'state_id'             => $state_id,
                'city_id'              => $city_id,
                'latitude'             => $latitude,
                'longitude'            => $longitude,
                'postal_code'          => $postal_code,
                'is_main'              => $is_main,
                'created_by'           => session_get('user_id'),
                'updated_by'           => session_get('user_id'),
            ]);
    }






// ==============================================================================================================================================================================
// ==============================================================================================================================================================================
// ==============================================================================================================================================================================
// ==============================================================================================================================================================================
// ==============================================================================================================================================================================
// ==============================================================================================================================================================================
// ==============================================================================================================================================================================
// ==============================================================================================================================================================================
// ==============================================================================================================================================================================

    // ===================== access_system_permissions =====================
    public static function insert_new_access_system_permission(string $name, string $group_name){
        return Db::getInstance()->insert("INSERT INTO access_system_permissions 
            (name, group_name, created_by, updated_by) VALUES 
            (:name, :group_name, :created_by, :updated_by)"
        , array(
            'name'       => $name,
            'group_name' => $group_name,
            'created_by' => session_get('user_id'),
            'updated_by' => session_get('user_id'),
        ));
    }

    // ===================== access_system_roles =====================

    public static function insert_new_access_system_role(string $name, string $type, string $color, int $sort_order) {
        return Db::getInstance()->insert("INSERT INTO access_system_roles 
            (name, type, color, sort_order, created_by, updated_by) 
            VALUES (:name, :type, :color, :sort_order, :created_by, :updated_by)",
            array(
                'name'       => $name,
                'type'       => $type,
                'color'      => $color,
                'sort_order' => $sort_order,
                'created_by' => session_get('user_id'),
                'updated_by' => session_get('user_id'),
            ));
    }

    // ===================== access_system_role_permissions =====================
    public static function insert_new_access_system_role_permission(int $role_id, int $permission_id) {
        return Db::getInstance()->insert("INSERT INTO access_system_role_permissions 
            (role_id, permission_id, created_by, updated_by) VALUES (:role_id, :permission_id, :created_by, :updated_by)",
        array(
            'role_id'       => $role_id,
            'permission_id' => $permission_id,
                'created_by' => session_get('user_id'),
                'updated_by' => session_get('user_id'),
        ));
    }

    // ===================== access_system_setting_permissions =====================
    public static function insert_new_access_system_setting_permission(int $setting_id, int $permission_id) {
        return Db::getInstance()->insert("INSERT INTO access_system_setting_permissions 
            (setting_id, permission_id, created_by, updated_by) VALUES (:setting_id, :permission_id, :created_by, :updated_by)",
        array(
            'setting_id'    => $setting_id,
            'permission_id' => $permission_id,
                'created_by' => session_get('user_id'),
                'updated_by' => session_get('user_id'),
        ));
    }

    // ===================== conversations =====================
    public static function insert_new_conversation(?int $last_message_id = null, string $type = 'direct', ?string $title = null) {
        return Db::getInstance()->insert("INSERT INTO conversations 
            (last_message_id, type, title, created_at) 
            VALUES (:last_message_id, :type, :title, NOW())",
            array(
                'last_message_id' => $last_message_id,
                'type' => $type,
                'title' => $title
            ));
    }

    // ===================== conversation_members =====================
    public static function insert_new_conversation_member(int $conversation_id, int $user_id, ?string $role = 'member', int $is_muted = 0) {
        return Db::getInstance()->insert("INSERT INTO conversation_members 
            (conversation_id, user_id, role, is_muted, joined_at) 
            VALUES (:conversation_id, :user_id, :role, :is_muted, NOW())",
            array(
                'conversation_id' => $conversation_id,
                'user_id' => $user_id,
                'role' => $role,
                'is_muted' => $is_muted
            ));
    }

    // ===================== financial_system_currency =====================
    public static function insert_new_financial_system_currency(string $brief, ?string $icon_path = null) {
        return Db::getInstance()->insert("INSERT INTO financial_system_currency 
            (brief, icon_path) 
            VALUES (:brief, :icon_path)",
            array(
                'brief' => $brief,
                'icon_path' => $icon_path
            ));
    }

    // ===================== financial_system_ledger_entries =====================
    public static function insert_new_financial_system_ledger_entry(int $account_id, ?int $reference_id = null, ?int $transaction_id = null, string $type, string $amount, ?string $reference_type = null, ?string $description = null) {
        return Db::getInstance()->insert("INSERT INTO financial_system_ledger_entries 
            (account_id, reference_id, transaction_id, type, amount, reference_type, description, created_at) 
            VALUES (:account_id, :reference_id, :transaction_id, :type, :amount, :reference_type, :description, NOW())",
            array(
                'account_id' => $account_id,
                'reference_id' => $reference_id,
                'transaction_id' => $transaction_id,
                'type' => $type,
                'amount' => $amount,
                'reference_type' => $reference_type,
                'description' => $description
            ));
    }

    // ===================== financial_system_refunds =====================
    public static function insert_new_financial_system_refund(int $payment_id, string $amount, ?string $reason = null, string $status = 'pending') {
        return Db::getInstance()->insert("INSERT INTO financial_system_refunds 
            (payment_id, amount, reason, status, created_at) 
            VALUES (:payment_id, :amount, :reason, :status, NOW())",
            array(
                'payment_id' => $payment_id,
                'amount' => $amount,
                'reason' => $reason,
                'status' => $status
            ));
    }

    // ===================== financial_system_transactions =====================
    public static function insert_new_financial_system_transaction(string $type = 'payment') {
        return Db::getInstance()->insert("INSERT INTO financial_system_transactions 
            (type, created_at) 
            VALUES (:type, NOW())",
            array('type' => $type));
    }

    // ===================== instruments =====================
    public static function insert_new_instrument() {
        return Db::getInstance()->insert("INSERT INTO instruments 
            (created_by, updated_by) 
            VALUES (:created_by, :updated_by)",
            array(
                'created_by' => session_get('user_id'),
                'updated_by' => session_get('user_id')
            ));
    }

    public static function insert_new_version_instrument(string $created_at, int $created_by) {
        return Db::getInstance()->insert("INSERT INTO instruments 
            (created_at, created_by, updated_by) 
            VALUES (:created_at, :created_by, :updated_by)",
            array(
                'created_at' => $created_at,
                'created_by' => $created_by,
                'updated_by' => session_get('user_id')
            ));
    }

    // ===================== lessons =====================
    public static function insert_new_lesson() {
        return Db::getInstance()->insert("INSERT INTO lessons 
            (created_by, updated_by) 
            VALUES (:created_by, :updated_by)",
            array(
                'created_by' => session_get('user_id'),
                'updated_by' => session_get('user_id')
            ));
    }

    public static function insert_new_version_lesson(string $created_at, int $created_by) {
        return Db::getInstance()->insert("INSERT INTO lessons 
            (created_at, created_by, updated_by) 
            VALUES (:created_at, :created_by, :updated_by)",
            array(
                'created_at' => $created_at,
                'created_by' => $created_by,
                'updated_by' => session_get('user_id')
            ));
    }

    // ===================== languages =====================
    public static function insert_new_language(string $code, ?string $name = null, ?string $direction = 'ltr', int $is_active = 1) {
        return Db::getInstance()->insert("INSERT INTO languages 
            (code, name, direction, is_active) 
            VALUES (:code, :name, :direction, :is_active)",
            array(
                'code' => $code,
                'name' => $name,
                'direction' => $direction,
                'is_active' => $is_active
            ));
    }

    // ===================== levels =====================
    public static function insert_new_level(string $type, ?int $sort_order = 0, int $is_active = 1) {
        return Db::getInstance()->insert("INSERT INTO levels 
            (type, sort_order, is_active) 
            VALUES (:type, :sort_order, :is_active)",
            array(
                'type' => $type,
                'sort_order' => $sort_order,
                'is_active' => $is_active
            ));
    }

    // ===================== otp_codes =====================
    public static function insert_new_otp_code(string $target, string $type, string $code, string $purpose, string $expires_at) {
        return Db::getInstance()->insert("INSERT INTO otp_codes 
            (target, type, code, purpose, expires_at) 
            VALUES (:target, :type, :code, :purpose, :expires_at)",
            array(
                'target' => $target,
                'type' => $type,
                'code' => $code,
                'purpose' => $purpose,
                'expires_at' => $expires_at
            ));
    }

    // ===================== password_resets =====================
    public static function insert_new_password_reset(string $email, string $token, string $expires_at) {
        return Db::getInstance()->insert("INSERT INTO password_resets 
            (email, token, expires_at) 
            VALUES (:email, :token, :expires_at)",
            array(
                'email' => $email,
                'token' => $token,
                'expires_at' => $expires_at
            ));
    }

    // ===================== settings =====================
    public static function insert_new_setting(string $key, ?string $group = null, ?string $type = null, ?string $value = null, int $is_active = 1) {
        return Db::getInstance()->insert("INSERT INTO settings 
            (`key`, `group`, type, value, is_active) 
            VALUES (:key, :group, :type, :value, :is_active)",
            array(
                'key' => $key,
                'group' => $group,
                'type' => $type,
                'value' => $value,
                'is_active' => $is_active
            ));
    }

    // ===================== system_events =====================
    public static function insert_new_system_event(?int $reference_id = null, ?string $type = null, ?string $data = null) {
        return Db::getInstance()->insert("INSERT INTO system_events 
            (reference_id, type, data) 
            VALUES (:reference_id, :type, :data)",
            array(
                'reference_id' => $reference_id,
                'type' => $type,
                'data' => $data
            ));
    }

    // ===================== tags =====================
    public static function insert_new_tag(string $name, ?string $slug = null) {
        return Db::getInstance()->insert("INSERT INTO tags 
            (name, slug) 
            VALUES (:name, :slug)",
            array(
                'name' => $name,
                'slug' => $slug
            ));
    }

    // ===================== taggables =====================
    public static function insert_new_taggable(int $tag_id, int $entity_id, string $entity_type) {
        return Db::getInstance()->insert("INSERT INTO taggables 
            (tag_id, entity_id, entity_type) 
            VALUES (:tag_id, :entity_id, :entity_type)",
            array(
                'tag_id' => $tag_id,
                'entity_id' => $entity_id,
                'entity_type' => $entity_type
            ));
    }

    // ===================== user_approvals =====================
    public static function insert_new_user_approval(int $by_user_id, int $entity_id, string $entity_type, string $action, ?string $note = null) {
        return Db::getInstance()->insert("INSERT INTO user_approvals 
            (by_user_id, entity_id, entity_type, action, note) 
            VALUES (:by_user_id, :entity_id, :entity_type, :action, :note)",
            array(
                'by_user_id' => $by_user_id,
                'entity_id' => $entity_id,
                'entity_type' => $entity_type,
                'action' => $action,
                'note' => $note
            ));
    }

    // ===================== user_audit_logs =====================
    public static function insert_new_user_audit_log(int $user_id, ?int $entity_id = null, ?string $entity_type = null, string $action, ?string $ip = null, ?string $user_agent = null, ?string $old_data = null, ?string $new_data = null) {
        return Db::getInstance()->insert("INSERT INTO user_audit_logs 
            (user_id, entity_id, entity_type, action, ip, user_agent, old_data, new_data) 
            VALUES (:user_id, :entity_id, :entity_type, :action, :ip, :user_agent, :old_data, :new_data)",
            array(
                'user_id' => $user_id,
                'entity_id' => $entity_id,
                'entity_type' => $entity_type,
                'action' => $action,
                'ip' => $ip,
                'user_agent' => $user_agent,
                'old_data' => $old_data,
                'new_data' => $new_data
            ));
    }

    // ===================== user_badges =====================
    public static function insert_new_user_badge(int $user_id, int $verification_level_id, ?int $granted_by = null) {
        return Db::getInstance()->insert("INSERT INTO user_badges 
            (user_id, verification_level_id, granted_by, granted_at) 
            VALUES (:user_id, :verification_level_id, :granted_by, NOW())",
            array(
                'user_id' => $user_id,
                'verification_level_id' => $verification_level_id,
                'granted_by' => $granted_by
            ));
    }

    // ===================== user_comments =====================
    public static function insert_new_user_comment(int $post_id, ?int $user_id = null, ?string $guest_name = null, ?string $content = null, ?int $parent_id = null, string $status = 'pending') {
        return Db::getInstance()->insert("INSERT INTO user_comments 
            (post_id, user_id, guest_name, content, parent_id, status) 
            VALUES (:post_id, :user_id, :guest_name, :content, :parent_id, :status)",
            array(
                'post_id' => $post_id,
                'user_id' => $user_id,
                'guest_name' => $guest_name,
                'content' => $content,
                'parent_id' => $parent_id,
                'status' => $status
            ));
    }

    // ===================== user_reputation_logs =====================
    public static function insert_new_user_reputation_log(int $user_id, ?int $source_id = null, string $type, int $score, ?string $source_type = null, ?string $description = null) {
        return Db::getInstance()->insert("INSERT INTO user_reputation_logs 
            (user_id, source_id, type, score, source_type, description) 
            VALUES (:user_id, :source_id, :type, :score, :source_type, :description)",
            array(
                'user_id' => $user_id,
                'source_id' => $source_id,
                'type' => $type,
                'score' => $score,
                'source_type' => $source_type,
                'description' => $description
            ));
    }

    // ===================== user_reviews =====================
    public static function insert_new_user_review(int $user_id, int $target_id, string $target_type, int $rating, ?string $comment = null) {
        return Db::getInstance()->insert("INSERT INTO user_reviews 
            (user_id, target_id, target_type, rating, comment) 
            VALUES (:user_id, :target_id, :target_type, :rating, :comment)",
            array(
                'user_id' => $user_id,
                'target_id' => $target_id,
                'target_type' => $target_type,
                'rating' => $rating,
                'comment' => $comment
            ));
    }

    // ===================== user_roles =====================
    public static function insert_new_user_role(int $user_id, int $role_id) {
        return Db::getInstance()->insert("INSERT INTO user_roles 
            (user_id, role_id, granted_by) 
            VALUES (:user_id, :role_id, :granted_by)",
        array(
            'user_id' => $user_id,
            'role_id' => $role_id,
            'granted_by' => session_get('user_id')
        ));
    }

    // public static function insert_new_user_role(int $user_id, int $role_id, ?int $granted_by = null) {
    //     return Db::getInstance()->insert("INSERT INTO user_roles 
    //         (user_id, role_id, granted_by, granted_at) 
    //         VALUES (:user_id, :role_id, :granted_by, NOW())",
    //         array(
    //             'user_id' => $user_id,
    //             'role_id' => $role_id,
    //             'granted_by' => $granted_by
    //         ));
    // }

    // ===================== user_specialties =====================
    public static function insert_new_user_specialty(int $user_id, ?int $instrument_id = null, string $skill_name, string $level = 'beginner', int $years_experience = 0) {
        return Db::getInstance()->insert("INSERT INTO user_specialties 
            (user_id, instrument_id, skill_name, level, years_experience) 
            VALUES (:user_id, :instrument_id, :skill_name, :level, :years_experience)",
            array(
                'user_id' => $user_id,
                'instrument_id' => $instrument_id,
                'skill_name' => $skill_name,
                'level' => $level,
                'years_experience' => $years_experience
            ));
    }

    // ===================== user_verifications =====================
    public static function insert_new_user_verification(int $user_id, ?int $target_id = null, ?string $type = null, string $status = 'pending', ?int $reviewed_by = null) {
        return Db::getInstance()->insert("INSERT INTO user_verifications 
            (user_id, target_id, type, status, reviewed_by, requested_at) 
            VALUES (:user_id, :target_id, :type, :status, :reviewed_by, NOW())",
            array(
                'user_id' => $user_id,
                'target_id' => $target_id,
                'type' => $type,
                'status' => $status,
                'reviewed_by' => $reviewed_by
            ));
    }

    // ===================== verification_levels =====================
    public static function insert_new_verification_level(string $code, ?int $priority = 0, ?string $icon = null, ?string $color = null, int $is_public = 1) {
        return Db::getInstance()->insert("INSERT INTO verification_levels 
            (code, priority, icon, color, is_public) 
            VALUES (:code, :priority, :icon, :color, :is_public)",
            array(
                'code' => $code,
                'priority' => $priority,
                'icon' => $icon,
                'color' => $color,
                'is_public' => $is_public
            ));
    }

    // ==========================================
    //              USER PROFILE & INFO
    // ==========================================

    public static function insert_new_user_profile(int $user_id, ?int $student_level_id = null, ?string $start_career_date = null, ?int $picture_media_id = null) {
        return Db::getInstance()->insert("INSERT INTO user_profiles 
            (user_id, student_level_id, start_career_date, picture_media_id) 
            VALUES (:user_id, :student_level_id, :start_career_date, :picture_media_id)",
            array(
                'user_id' => $user_id,
                'student_level_id' => $student_level_id,
                'start_career_date' => $start_career_date,
                'picture_media_id' => $picture_media_id
            ));
    }

    public static function insert_new_user_addresses(int $user_id, ?int $country_id = null, ?int $state_id = null, ?int $city_id = null, ?string $address = null, ?string $postal_code = null) {
        return Db::getInstance()->insert("INSERT INTO user_addresses 
            (user_id, country_id, state_id, city_id, address, postal_code) 
            VALUES (:user_id, :country_id, :state_id, :city_id, :address, :postal_code)",
            array(
                'user_id' => $user_id,
                'country_id' => $country_id,
                'state_id' => $state_id,
                'city_id' => $city_id,
                'address' => $address,
                'postal_code' => $postal_code
            ));
    }

    // ==========================================
    //              USER EDUCATION & EXPERIENCE
    // ==========================================

    public static function insert_new_user_education_without_end_date(int $user_id, string $start_date) {
        return Db::getInstance()->insert("INSERT INTO user_educations 
            (user_id, start_date, created_by, updated_by) 
            VALUES (:user_id, :start_date, :created_by, :updated_by)",
            array(
                'user_id'    => $user_id,
                'start_date' => $start_date,
                'created_by' => session_get('user_id'),
                'updated_by' => session_get('user_id'),
            ));
    }
    
    public static function insert_new_user_education_with_end_date(int $user_id, string $start_date, string $end_date) {
        return Db::getInstance()->insert("INSERT INTO user_educations 
            (user_id, start_date, end_date, created_by, updated_by) 
            VALUES (:user_id, :start_date, :end_date, :created_by, :updated_by)",
            array(
                'user_id'    => $user_id,
                'start_date' => $start_date,
                'end_date'   => $end_date,
                'created_by' => session_get('user_id'),
                'updated_by' => session_get('user_id'),
            ));
    }
    

    public static function insert_new_user_experience_without_end_date(int $user_id, int $address_id, string $start_date) {
        return Db::getInstance()->insert("INSERT INTO user_experiences 
            (user_id, address_id, start_date, created_by, updated_by) 
            VALUES (:user_id, :address_id, :start_date, :created_by, :updated_by)",
            array(
                'user_id'    => $user_id,
                'address_id' => $address_id,
                'start_date' => $start_date,
                'created_by' => session_get('user_id'),
                'updated_by' => session_get('user_id')
            ));
    }
    public static function insert_new_user_experience_with_end_date(int $user_id, int $address_id, string $start_date, string $end_date) {
        return Db::getInstance()->insert("INSERT INTO user_experiences 
            (user_id, address_id, start_date, end_date, created_by, updated_by) 
            VALUES (:user_id, :address_id, :start_date, :end_date, :created_by, :updated_by)",
            array(
                'user_id'    => $user_id,
                'address_id' => $address_id,
                'start_date' => $start_date,
                'end_date'   => $end_date,
                'created_by' => session_get('user_id'),
                'updated_by' => session_get('user_id')
            ));
    }
    
    public static function insert_new_user_award(int $user_id, string $date) {
        return Db::getInstance()->insert("INSERT INTO user_awards 
            (user_id, date, created_by, updated_by) 
            VALUES (:user_id, :date, :created_by, :updated_by)",
            array(
                'user_id'    => $user_id,
                'date'       => $date,
                'created_by' => session_get('user_id'),
                'updated_by' => session_get('user_id'),
            ));
    }

    public static function insert_new_user_certificate_without_expire_date(int $user_id, string $issue_date, string $certificate_url, string $file_path) {
        return Db::getInstance()->insert("INSERT INTO user_certificates 
            (user_id, issue_date, certificate_url, file_path, created_by, updated_by) 
            VALUES (:user_id, :issue_date, :certificate_url, :file_path, :created_by, :updated_by)",
            array(
                'user_id'         => $user_id,
                'issue_date'      => $issue_date,
                'certificate_url' => $certificate_url,
                'file_path'       => $file_path,
                'created_by'      => session_get('user_id'),
                'updated_by'      => session_get('user_id'),
            ));
    }

    public static function insert_new_user_certificate_with_expire_date(int $user_id, string $issue_date, string $expire_date, string $certificate_url, string $file_path) {
        return Db::getInstance()->insert("INSERT INTO user_certificates 
            (user_id, issue_date, expire_date, certificate_url, file_path, created_by, updated_by) 
            VALUES (:user_id, :issue_date, :expire_date, :certificate_url, :file_path, :created_by, :updated_by)",
            array(
                'user_id'         => $user_id,
                'issue_date'      => $issue_date,
                'expire_date'     => $expire_date,
                'certificate_url' => $certificate_url,
                'file_path'       => $file_path,
                'created_by'      => session_get('user_id'),
                'updated_by'      => session_get('user_id'),
            ));
    }

    // ==========================================
    //              USER SOCIAL & MEDIA
    // ==========================================

    public static function insert_new_user_social_link(int $user_id, string $platform, string $url, int $is_verified = 0) {
        return Db::getInstance()->insert("INSERT INTO user_social_links 
            (user_id, platform, url, is_verified) 
            VALUES (:user_id, :platform, :url, :is_verified)",
            array(
                'user_id' => $user_id,
                'platform' => $platform,
                'url' => $url,
                'is_verified' => $is_verified
            ));
    }

    // public static function insert_new_user_media(int $user_id, int $media_file_id, ?string $title = null, ?string $caption = null, string $visibility = 'public') {
    //     return Db::getInstance()->insert("INSERT INTO user_media 
    //         (user_id, media_file_id, title, caption, visibility) 
    //         VALUES (:user_id, :media_file_id, :title, :caption, :visibility)",
    //         array(
    //             'user_id' => $user_id,
    //             'media_file_id' => $media_file_id,
    //             'title' => $title,
    //             'caption' => $caption,
    //             'visibility' => $visibility
    //         ));
    // }

    // ==========================================
    //              USER AUTH & SECURITY
    // ==========================================

    public static function insert_new_user_auth_provider(int $user_id, string $provider, string $provider_user_id, ?string $provider_email = null, ?string $access_token = null) {
        return Db::getInstance()->insert("INSERT INTO user_auth_providers 
            (user_id, provider, provider_user_id, provider_email, access_token) 
            VALUES (:user_id, :provider, :provider_user_id, :provider_email, :access_token)",
            array(
                'user_id' => $user_id,
                'provider' => $provider,
                'provider_user_id' => $provider_user_id,
                'provider_email' => $provider_email,
                'access_token' => $access_token
            ));
    }

    public static function insert_new_user_session(int $user_id, string $token, ?string $device = null, ?string $ip = null) {
        return Db::getInstance()->insert("INSERT INTO user_sessions 
            (user_id, token, device, ip, expires_at) 
            VALUES (:user_id, :token, :device, :ip, DATE_ADD(NOW(), INTERVAL 30 DAY))",
            array(
                'user_id' => $user_id,
                'token' => $token,
                'device' => $device,
                'ip' => $ip
            ));
    }

    // ==========================================
    //              USER RELATIONSHIPS & ACTIVITY
    // ==========================================

    public static function insert_new_user_relationship(int $follower_id, int $following_id, string $type = 'follow') {
        return Db::getInstance()->insert("INSERT INTO user_relationships 
            (follower_id, following_id, type) 
            VALUES (:follower_id, :following_id, :type)",
            array(
                'follower_id' => $follower_id,
                'following_id' => $following_id,
                'type' => $type
            ));
    }

    public static function insert_new_user_favorite(int $user_id, int $item_id, string $item_type) {
        return Db::getInstance()->insert("INSERT INTO user_favorites 
            (user_id, item_id, item_type) 
            VALUES (:user_id, :item_id, :item_type)",
            array(
                'user_id' => $user_id,
                'item_id' => $item_id,
                'item_type' => $item_type
            ));
    }

    // ==========================================
    //              USER SETTINGS & NOTIFICATIONS
    // ==========================================

    public static function insert_new_user_setting(int $user_id, string $key, string $value, string $type = 'string') {
        return Db::getInstance()->insert("INSERT INTO user_settings 
            (user_id, `key`, value, type) 
            VALUES (:user_id, :key, :value, :type)",
            array(
                'user_id' => $user_id,
                'key' => $key,
                'value' => $value,
                'type' => $type
            ));
    }

    public static function insert_new_user_notification(int $user_id, string $type, string $title, ?string $message = null, ?string $entity_type = null, ?int $entity_id = null) {
        return Db::getInstance()->insert("INSERT INTO user_notifications 
            (user_id, type, title, message, entity_type, entity_id) 
            VALUES (:user_id, :type, :title, :message, :entity_type, :entity_id)",
            array(
                'user_id' => $user_id,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'entity_type' => $entity_type,
                'entity_id' => $entity_id
            ));
    }


    // ===================== user_events =====================
    public static function insert_new_user_event(int $user_id, int $address_id, string $event_type, string $event_date) {
        return Db::getInstance()->insert("INSERT INTO user_events 
            (user_id, address_id, event_type, event_date, created_by, updated_by) 
            VALUES (:user_id, :address_id, :event_type, :event_date, :created_by, :updated_by)",
            array(
                'user_id'    => $user_id,
                'address_id' => $address_id,
                'event_type' => $event_type,
                'event_date' => $event_date,
                'created_by' => session_get('user_id'),
                'updated_by' => session_get('user_id'),
            ));
    }

    // ===================== user_lessons =====================
    public static function insert_new_version_user_lesson(int $user_id, int $lesson_id, int $level_id, int $years_of_experience, int $is_primary, string $created_at, int $created_by) {
        return Db::getInstance()->insert("INSERT INTO user_lessons 
            (user_id, lesson_id, level_id, years_of_experience, is_primary, created_at, created_by, updated_by) 
            VALUES (:user_id, :lesson_id, :level_id, :years_of_experience, :is_primary, :created_at, :created_by, :updated_by)",
            array(
                'user_id'             => $user_id,
                'lesson_id'           => $lesson_id,
                'level_id'            => $level_id,
                'years_of_experience' => $years_of_experience,
                'is_primary'          => $is_primary,
                'created_at'          => $created_at,
                'created_by'          => $created_by,
                'updated_by'          => session_get('user_id')
            ));
    }


    public static function insert_new_user_lesson(int $user_id, int $lesson_id, int $level_id, int $years_of_experience, int $is_primary) {
        return Db::getInstance()->insert("INSERT INTO user_lessons 
            (user_id, lesson_id, level_id, years_of_experience, is_primary, created_by, updated_by) 
            VALUES (:user_id, :lesson_id, :level_id, :years_of_experience, :is_primary, :created_by, :updated_by)",
            array(
                'user_id'             => $user_id,
                'lesson_id'           => $lesson_id,
                'level_id'            => $level_id,
                'years_of_experience' => $years_of_experience,
                'is_primary'          => $is_primary,
                'created_by'          => session_get('user_id'),
                'updated_by'          => session_get('user_id')
            ));
    }

    // ===================== user_instruments =====================
    public static function insert_new_version_user_instrument(int $user_id, int $instrument_id, int $level_id, int $years_of_experience, int $is_primary, string $created_at, int $created_by) {
        return Db::getInstance()->insert("INSERT INTO user_instruments 
            (user_id, instrument_id, level_id, years_of_experience, is_primary, created_at, created_by, updated_by) 
            VALUES (:user_id, :instrument_id, :level_id, :years_of_experience, :is_primary, :created_at, :created_by, :updated_by)",
            array(
                'user_id'             => $user_id,
                'instrument_id'       => $instrument_id,
                'level_id'            => $level_id,
                'years_of_experience' => $years_of_experience,
                'is_primary'          => $is_primary,
                'created_at'          => $created_at,
                'created_by'          => $created_by,
                'updated_by'          => session_get('user_id')
            ));
    }

    public static function insert_new_user_instrument(int $user_id, int $instrument_id, int $level_id, int $years_of_experience, int $is_primary) {
        return Db::getInstance()->insert("INSERT INTO user_instruments 
            (user_id, instrument_id, level_id, years_of_experience, is_primary, created_by, updated_by) 
            VALUES (:user_id, :instrument_id, :level_id, :years_of_experience, :is_primary, :created_by, :updated_by)",
            array(
                'user_id'             => $user_id,
                'instrument_id'       => $instrument_id,
                'level_id'            => $level_id,
                'years_of_experience' => $years_of_experience,
                'is_primary'          => $is_primary,
                'created_by'          => session_get('user_id'),
                'updated_by'          => session_get('user_id')
            ));
    }

    // ===================== user_merges =====================
    public static function insert_new_user_merge(int $from_user_id, int $to_user_id, int $merged_by, ?string $reason = null) {
        return Db::getInstance()->insert("INSERT INTO user_merges 
            (from_user_id, to_user_id, merged_by, reason) 
            VALUES (:from_user_id, :to_user_id, :merged_by, :reason)",
            array(
                'from_user_id' => $from_user_id,
                'to_user_id' => $to_user_id,
                'merged_by' => $merged_by,
                'reason' => $reason
            ));
    }

    // ===================== user_messages =====================
    public static function insert_new_user_message(int $conversation_id, int $sender_id, string $type = 'text', ?string $content = null, ?int $reply_to_id = null, ?string $file_path = null) {
        return Db::getInstance()->insert("INSERT INTO user_messages 
            (conversation_id, sender_id, type, content, reply_to_id, file_path) 
            VALUES (:conversation_id, :sender_id, :type, :content, :reply_to_id, :file_path)",
            array(
                'conversation_id' => $conversation_id,
                'sender_id' => $sender_id,
                'type' => $type,
                'content' => $content,
                'reply_to_id' => $reply_to_id,
                'file_path' => $file_path
            ));
    }

    // ===================== user_permissions =====================
    public static function insert_new_user_permission(int $user_id, int $permission_id, ?int $granted_by = null) {
        return Db::getInstance()->insert("INSERT INTO user_permissions 
            (user_id, permission_id, granted_by, granted_at) 
            VALUES (:user_id, :permission_id, :granted_by, NOW())",
            array(
                'user_id' => $user_id,
                'permission_id' => $permission_id,
                'granted_by' => $granted_by
            ));
    }

    // ===================== user_permission_cache =====================
    public static function insert_new_user_permission_cache(int $user_id, string $permission_name, string $source = 'role') {
        return Db::getInstance()->insert("INSERT INTO user_permission_cache 
            (user_id, permission_name, source) 
            VALUES (:user_id, :permission_name, :source)",
            array(
                'user_id' => $user_id,
                'permission_name' => $permission_name,
                'source' => $source
            ));
    }

    // ===================== user_points =====================
    public static function insert_new_user_point(int $user_id, string $type = 'general', int $points, string $action, ?string $reference_type = null, ?int $reference_id = null) {
        return Db::getInstance()->insert("INSERT INTO user_points 
            (user_id, type, points, action, reference_type, reference_id) 
            VALUES (:user_id, :type, :points, :action, :reference_type, :reference_id)",
            array(
                'user_id' => $user_id,
                'type' => $type,
                'points' => $points,
                'action' => $action,
                'reference_type' => $reference_type,
                'reference_id' => $reference_id
            ));
    }

    // ===================== user_polls =====================
    public static function insert_new_user_poll(int $owner_id, string $target_type, int $target_id, string $type, string $status, string $expires_at, int $is_anonymous) {
        return Db::getInstance()->insert("INSERT INTO user_polls 
            (owner_id, target_type, target_id, type, status, expires_at, is_anonymous, created_by, updated_by) 
            VALUES (:owner_id, :target_type, :target_id, :type, :status, :expires_at, :is_anonymous, :created_by, :updated_by)",
            array(
                'owner_id'     => $owner_id,
                'target_type'  => $target_type,
                'target_id'    => $target_id,
                'type'         => $type,
                'status'       => $status,
                'expires_at'   => $expires_at,
                'is_anonymous' => $is_anonymous,
                'created_by'   => session_get('user_id'),
                'updated_by'   => session_get('user_id')
            ));
    }

    // ===================== user_poll_options =====================
    public static function insert_new_user_poll_option(int $poll_id, int $sort_order) {
        return Db::getInstance()->insert("INSERT INTO user_poll_options 
            (poll_id, sort_order, created_by, updated_by) 
            VALUES (:poll_id, :sort_order, :created_by, :updated_by)",
            array(
                'poll_id'    => $poll_id,
                'sort_order' => $sort_order,
                'created_by' => session_get('user_id'),
                'updated_by' => session_get('user_id')
            ));
    }

    // ===================== user_poll_votes =====================
    public static function insert_new_user_poll_vote(int $poll_id, int $option_id, int $user_id) {
        return Db::getInstance()->insert("INSERT INTO user_poll_votes 
            (poll_id, option_id, user_id, created_by, updated_by) 
            VALUES (:poll_id, :option_id, :user_id, :created_by, :updated_by)",
            array(
                'poll_id'    => $poll_id,
                'option_id'  => $option_id,
                'user_id'    => $user_id,
                'created_by' => session_get('user_id'),
                'updated_by' => session_get('user_id')
            ));
    }

    // ===================== user_posts =====================
    public static function insert_new_user_post(int $author_id, string $title, ?string $content = null, ?string $excerpt = null, ?string $type = null, string $status = 'draft', ?string $slug = null, ?int $cover_media_id = null, string $visibility = 'public') {
        return Db::getInstance()->insert("INSERT INTO user_posts 
            (author_id, title, content, excerpt, type, status, slug, cover_media_id, visibility) 
            VALUES (:author_id, :title, :content, :excerpt, :type, :status, :slug, :cover_media_id, :visibility)",
            array(
                'author_id' => $author_id,
                'title' => $title,
                'content' => $content,
                'excerpt' => $excerpt,
                'type' => $type,
                'status' => $status,
                'slug' => $slug,
                'cover_media_id' => $cover_media_id,
                'visibility' => $visibility
            ));
    }

    // ===================== user_publications =====================
    public static function insert_new_user_publication(int $user_id, string $title, ?string $publisher = null, ?string $url = null, ?string $published_date = null, ?string $content = null, int $is_peer_reviewed = 0) {
        return Db::getInstance()->insert("INSERT INTO user_publications 
            (user_id, title, publisher, url, published_date, content, is_peer_reviewed) 
            VALUES (:user_id, :title, :publisher, :url, :published_date, :content, :is_peer_reviewed)",
            array(
                'user_id' => $user_id,
                'title' => $title,
                'publisher' => $publisher,
                'url' => $url,
                'published_date' => $published_date,
                'content' => $content,
                'is_peer_reviewed' => $is_peer_reviewed
            ));
    }

    // ===================== user_ratings =====================
    public static function insert_new_user_rating(int $user_id, int $item_id, string $item_type, int $rating, ?string $review = null, int $is_private = 0, int $is_anonymous = 0) {
        return Db::getInstance()->insert("INSERT INTO user_ratings 
            (user_id, item_id, item_type, rating, review, is_private, is_anonymous) 
            VALUES (:user_id, :item_id, :item_type, :rating, :review, :is_private, :is_anonymous)",
            array(
                'user_id' => $user_id,
                'item_id' => $item_id,
                'item_type' => $item_type,
                'rating' => $rating,
                'review' => $review,
                'is_private' => $is_private,
                'is_anonymous' => $is_anonymous
            ));
    }

    // ===================== user_rating_summaries =====================
    public static function insert_new_user_rating_summary(int $target_id, string $target_type, ?string $avg_rating = null, int $total_votes = 0) {
        return Db::getInstance()->insert("INSERT INTO user_rating_summaries 
            (target_id, target_type, avg_rating, total_votes) 
            VALUES (:target_id, :target_type, :avg_rating, :total_votes)",
            array(
                'target_id' => $target_id,
                'target_type' => $target_type,
                'avg_rating' => $avg_rating,
                'total_votes' => $total_votes
            ));
    }

    // ===================== user_reports =====================
    public static function insert_new_user_report(int $reporter_id, ?int $reported_user_id = null, ?int $item_id = null, ?string $item_type = null, string $reason, ?string $description = null) {
        return Db::getInstance()->insert("INSERT INTO user_reports 
            (reporter_id, reported_user_id, item_id, item_type, reason, description) 
            VALUES (:reporter_id, :reported_user_id, :item_id, :item_type, :reason, :description)",
            array(
                'reporter_id' => $reporter_id,
                'reported_user_id' => $reported_user_id,
                'item_id' => $item_id,
                'item_type' => $item_type,
                'reason' => $reason,
                'description' => $description
            ));
    }

    // ===================== user_reputation =====================
    public static function insert_new_user_reputation(int $user_id) {
        return Db::getInstance()->insert("INSERT INTO user_reputation 
            (user_id, general_score, professional_score, social_score, academy_score, teaching_score, student_score, total_score) 
            VALUES (:user_id, 0, 0, 0, 0, 0, 0, 0)",
            array('user_id' => $user_id));
    }




    // ==========================================
    //              FINANCIAL TABLES
    // ==========================================

    public static function insert_new_financial_system_account(int $user_id, ?int $branch_id = null, string $type, ?int $currency_id = null) {
        return Db::getInstance()->insert("INSERT INTO financial_system_accounts 
            (user_id, branch_id, type, currency_id, balance, status, created_at) 
            VALUES (:user_id, :branch_id, :type, :currency_id, 0.00, 'active', NOW())",
            array(
                'user_id' => $user_id,
                'branch_id' => $branch_id,
                'type' => $type,
                'currency_id' => $currency_id
            ));
    }

    // ... و بقیه جداول

    public static function insert_new_financial_system_payment(int $invoice_id, int $payer_id, string $amount, ?int $currency_id = null, string $method = 'online', string $status = 'pending') {
        return Db::getInstance()->insert("INSERT INTO financial_system_payments 
            (invoice_id, payer_id, amount, currency_id, method, status, created_at) 
            VALUES (:invoice_id, :payer_id, :amount, :currency_id, :method, :status, NOW())",
            array(
                'invoice_id' => $invoice_id,
                'payer_id' => $payer_id,
                'amount' => $amount,
                'currency_id' => $currency_id,
                'method' => $method,
                'status' => $status
            ));
    }

    // ==========================================
    //              OTHER TABLES (Media, Translations, etc.)
    // ==========================================
    public static function insert_new_media_file(
        int $user_id,
        string $disk,
        string $directory,
        string $filename,
        string $extension,
        string $mime_type,
        string $type,
        string $path,
        string $thumbnail_path,
        int $size,
        string $visibility,
        string $original_filename,
        string $fileable_type,
        int $fileable_id,
        int $sort_order,
    ) {
        return Db::getInstance()->insert("INSERT INTO media_files 
            (user_id, disk, directory, filename, extension, mime_type, type, path, thumbnail_path, size, visibility, original_filename, fileable_type, fileable_id, sort_order, created_by, updated_by) 
            VALUES (:user_id, :disk, :directory, :filename, :extension, :mime_type, :type, :path, :thumbnail_path, :size, :visibility, :original_filename, :fileable_type, :fileable_id, :sort_order, :created_by, :updated_by)",
            array(
                'user_id'           => $user_id,
                'disk'              => $disk,
                'directory'         => $directory,
                'filename'          => $filename,
                'extension'         => $extension,
                'mime_type'         => $mime_type,
                'type'              => $type,
                'path'              => $path,
                'thumbnail_path'    => $thumbnail_path,
                'size'              => $size,
                'visibility'        => $visibility,
                'original_filename' => $original_filename,
                'fileable_type'     => $fileable_type,
                'fileable_id'       => $fileable_id,
                'sort_order'        => $sort_order,
                'created_by'        => session_get('user_id'),
                'updated_by'        => session_get('user_id'),
            ));
    }



































}

