<?php

trait ModelPageUpdateTrait {


    // ==========================================
    //              ACADEMY TABLES
    // ==========================================

    public static function update_academy(int $academy_id, int $user_id, int $updated_by){
        return Db::getInstance()->modify("UPDATE academies SET user_id = :user_id, updated_by = :updated_by, updated_at = NOW() 
            WHERE academy_id = :academy_id",
            array(
                'academy_id' => $academy_id,
                'user_id' => $user_id,
                'updated_by' => $updated_by
            ));
    }

    public static function update_academy_branch(int $branch_id, int $academy_id, int $user_id, int $is_main, ?string $mode, ?string $timezone, int $updated_by){
        return Db::getInstance()->modify("UPDATE academy_branches 
            SET academy_id = :academy_id, user_id = :user_id, is_main = :is_main, 
                mode = :mode, timezone = :timezone, updated_by = :updated_by, updated_at = NOW() 
            WHERE branch_id = :branch_id",
            array(
                'branch_id' => $branch_id,
                'academy_id' => $academy_id,
                'user_id' => $user_id,
                'is_main' => $is_main,
                'mode' => $mode,
                'timezone' => $timezone,
                'updated_by' => $updated_by
            ));
    }

    public static function update_academy_branch_phone(int $id, int $branch_id, string $type, string $country_code, string $phone, int $updated_by){
        return Db::getInstance()->modify("UPDATE academy_branch_phones 
            SET branch_id = :branch_id, type = :type, country_code = :country_code, 
                phone = :phone, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'branch_id' => $branch_id,
                'type' => $type,
                'country_code' => $country_code,
                'phone' => $phone,
                'updated_by' => $updated_by
            ));
    }

    public static function update_academy_branch_url(int $id, int $branch_id, string $url, string $type, ?string $purpose, int $is_primary, int $updated_by){
        return Db::getInstance()->modify("UPDATE academy_branch_urls 
            SET branch_id = :branch_id, url = :url, type = :type, purpose = :purpose, 
                is_primary = :is_primary, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'branch_id' => $branch_id,
                'url' => $url,
                'type' => $type,
                'purpose' => $purpose,
                'is_primary' => $is_primary,
                'updated_by' => $updated_by
            ));
    }

    public static function update_academy_branch_booking(int $id, int $student_id, int $teacher_id, int $branch_id, string $requested_date, string $start_time, string $end_time, string $status, ?string $source, ?string $note, int $updated_by){
        return Db::getInstance()->modify("UPDATE academy_branch_bookings 
            SET student_id = :student_id, teacher_id = :teacher_id, branch_id = :branch_id, 
                requested_date = :requested_date, start_time = :start_time, end_time = :end_time, 
                status = :status, source = :source, note = :note, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'student_id' => $student_id,
                'teacher_id' => $teacher_id,
                'branch_id' => $branch_id,
                'requested_date' => $requested_date,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'status' => $status,
                'source' => $source,
                'note' => $note,
                'updated_by' => $updated_by
            ));
    }

    public static function update_academy_branch_classroom(int $id, int $branch_id, int $type_id, ?int $capacity, int $is_active, string $status, int $updated_by){
        return Db::getInstance()->modify("UPDATE academy_branch_classrooms 
            SET branch_id = :branch_id, type_id = :type_id, capacity = :capacity, 
                is_active = :is_active, status = :status, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'branch_id' => $branch_id,
                'type_id' => $type_id,
                'capacity' => $capacity,
                'is_active' => $is_active,
                'status' => $status,
                'updated_by' => $updated_by
            ));
    }






    
    // ==========================================
    //              USER TABLES
    // ==========================================

    public static function update_user(int $user_id, ?string $email, ?string $username, ?string $phone, ?string $national_code, ?string $gender, ?string $status, int $updated_by){
        return Db::getInstance()->modify("UPDATE users 
            SET email = :email, username = :username, phone = :phone, 
                national_code = :national_code, gender = :gender, status = :status, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :user_id",
            array(
                'user_id' => $user_id,
                'email' => $email,
                'username' => $username,
                'phone' => $phone,
                'national_code' => $national_code,
                'gender' => $gender,
                'status' => $status,
                'updated_by' => $updated_by
            ));
    }

    public static function update_user_profile(int $user_id, ?int $student_level_id, ?string $start_career_date, ?int $picture_media_id, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_profiles 
            SET student_level_id = :student_level_id, start_career_date = :start_career_date, 
                picture_media_id = :picture_media_id, updated_by = :updated_by, updated_at = NOW() 
            WHERE user_id = :user_id",
            array(
                'user_id' => $user_id,
                'student_level_id' => $student_level_id,
                'start_career_date' => $start_career_date,
                'picture_media_id' => $picture_media_id,
                'updated_by' => $updated_by
            ));
    }

    public static function update_user_post(int $id, string $title, ?string $content, ?string $excerpt, string $status, string $visibility, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_posts 
            SET title = :title, content = :content, excerpt = :excerpt, 
                status = :status, visibility = :visibility, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'title' => $title,
                'content' => $content,
                'excerpt' => $excerpt,
                'status' => $status,
                'visibility' => $visibility,
                'updated_by' => $updated_by
            ));
    }







    // ==========================================
    //              COMMON TABLES
    // ==========================================

    public static function update_tag(int $id, string $name, ?string $slug, int $updated_by){
        return Db::getInstance()->modify("UPDATE tags 
            SET name = :name, slug = :slug, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'name' => $name,
                'slug' => $slug,
                'updated_by' => $updated_by
            ));
    }

    public static function update_setting(int $id, string $key, ?string $group, ?string $type, ?string $value, int $is_active, int $updated_by){
        return Db::getInstance()->modify("UPDATE settings 
            SET `key` = :key, `group` = :group, type = :type, value = :value, 
                is_active = :is_active, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'key' => $key,
                'group' => $group,
                'type' => $type,
                'value' => $value,
                'is_active' => $is_active,
                'updated_by' => $updated_by
            ));
    }

    public static function update_translation(int $id, string $locale, ?string $title, ?string $brief, ?string $description, int $updated_by){
        return Db::getInstance()->modify("UPDATE translations 
            SET locale = :locale, title = :title, brief = :brief, description = :description, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'locale' => $locale,
                'title' => $title,
                'brief' => $brief,
                'description' => $description,
                'updated_by' => $updated_by
            ));
    }

    public static function update_media_file(int $id, ?string $title, ?string $type, int $is_public, int $updated_by){
        return Db::getInstance()->modify("UPDATE media_files 
            SET title = :title, type = :type, is_public = :is_public, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'title' => $title,
                'type' => $type,
                'is_public' => $is_public,
                'updated_by' => $updated_by
            ));
    }

    // ======================================================================================================================
    // ======================================================================================================================
    // ======================================================================================================================
    // ======================================================================================================================
    // ======================================================================================================================

    // ==========================================
    //              ACADEMY BRANCH TABLES
    // ==========================================

    public static function update_academy_branch_booking_enrollment(int $id, int $booking_id, int $student_id, string $status, int $updated_by){
        return Db::getInstance()->modify("UPDATE academy_branch_booking_enrollments 
            SET booking_id = :booking_id, student_id = :student_id, status = :status, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'booking_id' => $booking_id,
                'student_id' => $student_id,
                'status' => $status,
                'updated_by' => $updated_by
            ));
    }

    public static function update_academy_branch_classroom_asset(int $id, int $classroom_id, int $quantity, int $updated_by){
        return Db::getInstance()->modify("UPDATE academy_branch_classroom_assets 
            SET classroom_id = :classroom_id, quantity = :quantity, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'classroom_id' => $classroom_id,
                'quantity' => $quantity,
                'updated_by' => $updated_by
            ));
    }

    public static function update_academy_branch_classroom_type(int $id, string $code, int $updated_by){
        return Db::getInstance()->modify("UPDATE academy_branch_classroom_types 
            SET code = :code, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'code' => $code,
                'updated_by' => $updated_by
            ));
    }

    public static function update_academy_branch_course(int $id, int $branch_id, int $lesson_id, ?int $level_id, ?int $capacity, int $updated_by){
        return Db::getInstance()->modify("UPDATE academy_branch_courses 
            SET branch_id = :branch_id, lesson_id = :lesson_id, level_id = :level_id, 
                capacity = :capacity, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'branch_id' => $branch_id,
                'lesson_id' => $lesson_id,
                'level_id' => $level_id,
                'capacity' => $capacity,
                'updated_by' => $updated_by
            ));
    }

    public static function update_academy_branch_course_term(int $id, int $course_id, int $branch_id, string $start_date, string $end_date, int $session_count, ?string $price, ?int $currency_id, string $status, int $updated_by){
        return Db::getInstance()->modify("UPDATE academy_branch_course_terms 
            SET course_id = :course_id, branch_id = :branch_id, start_date = :start_date, 
                end_date = :end_date, session_count = :session_count, price = :price, 
                currency_id = :currency_id, status = :status, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'course_id' => $course_id,
                'branch_id' => $branch_id,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'session_count' => $session_count,
                'price' => $price,
                'currency_id' => $currency_id,
                'status' => $status,
                'updated_by' => $updated_by
            ));
    }

    public static function update_academy_branch_course_term_enrollment(int $id, int $term_id, int $student_id, string $status, int $updated_by){
        return Db::getInstance()->modify("UPDATE academy_branch_course_term_enrollments 
            SET term_id = :term_id, student_id = :student_id, status = :status, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'term_id' => $term_id,
                'student_id' => $student_id,
                'status' => $status,
                'updated_by' => $updated_by
            ));
    }

    public static function update_academy_branch_course_term_invoice(int $id, int $user_id, int $branch_id, int $term_id, string $total_amount, string $payable_amount, ?int $currency_id, string $status, ?string $due_date, int $updated_by){
        return Db::getInstance()->modify("UPDATE academy_branch_course_term_invoices 
            SET user_id = :user_id, branch_id = :branch_id, term_id = :term_id, 
                total_amount = :total_amount, payable_amount = :payable_amount, 
                currency_id = :currency_id, status = :status, due_date = :due_date, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'user_id' => $user_id,
                'branch_id' => $branch_id,
                'term_id' => $term_id,
                'total_amount' => $total_amount,
                'payable_amount' => $payable_amount,
                'currency_id' => $currency_id,
                'status' => $status,
                'due_date' => $due_date,
                'updated_by' => $updated_by
            ));
    }

    public static function update_academy_branch_course_term_invoice_discount(int $id, int $invoice_id, int $discount_id, ?string $amount, int $updated_by){
        return Db::getInstance()->modify("UPDATE academy_branch_course_term_invoice_discounts 
            SET invoice_id = :invoice_id, discount_id = :discount_id, amount = :amount, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'invoice_id' => $invoice_id,
                'discount_id' => $discount_id,
                'amount' => $amount,
                'updated_by' => $updated_by
            ));
    }

    public static function update_academy_branch_course_term_invoice_installment(int $id, int $invoice_id, int $installment_number, string $amount, string $due_date, string $status, int $updated_by){
        return Db::getInstance()->modify("UPDATE academy_branch_course_term_invoice_installments 
            SET invoice_id = :invoice_id, installment_number = :installment_number, 
                amount = :amount, due_date = :due_date, status = :status, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'invoice_id' => $invoice_id,
                'installment_number' => $installment_number,
                'amount' => $amount,
                'due_date' => $due_date,
                'status' => $status,
                'updated_by' => $updated_by
            ));
    }

    public static function update_academy_branch_course_term_session(int $id, int $term_id, ?int $booking_id, ?int $classroom_id, ?int $branch_url_id, ?int $teacher_id, string $status, ?int $session_number, string $date, string $start_time, string $end_time, int $updated_by){
        return Db::getInstance()->modify("UPDATE academy_branch_course_term_sessions 
            SET term_id = :term_id, booking_id = :booking_id, classroom_id = :classroom_id, 
                branch_url_id = :branch_url_id, teacher_id = :teacher_id, status = :status, 
                session_number = :session_number, date = :date, start_time = :start_time, 
                end_time = :end_time, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'term_id' => $term_id,
                'booking_id' => $booking_id,
                'classroom_id' => $classroom_id,
                'branch_url_id' => $branch_url_id,
                'teacher_id' => $teacher_id,
                'status' => $status,
                'session_number' => $session_number,
                'date' => $date,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'updated_by' => $updated_by
            ));
    }

    public static function update_academy_branch_course_term_session_attendance(int $session_id, int $academy_member_id, string $status, int $updated_by){
        return Db::getInstance()->modify("UPDATE academy_branch_course_term_session_attendance 
            SET status = :status, updated_by = :updated_by, updated_at = NOW() 
            WHERE session_id = :session_id AND academy_member_id = :academy_member_id",
            array(
                'session_id' => $session_id,
                'academy_member_id' => $academy_member_id,
                'status' => $status,
                'updated_by' => $updated_by
            ));
    }

    public static function update_academy_branch_course_term_session_change(int $id, ?int $new_classroom_id, ?int $new_teacher_id, ?string $new_date, ?string $reason, string $status, int $updated_by){
        return Db::getInstance()->modify("UPDATE academy_branch_course_term_session_changes 
            SET new_classroom_id = :new_classroom_id, new_teacher_id = :new_teacher_id, 
                new_date = :new_date, reason = :reason, status = :status, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'new_classroom_id' => $new_classroom_id,
                'new_teacher_id' => $new_teacher_id,
                'new_date' => $new_date,
                'reason' => $reason,
                'status' => $status,
                'updated_by' => $updated_by
            ));
    }

    public static function update_academy_branch_course_term_session_classroom(int $session_id, int $classroom_id, int $updated_by){
        return Db::getInstance()->modify("UPDATE academy_branch_course_term_session_classrooms 
            SET updated_by = :updated_by, updated_at = NOW() 
            WHERE session_id = :session_id AND classroom_id = :classroom_id",
            array(
                'session_id' => $session_id,
                'classroom_id' => $classroom_id,
                'updated_by' => $updated_by
            ));
    }

    public static function update_academy_branch_course_term_session_exception(int $id, string $type, ?string $new_date, ?string $description, int $updated_by){
        return Db::getInstance()->modify("UPDATE academy_branch_course_term_session_exceptions 
            SET type = :type, new_date = :new_date, description = :description, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'type' => $type,
                'new_date' => $new_date,
                'description' => $description,
                'updated_by' => $updated_by
            ));
    }

    public static function update_academy_branch_course_term_waiting_list(int $id, int $term_id, int $user_id, int $updated_by){
        return Db::getInstance()->modify("UPDATE academy_branch_course_term_waiting_list 
            SET term_id = :term_id, user_id = :user_id, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'term_id' => $term_id,
                'user_id' => $user_id,
                'updated_by' => $updated_by
            ));
    }

    public static function update_academy_branch_member(int $id, int $branch_id, int $user_id, ?int $role_id, string $status, int $updated_by){
        return Db::getInstance()->modify("UPDATE academy_branch_members 
            SET branch_id = :branch_id, user_id = :user_id, role_id = :role_id, 
                status = :status, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'branch_id' => $branch_id,
                'user_id' => $user_id,
                'role_id' => $role_id,
                'status' => $status,
                'updated_by' => $updated_by
            ));
    }

    public static function update_academy_branch_member_contract(int $id, int $member_id, int $branch_id, string $type, string $start_date, ?string $end_date, ?string $terms, int $updated_by){
        return Db::getInstance()->modify("UPDATE academy_branch_member_contracts 
            SET member_id = :member_id, branch_id = :branch_id, type = :type, 
                start_date = :start_date, end_date = :end_date, terms = :terms, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'member_id' => $member_id,
                'branch_id' => $branch_id,
                'type' => $type,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'terms' => $terms,
                'updated_by' => $updated_by
            ));
    }

    public static function update_academy_branch_member_permission(int $academy_member_id, int $permission_id, int $updated_by){
        return Db::getInstance()->modify("UPDATE academy_branch_member_permissions 
            SET updated_by = :updated_by, updated_at = NOW() 
            WHERE academy_member_id = :academy_member_id AND permission_id = :permission_id",
            array(
                'academy_member_id' => $academy_member_id,
                'permission_id' => $permission_id,
                'updated_by' => $updated_by
            ));
    }

    public static function update_academy_branch_member_schedule(int $id, int $academy_member_id, int $branch_id, int $day_of_week, string $start_time, string $end_time, string $availability_type, int $updated_by){
        return Db::getInstance()->modify("UPDATE academy_branch_member_schedules 
            SET academy_member_id = :academy_member_id, branch_id = :branch_id, 
                day_of_week = :day_of_week, start_time = :start_time, end_time = :end_time, 
                availability_type = :availability_type, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'academy_member_id' => $academy_member_id,
                'branch_id' => $branch_id,
                'day_of_week' => $day_of_week,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'availability_type' => $availability_type,
                'updated_by' => $updated_by
            ));
    }

    public static function update_academy_branch_scheduling_queue(int $id, int $reference_id, string $status, string $type, int $priority, int $updated_by){
        return Db::getInstance()->modify("UPDATE academy_branch_scheduling_queue 
            SET reference_id = :reference_id, status = :status, type = :type, 
                priority = :priority, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'reference_id' => $reference_id,
                'status' => $status,
                'type' => $type,
                'priority' => $priority,
                'updated_by' => $updated_by
            ));
    }

    public static function update_academy_branch_scheduling_rule(int $id, int $branch_id, ?int $max_sessions_per_day, ?int $min_break_minutes, ?int $session_duration, int $allow_overlap, int $updated_by){
        return Db::getInstance()->modify("UPDATE academy_branch_scheduling_rules 
            SET branch_id = :branch_id, max_sessions_per_day = :max_sessions_per_day, 
                min_break_minutes = :min_break_minutes, session_duration = :session_duration, 
                allow_overlap = :allow_overlap, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'branch_id' => $branch_id,
                'max_sessions_per_day' => $max_sessions_per_day,
                'min_break_minutes' => $min_break_minutes,
                'session_duration' => $session_duration,
                'allow_overlap' => $allow_overlap,
                'updated_by' => $updated_by
            ));
    }

    public static function update_academy_branch_type(int $id, int $updated_by){
        return Db::getInstance()->modify("UPDATE academy_branch_types 
            SET updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array('id' => $id, 'updated_by' => $updated_by));
    }

    // ==========================================
    //              ACCESS & CONVERSATION & FINANCIAL
    // ==========================================

    public static function update_access_system_permission(int $id, string $name, ?string $group_name, int $updated_by){
        return Db::getInstance()->modify("UPDATE access_system_permissions 
            SET name = :name, group_name = :group_name, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array('id' => $id, 'name' => $name, 'group_name' => $group_name, 'updated_by' => $updated_by));
    }

    public static function update_access_system_role(int $id, string $name, ?string $description, ?string $color, int $sort_order, int $updated_by){
        return Db::getInstance()->modify("UPDATE access_system_roles 
            SET name = :name, description = :description, color = :color, 
                sort_order = :sort_order, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'name' => $name,
                'description' => $description,
                'color' => $color,
                'sort_order' => $sort_order,
                'updated_by' => $updated_by
            ));
    }

    public static function update_access_system_role_permission(int $role_id, int $permission_id, int $updated_by){
        return Db::getInstance()->modify("UPDATE access_system_role_permissions 
            SET updated_by = :updated_by, updated_at = NOW() 
            WHERE role_id = :role_id AND permission_id = :permission_id",
            array('role_id' => $role_id, 'permission_id' => $permission_id, 'updated_by' => $updated_by));
    }

    public static function update_conversation(int $id, ?string $title, string $type, int $updated_by){
        return Db::getInstance()->modify("UPDATE conversations 
            SET title = :title, type = :type, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array('id' => $id, 'title' => $title, 'type' => $type, 'updated_by' => $updated_by));
    }

    public static function update_conversation_member(int $id, int $conversation_id, int $user_id, ?string $role, int $is_muted, int $updated_by){
        return Db::getInstance()->modify("UPDATE conversation_members 
            SET conversation_id = :conversation_id, user_id = :user_id, role = :role, 
                is_muted = :is_muted, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'conversation_id' => $conversation_id,
                'user_id' => $user_id,
                'role' => $role,
                'is_muted' => $is_muted,
                'updated_by' => $updated_by
            ));
    }

    public static function update_financial_system_account(int $id, int $user_id, ?int $branch_id, string $type, string $balance, string $status, int $updated_by){
        return Db::getInstance()->modify("UPDATE financial_system_accounts 
            SET user_id = :user_id, branch_id = :branch_id, type = :type, 
                balance = :balance, status = :status, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'user_id' => $user_id,
                'branch_id' => $branch_id,
                'type' => $type,
                'balance' => $balance,
                'status' => $status,
                'updated_by' => $updated_by
            ));
    }

    public static function update_financial_system_currency(int $id, string $brief, ?string $icon_path, int $updated_by){
        return Db::getInstance()->modify("UPDATE financial_system_currency 
            SET brief = :brief, icon_path = :icon_path, updated_by = :updated_by 
            WHERE id = :id",
            array('id' => $id, 'brief' => $brief, 'icon_path' => $icon_path, 'updated_by' => $updated_by));
    }

    public static function update_financial_system_discount(int $id, string $code, string $type, string $value, ?int $max_usage, int $updated_by){
        return Db::getInstance()->modify("UPDATE financial_system_discounts 
            SET code = :code, type = :type, value = :value, max_usage = :max_usage, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'code' => $code,
                'type' => $type,
                'value' => $value,
                'max_usage' => $max_usage,
                'updated_by' => $updated_by
            ));
    }

    public static function update_financial_system_ledger_entry(int $id, int $account_id, ?int $reference_id, ?int $transaction_id, string $type, string $amount, ?string $description, int $updated_by){
        return Db::getInstance()->modify("UPDATE financial_system_ledger_entries 
            SET account_id = :account_id, reference_id = :reference_id, 
                transaction_id = :transaction_id, type = :type, amount = :amount, 
                description = :description, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'account_id' => $account_id,
                'reference_id' => $reference_id,
                'transaction_id' => $transaction_id,
                'type' => $type,
                'amount' => $amount,
                'description' => $description,
                'updated_by' => $updated_by
            ));
    }

    public static function update_financial_system_payment(int $id, int $invoice_id, int $payer_id, string $amount, ?int $currency_id, string $method, string $status, int $updated_by){
        return Db::getInstance()->modify("UPDATE financial_system_payments 
            SET invoice_id = :invoice_id, payer_id = :payer_id, amount = :amount, 
                currency_id = :currency_id, method = :method, status = :status, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'invoice_id' => $invoice_id,
                'payer_id' => $payer_id,
                'amount' => $amount,
                'currency_id' => $currency_id,
                'method' => $method,
                'status' => $status,
                'updated_by' => $updated_by
            ));
    }

    public static function update_financial_system_refund(int $id, int $payment_id, string $amount, ?string $reason, string $status, int $updated_by){
        return Db::getInstance()->modify("UPDATE financial_system_refunds 
            SET payment_id = :payment_id, amount = :amount, reason = :reason, 
                status = :status, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'payment_id' => $payment_id,
                'amount' => $amount,
                'reason' => $reason,
                'status' => $status,
                'updated_by' => $updated_by
            ));
    }

    public static function update_financial_system_transaction(int $id, string $type, int $updated_by){
        return Db::getInstance()->modify("UPDATE financial_system_transactions 
            SET type = :type, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array('id' => $id, 'type' => $type, 'updated_by' => $updated_by));
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



// ==========================================
    //              COMMON & BASE TABLES
    // ==========================================

    public static function update_instrument(int $id, int $updated_by){
        return Db::getInstance()->modify("UPDATE instruments 
            SET updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array('id' => $id, 'updated_by' => $updated_by));
    }

    public static function update_language(string $code, ?string $name, ?string $direction, int $is_active, int $updated_by){
        return Db::getInstance()->modify("UPDATE languages 
            SET name = :name, direction = :direction, is_active = :is_active, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE code = :code",
            array(
                'code' => $code,
                'name' => $name,
                'direction' => $direction,
                'is_active' => $is_active,
                'updated_by' => $updated_by
            ));
    }

    public static function update_lesson(int $lesson_id, string $name_fa, ?string $name_en, ?string $description_fa, ?string $description_en, ?string $reigion, int $updated_by){
        return Db::getInstance()->modify("UPDATE sor_lessons 
            SET name_fa = :name_fa, name_en = :name_en, description_fa = :description_fa, 
                description_en = :description_en, reigion = :reigion, 
                updated_by = :updated_by 
            WHERE lesson_id = :lesson_id",
            array(
                'lesson_id' => $lesson_id,
                'name_fa' => $name_fa,
                'name_en' => $name_en,
                'description_fa' => $description_fa,
                'description_en' => $description_en,
                'reigion' => $reigion,
                'updated_by' => $updated_by
            ));
    }

    public static function update_level(int $id, string $type, ?int $sort_order, int $is_active, int $updated_by){
        return Db::getInstance()->modify("UPDATE levels 
            SET type = :type, sort_order = :sort_order, is_active = :is_active, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'type' => $type,
                'sort_order' => $sort_order,
                'is_active' => $is_active,
                'updated_by' => $updated_by
            ));
    }

    public static function update_otp_code(int $id, string $target, string $type, string $code, string $purpose, string $expires_at, int $updated_by){
        return Db::getInstance()->modify("UPDATE otp_codes 
            SET target = :target, type = :type, code = :code, purpose = :purpose, 
                expires_at = :expires_at, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'target' => $target,
                'type' => $type,
                'code' => $code,
                'purpose' => $purpose,
                'expires_at' => $expires_at,
                'updated_by' => $updated_by
            ));
    }

    public static function update_password_reset(string $email, string $token, string $expires_at, int $updated_by){
        return Db::getInstance()->modify("UPDATE password_resets 
            SET token = :token, expires_at = :expires_at, updated_by = :updated_by 
            WHERE email = :email",
            array(
                'email' => $email,
                'token' => $token,
                'expires_at' => $expires_at,
                'updated_by' => $updated_by
            ));
    }

    public static function update_system_event(int $id, ?int $reference_id, ?string $type, ?string $data, int $updated_by){
        return Db::getInstance()->modify("UPDATE system_events 
            SET reference_id = :reference_id, type = :type, data = :data, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'reference_id' => $reference_id,
                'type' => $type,
                'data' => $data,
                'updated_by' => $updated_by
            ));
    }

    public static function update_taggable(int $tag_id, int $entity_id, string $entity_type, int $updated_by){
        return Db::getInstance()->modify("UPDATE taggables 
            SET updated_by = :updated_by, updated_at = NOW() 
            WHERE tag_id = :tag_id AND entity_id = :entity_id AND entity_type = :entity_type",
            array(
                'tag_id' => $tag_id,
                'entity_id' => $entity_id,
                'entity_type' => $entity_type,
                'updated_by' => $updated_by
            ));
    }

    // ==========================================
    //              USER TABLES
    // ==========================================

    public static function update_user_address(int $id, int $user_id, ?int $country_id, ?int $state_id, ?int $city_id, ?string $address, ?string $postal_code, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_addresses 
            SET user_id = :user_id, country_id = :country_id, state_id = :state_id, 
                city_id = :city_id, address = :address, postal_code = :postal_code, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'user_id' => $user_id,
                'country_id' => $country_id,
                'state_id' => $state_id,
                'city_id' => $city_id,
                'address' => $address,
                'postal_code' => $postal_code,
                'updated_by' => $updated_by
            ));
    }

    public static function update_user_approval(int $id, int $by_user_id, int $entity_id, string $entity_type, string $action, ?string $note, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_approvals 
            SET by_user_id = :by_user_id, entity_id = :entity_id, entity_type = :entity_type, 
                action = :action, note = :note, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'by_user_id' => $by_user_id,
                'entity_id' => $entity_id,
                'entity_type' => $entity_type,
                'action' => $action,
                'note' => $note,
                'updated_by' => $updated_by
            ));
    }

    public static function update_user_audit_log(int $id, string $action, ?string $ip, ?string $user_agent, ?string $old_data, ?string $new_data, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_audit_logs 
            SET action = :action, ip = :ip, user_agent = :user_agent, 
                old_data = :old_data, new_data = :new_data, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'action' => $action,
                'ip' => $ip,
                'user_agent' => $user_agent,
                'old_data' => $old_data,
                'new_data' => $new_data,
                'updated_by' => $updated_by
            ));
    }

    public static function update_user_auth_provider(int $id, string $provider, string $provider_user_id, ?string $access_token, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_auth_providers 
            SET provider = :provider, provider_user_id = :provider_user_id, 
                access_token = :access_token, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'provider' => $provider,
                'provider_user_id' => $provider_user_id,
                'access_token' => $access_token,
                'updated_by' => $updated_by
            ));
    }

    public static function update_user_availability(int $id, int $user_id, int $day_of_week, string $start_time, string $end_time, string $type, ?string $timezone, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_availabilities 
            SET user_id = :user_id, day_of_week = :day_of_week, start_time = :start_time, 
                end_time = :end_time, type = :type, timezone = :timezone, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'user_id' => $user_id,
                'day_of_week' => $day_of_week,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'type' => $type,
                'timezone' => $timezone,
                'updated_by' => $updated_by
            ));
    }

    public static function update_user_availability_exception(int $id, int $user_id, string $date, ?string $start_time, ?string $end_time, string $type, ?string $title, ?string $reason, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_availability_exceptions 
            SET user_id = :user_id, date = :date, start_time = :start_time, 
                end_time = :end_time, type = :type, title = :title, reason = :reason, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'user_id' => $user_id,
                'date' => $date,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'type' => $type,
                'title' => $title,
                'reason' => $reason,
                'updated_by' => $updated_by
            ));
    }

    public static function update_user_award(int $id, int $user_id, string $title, ?string $organization, ?string $description, ?string $date, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_awards 
            SET user_id = :user_id, title = :title, organization = :organization, 
                description = :description, date = :date, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'user_id' => $user_id,
                'title' => $title,
                'organization' => $organization,
                'description' => $description,
                'date' => $date,
                'updated_by' => $updated_by
            ));
    }

    public static function update_user_badge(int $id, int $user_id, int $verification_level_id, string $status, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_badges 
            SET user_id = :user_id, verification_level_id = :verification_level_id, 
                status = :status, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'user_id' => $user_id,
                'verification_level_id' => $verification_level_id,
                'status' => $status,
                'updated_by' => $updated_by
            ));
    }

    public static function update_user_certificate(int $id, int $user_id, string $title, ?string $issuer, ?string $issue_date, ?string $expire_date, ?string $certificate_url, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_certificates 
            SET user_id = :user_id, title = :title, issuer = :issuer, 
                issue_date = :issue_date, expire_date = :expire_date, 
                certificate_url = :certificate_url, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'user_id' => $user_id,
                'title' => $title,
                'issuer' => $issuer,
                'issue_date' => $issue_date,
                'expire_date' => $expire_date,
                'certificate_url' => $certificate_url,
                'updated_by' => $updated_by
            ));
    }

    public static function update_user_comment(int $id, ?int $post_id, ?int $user_id, string $content, string $status, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_comments 
            SET post_id = :post_id, user_id = :user_id, content = :content, 
                status = :status, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'post_id' => $post_id,
                'user_id' => $user_id,
                'content' => $content,
                'status' => $status,
                'updated_by' => $updated_by
            ));
    }

    public static function update_user_contact(int $contact_id, ?int $user_id, ?int $post_id, ?string $content, ?string $approved, int $updated_by){
        return Db::getInstance()->modify("UPDATE sor_contacts 
            SET user_id = :user_id, post_id = :post_id, content = :content, 
                approved = :approved, updated_by = :updated_by 
            WHERE contact_id = :contact_id",
            array(
                'contact_id' => $contact_id,
                'user_id' => $user_id,
                'post_id' => $post_id,
                'content' => $content,
                'approved' => $approved,
                'updated_by' => $updated_by
            ));
    }

    public static function update_user_education(int $id, int $user_id, string $institution, ?string $field_of_study, ?string $degree, ?string $start_date, ?string $end_date, int $is_current, ?string $description, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_educations 
            SET user_id = :user_id, institution = :institution, field_of_study = :field_of_study, 
                degree = :degree, start_date = :start_date, end_date = :end_date, 
                is_current = :is_current, description = :description, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'user_id' => $user_id,
                'institution' => $institution,
                'field_of_study' => $field_of_study,
                'degree' => $degree,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'is_current' => $is_current,
                'description' => $description,
                'updated_by' => $updated_by
            ));
    }

    public static function update_user_event(int $id, int $user_id, string $title, string $event_type, ?string $location, ?string $event_date, ?string $description, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_events 
            SET user_id = :user_id, title = :title, event_type = :event_type, 
                location = :location, event_date = :event_date, description = :description, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'user_id' => $user_id,
                'title' => $title,
                'event_type' => $event_type,
                'location' => $location,
                'event_date' => $event_date,
                'description' => $description,
                'updated_by' => $updated_by
            ));
    }

    public static function update_user_experience(int $id, int $user_id, string $title, ?string $company, ?string $location, ?string $start_date, ?string $end_date, int $is_current, ?string $description, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_experiences 
            SET user_id = :user_id, title = :title, company = :company, location = :location, 
                start_date = :start_date, end_date = :end_date, is_current = :is_current, 
                description = :description, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'user_id' => $user_id,
                'title' => $title,
                'company' => $company,
                'location' => $location,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'is_current' => $is_current,
                'description' => $description,
                'updated_by' => $updated_by
            ));
    }

    public static function update_approval_user_experience_by_id(int $user_experience_id){
        return Db::getInstance()->modify("UPDATE user_experiences 
            SET approved_by = :approved_by, approved_at = NOW() 
            WHERE user_experience_id = :user_experience_id",
            array(
                'approved_by' => session_get('user_id'),
                'user_experience_id' => $user_experience_id,
            ));
    }

    public static function update_approval_user_award_by_id(int $user_award_id){
        return Db::getInstance()->modify("UPDATE user_awards 
            SET approved_by = :approved_by, approved_at = NOW() 
            WHERE user_award_id = :user_award_id",
            array(
                'approved_by' => session_get('user_id'),
                'user_award_id' => $user_award_id,
            ));
    }

    public static function update_approval_user_certificate_by_id(int $user_certificate_id){
        return Db::getInstance()->modify("UPDATE user_certificates 
            SET approved_by = :approved_by, approved_at = NOW() 
            WHERE user_certificate_id = :user_certificate_id",
            array(
                'approved_by' => session_get('user_id'),
                'user_certificate_id' => $user_certificate_id,
            ));
    }

    public static function update_approval_user_education_by_id(int $user_education_id){
        return Db::getInstance()->modify("UPDATE user_educations 
            SET approved_by = :approved_by, approved_at = NOW() 
            WHERE user_education_id = :user_education_id",
            array(
                'approved_by' => session_get('user_id'),
                'user_education_id' => $user_education_id,
            ));
    }
    
    public static function update_approval_user_event_by_id(int $user_event_id){
        return Db::getInstance()->modify("UPDATE user_events 
            SET approved_by = :approved_by, approved_at = NOW() 
            WHERE user_event_id = :user_event_id",
            array(
                'approved_by' => session_get('user_id'),
                'user_event_id' => $user_event_id,
            ));
    }
    
    public static function update_approval_user_instrument_by_id(int $user_instrument_id){
        return Db::getInstance()->modify("UPDATE user_instruments 
            SET approved_by = :approved_by, approved_at = NOW() 
            WHERE user_instrument_id = :user_instrument_id",
            array(
                'approved_by' => session_get('user_id'),
                'user_instrument_id' => $user_instrument_id,
            ));
    }

    public static function update_approval_user_lesson_by_id(int $user_lesson_id){
        return Db::getInstance()->modify("UPDATE user_lessons 
            SET approved_by = :approved_by, approved_at = NOW() 
            WHERE user_lesson_id = :user_lesson_id",
            array(
                'approved_by'    => session_get('user_id'),
                'user_lesson_id' => $user_lesson_id,
            ));
    }

    public static function update_approval_media_file_by_id(int $media_file_id){
        return Db::getInstance()->modify("UPDATE media_files 
            SET approved_by = :approved_by, approved_at = NOW() 
            WHERE media_file_id = :media_file_id",
            array(
                'approved_by'   => session_get('user_id'),
                'media_file_id' => $media_file_id,
            ));
    }

    public static function update_approval_user_poll_by_id(int $user_poll_id){
        return Db::getInstance()->modify("UPDATE user_polls 
            SET status = :status, approved_by = :approved_by, approved_at = NOW() 
            WHERE user_poll_id = :user_poll_id",
            array(
                'status'       => 'active',
                'approved_by'  => session_get('user_id'),
                'user_poll_id' => $user_poll_id,
            ));
    }

    public static function update_status_closed_user_poll_by_id(int $user_poll_id){
        return Db::getInstance()->modify("UPDATE user_polls 
            SET status = :status
            WHERE user_poll_id = :user_poll_id",
            array(
                'status'       => 'closed',
                'user_poll_id' => $user_poll_id,
            ));
    }

    public static function update_status_active_user_poll_by_id(int $user_poll_id){
        return Db::getInstance()->modify("UPDATE user_polls 
            SET status = :status
            WHERE user_poll_id = :user_poll_id",
            array(
                'status'       => 'active',
                'user_poll_id' => $user_poll_id,
            ));
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

// ===================== user_favorites =====================
    public static function update_user_favorite(int $id, int $user_id, int $item_id, string $item_type, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_favorites 
            SET user_id = :user_id, item_id = :item_id, item_type = :item_type, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'user_id' => $user_id,
                'item_id' => $item_id,
                'item_type' => $item_type,
                'updated_by' => $updated_by
            ));
    }

    // ===================== user_instruments =====================
    public static function update_user_instrument(int $user_id, int $instrument_id, ?string $level, ?int $years_of_experience, int $is_primary, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_instruments 
            SET level = :level, years_of_experience = :years_of_experience, 
                is_primary = :is_primary, updated_by = :updated_by, updated_at = NOW() 
            WHERE user_id = :user_id AND instrument_id = :instrument_id",
            array(
                'user_id' => $user_id,
                'instrument_id' => $instrument_id,
                'level' => $level,
                'years_of_experience' => $years_of_experience,
                'is_primary' => $is_primary,
                'updated_by' => $updated_by
            ));
    }

    // ===================== user_lessons =====================
    public static function update_user_lesson(int $id, int $user_id, ?string $lessons_id, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_lessons 
            SET user_id = :user_id, lessons_id = :lessons_id, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'user_id' => $user_id,
                'lessons_id' => $lessons_id,
                'updated_by' => $updated_by
            ));
    }

    // ===================== user_merges =====================
    public static function update_user_merge(int $id, int $from_user_id, int $to_user_id, ?string $reason, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_merges 
            SET from_user_id = :from_user_id, to_user_id = :to_user_id, reason = :reason, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'from_user_id' => $from_user_id,
                'to_user_id' => $to_user_id,
                'reason' => $reason,
                'updated_by' => $updated_by
            ));
    }

    // ===================== user_messages =====================
    public static function update_user_message(int $id, ?string $content, ?string $file_path, string $type, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_messages 
            SET content = :content, file_path = :file_path, type = :type, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'content' => $content,
                'file_path' => $file_path,
                'type' => $type,
                'updated_by' => $updated_by
            ));
    }

    // ===================== user_notifications =====================
    public static function update_user_notification(int $id, string $title, ?string $message, int $is_read, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_notifications 
            SET title = :title, message = :message, is_read = :is_read, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'title' => $title,
                'message' => $message,
                'is_read' => $is_read,
                'updated_by' => $updated_by
            ));
    }

    // ===================== user_permissions =====================
    public static function update_user_permission(int $user_id, int $permission_id, ?string $expires_at, ?string $note, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_permissions 
            SET expires_at = :expires_at, note = :note, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE user_id = :user_id AND permission_id = :permission_id",
            array(
                'user_id' => $user_id,
                'permission_id' => $permission_id,
                'expires_at' => $expires_at,
                'note' => $note,
                'updated_by' => $updated_by
            ));
    }

    // ===================== user_permission_cache =====================
    public static function update_user_permission_cache(int $user_id, string $permission_name, string $source, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_permission_cache 
            SET source = :source, updated_by = :updated_by, updated_at = NOW() 
            WHERE user_id = :user_id AND permission_name = :permission_name",
            array(
                'user_id' => $user_id,
                'permission_name' => $permission_name,
                'source' => $source,
                'updated_by' => $updated_by
            ));
    }

    // ===================== user_points =====================
    public static function update_user_point(int $id, int $points, string $action, ?string $reference_type, ?int $reference_id, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_points 
            SET points = :points, action = :action, reference_type = :reference_type, 
                reference_id = :reference_id, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'points' => $points,
                'action' => $action,
                'reference_type' => $reference_type,
                'reference_id' => $reference_id,
                'updated_by' => $updated_by
            ));
    }

    // ===================== user_polls =====================
    public static function update_user_poll(int $id, string $title, ?string $description, ?string $question, string $status, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_polls 
            SET title = :title, description = :description, question = :question, 
                status = :status, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'title' => $title,
                'description' => $description,
                'question' => $question,
                'status' => $status,
                'updated_by' => $updated_by
            ));
    }

    
    // ===================== user_poll_options =====================
    public static function update_vote_counts_of_user_poll(int $user_poll_id, int $votes_count, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_polls 
            SET votes_count = :votes_count, 
                updated_by = :updated_by
            WHERE user_poll_id = :user_poll_id",
            array(
                'votes_count'  => $votes_count,
                'updated_by'   => $updated_by,
                'user_poll_id' => $user_poll_id,
            ));
    }

    // ===================== user_poll_options =====================
    public static function update_vote_counts_of_user_poll_option(int $user_poll_option_id, int $votes_count, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_poll_options 
            SET votes_count = :votes_count, 
                updated_by = :updated_by
            WHERE user_poll_option_id = :user_poll_option_id",
            array(
                'votes_count' => $votes_count,
                'updated_by' => $updated_by,
                'user_poll_option_id' => $user_poll_option_id,
            ));
    }

    // ===================== user_poll_options =====================
    public static function update_user_poll_option(int $id, string $text, int $sort_order, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_poll_options 
            SET text = :text, sort_order = :sort_order, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'text' => $text,
                'sort_order' => $sort_order,
                'updated_by' => $updated_by
            ));
    }

    // ===================== user_poll_votes =====================
    public static function update_user_poll_vote(int $id, int $option_id, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_poll_votes 
            SET option_id = :option_id, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'option_id' => $option_id,
                'updated_by' => $updated_by
            ));
    }

    // ===================== user_publications =====================
    public static function update_user_publication(int $id, int $user_id, string $title, ?string $publisher, ?string $url, ?string $published_date, ?string $content, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_publications 
            SET user_id = :user_id, title = :title, publisher = :publisher, url = :url, 
                published_date = :published_date, content = :content, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'user_id' => $user_id,
                'title' => $title,
                'publisher' => $publisher,
                'url' => $url,
                'published_date' => $published_date,
                'content' => $content,
                'updated_by' => $updated_by
            ));
    }

    // ===================== user_ratings =====================
    public static function update_user_rating(int $id, int $rating, ?string $review, int $is_private, int $is_anonymous, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_ratings 
            SET rating = :rating, review = :review, is_private = :is_private, 
                is_anonymous = :is_anonymous, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'rating' => $rating,
                'review' => $review,
                'is_private' => $is_private,
                'is_anonymous' => $is_anonymous,
                'updated_by' => $updated_by
            ));
    }

    // ===================== user_rating_summaries =====================
    public static function update_user_rating_summary(int $target_id, string $target_type, string $avg_rating, int $total_votes, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_rating_summaries 
            SET avg_rating = :avg_rating, total_votes = :total_votes, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE target_id = :target_id AND target_type = :target_type",
            array(
                'target_id' => $target_id,
                'target_type' => $target_type,
                'avg_rating' => $avg_rating,
                'total_votes' => $total_votes,
                'updated_by' => $updated_by
            ));
    }

    // ===================== user_relationships =====================
    public static function update_user_relationship(int $id, string $status, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_relationships 
            SET status = :status, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'status' => $status,
                'updated_by' => $updated_by
            ));
    }

    // ===================== user_reports =====================
    public static function update_user_report(int $id, string $status, ?string $description, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_reports 
            SET status = :status, description = :description, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'status' => $status,
                'description' => $description,
                'updated_by' => $updated_by
            ));
    }

    // ===================== user_reputation =====================
    public static function update_user_reputation(int $user_id, int $general_score, int $professional_score, int $social_score, int $academy_score, int $teaching_score, int $student_score, int $total_score, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_reputation 
            SET general_score = :general_score, professional_score = :professional_score, 
                social_score = :social_score, academy_score = :academy_score, 
                teaching_score = :teaching_score, student_score = :student_score, 
                total_score = :total_score, updated_by = :updated_by, updated_at = NOW() 
            WHERE user_id = :user_id",
            array(
                'user_id' => $user_id,
                'general_score' => $general_score,
                'professional_score' => $professional_score,
                'social_score' => $social_score,
                'academy_score' => $academy_score,
                'teaching_score' => $teaching_score,
                'student_score' => $student_score,
                'total_score' => $total_score,
                'updated_by' => $updated_by
            ));
    }

    // ===================== user_reputation_logs =====================
    public static function update_user_reputation_log(int $id, int $score, ?string $description, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_reputation_logs 
            SET score = :score, description = :description, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'score' => $score,
                'description' => $description,
                'updated_by' => $updated_by
            ));
    }

    // ===================== user_reviews =====================
    public static function update_user_review(int $id, int $rating, ?string $comment, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_reviews 
            SET rating = :rating, comment = :comment, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'rating' => $rating,
                'comment' => $comment,
                'updated_by' => $updated_by
            ));
    }

    // ===================== user_roles =====================
    public static function update_user_role(int $user_id, int $role_id, ?string $expires_at, ?string $note, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_roles 
            SET expires_at = :expires_at, note = :note, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE user_id = :user_id AND role_id = :role_id",
            array(
                'user_id' => $user_id,
                'role_id' => $role_id,
                'expires_at' => $expires_at,
                'note' => $note,
                'updated_by' => $updated_by
            ));
    }

    // ===================== user_sessions =====================
    public static function update_user_session(int $id, string $expires_at, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_sessions 
            SET expires_at = :expires_at, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'expires_at' => $expires_at,
                'updated_by' => $updated_by
            ));
    }

    // ===================== user_settings =====================
    public static function update_user_setting(int $id, string $value, string $visibility, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_settings 
            SET value = :value, visibility = :visibility, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'value' => $value,
                'visibility' => $visibility,
                'updated_by' => $updated_by
            ));
    }

    // ===================== user_specialties =====================
    public static function update_user_specialty(int $id, ?int $instrument_id, string $skill_name, string $level, int $years_experience, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_specialties 
            SET instrument_id = :instrument_id, skill_name = :skill_name, 
                level = :level, years_experience = :years_experience, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'instrument_id' => $instrument_id,
                'skill_name' => $skill_name,
                'level' => $level,
                'years_experience' => $years_experience,
                'updated_by' => $updated_by
            ));
    }

    // ===================== user_verifications =====================
    public static function update_user_verification(int $id, string $status, ?string $notes, int $updated_by){
        return Db::getInstance()->modify("UPDATE user_verifications 
            SET status = :status, notes = :notes, 
                updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'status' => $status,
                'notes' => $notes,
                'updated_by' => $updated_by
            ));
    }

    // ===================== verification_levels =====================
    public static function update_verification_level(int $id, string $code, int $priority, ?string $icon, ?string $color, int $is_public, int $updated_by){
        return Db::getInstance()->modify("UPDATE verification_levels 
            SET code = :code, priority = :priority, icon = :icon, color = :color, 
                is_public = :is_public, updated_by = :updated_by, updated_at = NOW() 
            WHERE id = :id",
            array(
                'id' => $id,
                'code' => $code,
                'priority' => $priority,
                'icon' => $icon,
                'color' => $color,
                'is_public' => $is_public,
                'updated_by' => $updated_by
            ));
    }


}        

