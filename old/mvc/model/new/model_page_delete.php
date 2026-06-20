<?php

trait ModelPageDeleteTrait {

// ==================================================================================================================================================================================================================
//              ACADEMY TABLES
// ==========================================

    public static function soft_delete_academy(int $academy_id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE academies 
            SET deleted_by = :deleted_by, 
                deleted_at = :deleted_at, 
                updated_by = :updated_by, 
                updated_at = :updated_at 
            WHERE id = :academy_id",
            array(
                'academy_id' => $academy_id,
                'deleted_by' => $deleted_by,
                'deleted_at' => $deleted_at,
                'updated_by' => $deleted_by,
                'updated_at' => $deleted_at
            ));
    }

    public static function soft_delete_academy_branch(int $branch_id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE academy_branches 
            SET deleted_by = :deleted_by, 
                deleted_at = :deleted_at, 
                updated_by = :updated_by, 
                updated_at = :updated_at 
            WHERE id = :branch_id",
            array(
                'branch_id' => $branch_id,
                'deleted_by' => $deleted_by,
                'deleted_at' => $deleted_at,
                'updated_by' => $deleted_by,
                'updated_at' => $deleted_at
            ));
    }

    public static function soft_delete_academy_branch_phone(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE academy_branch_phones 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_academy_branch_booking(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE academy_branch_bookings 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_academy_branch_classroom(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE academy_branch_classrooms 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_academy_branch_course(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE academy_branch_courses 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_academy_branch_course_term(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE academy_branch_course_terms 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_academy_branch_course_term_session(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE academy_branch_course_term_sessions 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_academy_branch_member(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE academy_branch_members 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_academy_branch_url(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE academy_branch_urls 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_academy_branch_member_contract(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE academy_branch_member_contracts 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_academy_branch_type(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE academy_branch_types 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_academy_branch_booking_enrollment(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE academy_branch_booking_enrollments 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array(
                'id' => $id,
                'deleted_by' => $deleted_by,
                'deleted_at' => $deleted_at,
                'updated_by' => $deleted_by,
                'updated_at' => $deleted_at
            ));
    }

    public static function soft_delete_academy_branch_classroom_asset(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE academy_branch_classroom_assets 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_academy_branch_classroom_type(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE academy_branch_classroom_types 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_academy_branch_course_term_enrollment(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE academy_branch_course_term_enrollments 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_academy_branch_course_term_invoice(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE academy_branch_course_term_invoices 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_academy_branch_course_term_invoice_discount(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE academy_branch_course_term_invoice_discounts 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_academy_branch_course_term_invoice_installment(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE academy_branch_course_term_invoice_installments 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_academy_branch_course_term_session_attendance(int $session_id, int $academy_member_id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE academy_branch_course_term_session_attendance 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE session_id = :session_id AND academy_member_id = :academy_member_id",
            array(
                'session_id' => $session_id,
                'academy_member_id' => $academy_member_id,
                'deleted_by' => $deleted_by,
                'deleted_at' => $deleted_at,
                'updated_by' => $deleted_by,
                'updated_at' => $deleted_at
            ));
    }

    public static function soft_delete_academy_branch_course_term_session_change(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE academy_branch_course_term_session_changes 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_academy_branch_course_term_session_classroom(int $session_id, int $classroom_id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE academy_branch_course_term_session_classrooms 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at 
            WHERE session_id = :session_id AND classroom_id = :classroom_id",
            array(
                'session_id' => $session_id,
                'classroom_id' => $classroom_id,
                'deleted_by' => $deleted_by,
                'deleted_at' => $deleted_at
            ));
    }

    public static function soft_delete_academy_branch_course_term_session_exception(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE academy_branch_course_term_session_exceptions 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_academy_branch_course_term_waiting_list(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE academy_branch_course_term_waiting_list 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_academy_branch_member_permission(int $academy_member_id, int $permission_id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE academy_branch_member_permissions 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at 
            WHERE academy_member_id = :academy_member_id AND permission_id = :permission_id",
            array(
                'academy_member_id' => $academy_member_id,
                'permission_id' => $permission_id,
                'deleted_by' => $deleted_by,
                'deleted_at' => $deleted_at
            ));
    }

    public static function soft_delete_academy_branch_member_schedule(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE academy_branch_member_schedules 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_academy_branch_scheduling_queue(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE academy_branch_scheduling_queue 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_academy_branch_scheduling_rule(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE academy_branch_scheduling_rules 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

// ==================================================================================================================================================================================================================
//              USER TABLES
// ==========================================

    public static function soft_delete_user(int $user_id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE users 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :user_id",
            array('user_id' => $user_id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_user_post(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_posts 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_user_comment(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_comments 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_user_media(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_media 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_user_availability_exception(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_availability_exceptions 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }


    public static function soft_delete_user_address(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_addresses SET deleted_by = :deleted_by, deleted_at = :deleted_at, updated_by = :updated_by, updated_at = :updated_at WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_user_approval(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_approvals SET deleted_by = :deleted_by, deleted_at = :deleted_at, updated_by = :updated_by, updated_at = :updated_at WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_user_audit_log(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_audit_logs 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_user_auth_provider(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_auth_providers 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_user_availability(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_availabilities 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_user_badge(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_badges 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_user_favorite(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_favorites 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }


    public static function soft_delete_user_merge(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_merges 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_user_notification(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_notifications 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_user_permission(int $user_id, int $permission_id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_permissions 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at 
            WHERE user_id = :user_id AND permission_id = :permission_id",
            array('user_id' => $user_id, 'permission_id' => $permission_id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at));
    }

    public static function soft_delete_user_profile(int $user_id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_profiles 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE user_id = :user_id",
            array('user_id' => $user_id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_user_rating(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_ratings 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_user_relationship(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_relationships 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_user_report(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_reports 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_user_reputation(int $user_id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_reputation 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE user_id = :user_id",
            array('user_id' => $user_id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_user_review(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_reviews 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_user_role(int $user_id, int $role_id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_roles 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at 
            WHERE user_id = :user_id AND role_id = :role_id",
            array('user_id' => $user_id, 'role_id' => $role_id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at));
    }

    public static function soft_delete_user_session(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_sessions 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_user_setting(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_settings 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_user_specialty(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_specialties 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_user_verification(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_verifications 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    // ===================== user_contacts =====================
    public static function soft_delete_user_contact(int $contact_id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_contacts 
            SET deleted_by = :deleted_by, 
                deleted_at = :deleted_at, 
                updated_by = :updated_by, 
                updated_at = :updated_at 
            WHERE contact_id = :contact_id",
            array(
                'contact_id' => $contact_id,
                'deleted_by' => $deleted_by,
                'deleted_at' => $deleted_at,
                'updated_by' => $deleted_by,
                'updated_at' => $deleted_at
            ));
    }


    // ===================== user_messages =====================
    public static function soft_delete_user_message(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_messages 
            SET deleted_by = :deleted_by, 
                deleted_at = :deleted_at, 
                updated_by = :updated_by, 
                updated_at = :updated_at 
            WHERE id = :id",
            array(
                'id' => $id,
                'deleted_by' => $deleted_by,
                'deleted_at' => $deleted_at,
                'updated_by' => $deleted_by,
                'updated_at' => $deleted_at
            ));
    }

    // ===================== user_permission_cache =====================
    public static function soft_delete_user_permission_cache(int $user_id, string $permission_name, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_permission_cache 
            SET deleted_by = :deleted_by, 
                deleted_at = :deleted_at, 
                updated_by = :updated_by, 
                updated_at = :updated_at 
            WHERE user_id = :user_id AND permission_name = :permission_name",
            array(
                'user_id' => $user_id,
                'permission_name' => $permission_name,
                'deleted_by' => $deleted_by,
                'deleted_at' => $deleted_at,
                'updated_by' => $deleted_by,
                'updated_at' => $deleted_at
            ));
    }

    // ===================== user_points =====================
    public static function soft_delete_user_point(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_points 
            SET deleted_by = :deleted_by, 
                deleted_at = :deleted_at, 
                updated_by = :updated_by, 
                updated_at = :updated_at 
            WHERE id = :id",
            array(
                'id' => $id,
                'deleted_by' => $deleted_by,
                'deleted_at' => $deleted_at,
                'updated_by' => $deleted_by,
                'updated_at' => $deleted_at
            ));
    }


    // ===================== user_publications =====================
    public static function soft_delete_user_publication(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_publications 
            SET deleted_by = :deleted_by, 
                deleted_at = :deleted_at, 
                updated_by = :updated_by, 
                updated_at = :updated_at 
            WHERE id = :id",
            array(
                'id' => $id,
                'deleted_by' => $deleted_by,
                'deleted_at' => $deleted_at,
                'updated_by' => $deleted_by,
                'updated_at' => $deleted_at
            ));
    }

    // ===================== user_rating_summaries =====================
    public static function soft_delete_user_rating_summary(int $target_id, string $target_type, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_rating_summaries 
            SET deleted_by = :deleted_by, 
                deleted_at = :deleted_at, 
                updated_by = :updated_by, 
                updated_at = :updated_at 
            WHERE target_id = :target_id AND target_type = :target_type",
            array(
                'target_id' => $target_id,
                'target_type' => $target_type,
                'deleted_by' => $deleted_by,
                'deleted_at' => $deleted_at,
                'updated_by' => $deleted_by,
                'updated_at' => $deleted_at
            ));
    }

    // ===================== user_reputation_logs =====================
    public static function soft_delete_user_reputation_log(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_reputation_logs 
            SET deleted_by = :deleted_by, 
                deleted_at = :deleted_at, 
                updated_by = :updated_by, 
                updated_at = :updated_at 
            WHERE id = :id",
            array(
                'id' => $id,
                'deleted_by' => $deleted_by,
                'deleted_at' => $deleted_at,
                'updated_by' => $deleted_by,
                'updated_at' => $deleted_at
            ));
    }

// ==================================================================================================================================================================================================================
//              ACCESS & CONVERSATION
// ==========================================

    public static function soft_delete_access_system_permission(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE access_system_permissions 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id", array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_access_system_role(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE access_system_roles 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id", array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_access_system_role_permission(int $role_id, int $permission_id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE access_system_role_permissions 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at 
            WHERE role_id = :role_id AND permission_id = :permission_id",
            array(
                'role_id' => $role_id,
                'permission_id' => $permission_id,
                'deleted_by' => $deleted_by,
                'deleted_at' => $deleted_at
            ));
    }

// ==================================================================================================================================================================================================================
//              FINANCIAL
// ==========================================

    public static function soft_delete_financial_system_account(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE financial_system_accounts 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id", array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_financial_system_currency(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE financial_system_currency 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_financial_system_discount(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE financial_system_discounts 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id", array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_financial_system_ledger_entry(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE financial_system_ledger_entries 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_financial_system_payment(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE financial_system_payments 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_financial_system_refund(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE financial_system_refunds 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_financial_system_transaction(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE financial_system_transactions 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

// ==================================================================================================================================================================================================================
//              CONVERSATION
// ==========================================

    public static function soft_delete_conversation(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE conversations 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id", array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_conversation_member(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE conversation_members 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

// ==================================================================================================================================================================================================================
//              OTHER TABLES
// ==========================================


    public static function soft_delete_translation(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE translations 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }


    public static function soft_delete_language(string $code, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE languages SET deleted_by = :deleted_by, deleted_at = :deleted_at WHERE code = :code",
            array('code' => $code, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at));
    }


    public static function soft_delete_tag(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE tags SET deleted_by = :deleted_by, deleted_at = :deleted_at, updated_by = :updated_by, updated_at = :updated_at WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }


    public static function soft_delete_otp_code(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE otp_codes 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_password_reset(string $email, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE password_resets 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at 
            WHERE email = :email",
            array('email' => $email, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at));
    }

    public static function soft_delete_setting(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE settings 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_sor_setting(int $setting_id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE sor_settings 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE setting_id = :setting_id",
            array('setting_id' => $setting_id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_system_event(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE system_events 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_taggable(int $tag_id, int $entity_id, string $entity_type, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE taggables 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at 
            WHERE tag_id = :tag_id AND entity_id = :entity_id AND entity_type = :entity_type",
            array(
                'tag_id' => $tag_id,
                'entity_id' => $entity_id,
                'entity_type' => $entity_type,
                'deleted_by' => $deleted_by,
                'deleted_at' => $deleted_at
            ));
    }

    public static function soft_delete_verification_level(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE verification_levels 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }


}

