<?php

trait AdminDeleteQueriesTrait {

// =================================================================================================================
//              ACADEMY DELETE METHODS
// ==========================================

    public function soft_delete_academy_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_academy($id);
        $this->academies();
        return $soft_delete_status;
    }

    public function soft_delete_academy_branch_by_id(int $branch_id) {
        $soft_delete_status = AdminModel::soft_delete_academy_branch($branch_id);
        $this->academyBranches();
        return $soft_delete_status;
    }

    public function soft_delete_academy_branch_phone_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_academy_branch_phone($id);
        $this->academyBranchPhones();
        return $soft_delete_status;
    }

    public function soft_delete_academy_branch_booking_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_academy_branch_booking($id);
        $this->academyBranchBookings();
        return $soft_delete_status;
    }

    public function soft_delete_academy_branch_classroom_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_academy_branch_classroom($id);
        $this->academyBranchClassrooms();
        return $soft_delete_status;
    }

    public function soft_delete_academy_branch_contract_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_academy_branch_contract($id);
        $this->academyBranchContracts();
        return $soft_delete_status;
    }

    public function soft_delete_academy_branch_course_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_academy_branch_course($id);
        $this->academyBranchCourses();
        return $soft_delete_status;
    }

    public function soft_delete_academy_branch_course_term_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_academy_branch_course_term($id);
        $this->academyBranchCourseTerms();
        return $soft_delete_status;
    }

    public function soft_delete_academy_branch_course_term_enrollment_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_academy_branch_course_term_enrollment($id);
        $this->academyBranchCourseTermEnrollments();
        return $soft_delete_status;
    }

    public function soft_delete_academy_branch_course_term_invoice_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_academy_branch_course_term_invoice($id);
        $this->academyBranchCourseTermInvoices();
        return $soft_delete_status;
    }

    public function soft_delete_academy_branch_course_term_session_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_academy_branch_course_term_session($id);
        $this->academyBranchCourseTermSessions();
        return $soft_delete_status;
    }

    public function soft_delete_academy_branch_member_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_academy_branch_member($id);
        $this->academyBranchMembers();
        return $soft_delete_status;
    }

    public function soft_delete_academy_branch_url_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_academy_branch_url($id);
        $this->academyBranchUrls();
        return $soft_delete_status;
    }

    public function soft_delete_academy_branch_classroom_asset_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_academy_branch_classroom_asset($id);
        $this->academyBranchClassroomAssets();
        return $soft_delete_status;
    }

    public function soft_delete_academy_branch_course_term_teacher_by_id(int $term_id, int $academy_member_id) {
        $soft_delete_status = AdminModel::soft_delete_academy_branch_course_term_teacher($term_id, $academy_member_id);
        $this->academyBranchCourseTermTeachers();
        return $soft_delete_status;
    }

    public function soft_delete_academy_branch_member_permission_by_id(int $academy_member_id, int $permission_id) {
        $soft_delete_status = AdminModel::soft_delete_academy_branch_member_permission($academy_member_id, $permission_id);
        $this->academyBranchMemberPermissions();
        return $soft_delete_status;
    }

    public function soft_delete_academy_branch_booking_enrollment_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_academy_branch_booking_enrollment($id);
        $this->academyBranchBookingEnrollments();
        return $soft_delete_status;
    }

    public function soft_delete_academy_branch_classroom_type_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_academy_branch_classroom_type($id);
        $this->academyBranchClassroomTypes();
        return $soft_delete_status;
    }

    public function soft_delete_academy_branch_course_term_invoice_discount_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_academy_branch_course_term_invoice_discount($id);
        $this->academyBranchCourseTermInvoiceDiscounts();
        return $soft_delete_status;
    }

    public function soft_delete_academy_branch_course_term_invoice_installment_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_academy_branch_course_term_invoice_installment($id);
        $this->academyBranchCourseTermInvoiceInstallments();
        return $soft_delete_status;
    }

    public function soft_delete_academy_branch_course_term_session_attendance_by_id(int $session_id, int $academy_member_id) {
        $soft_delete_status = AdminModel::soft_delete_academy_branch_course_term_session_attendance($session_id, $academy_member_id);
        $this->academyBranchCourseTermSessionAttendances();
        return $soft_delete_status;
    }

    public function soft_delete_academy_branch_course_term_session_change_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_academy_branch_course_term_session_change($id);
        $this->academyBranchCourseTermSessionChanges();
        return $soft_delete_status;
    }

    public function soft_delete_academy_branch_course_term_session_classroom_by_id(int $session_id, int $classroom_id) {
        $soft_delete_status = AdminModel::soft_delete_academy_branch_course_term_session_classroom($session_id, $classroom_id);
        $this->academyBranchCourseTermSessionClassrooms();
        return $soft_delete_status;
    }

    public function soft_delete_academy_branch_course_term_session_exception_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_academy_branch_course_term_session_exception($id);
        $this->academyBranchCourseTermSessionExceptions();
        return $soft_delete_status;
    }

    public function soft_delete_academy_branch_course_term_waiting_list_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_academy_branch_course_term_waiting_list($id);
        $this->academyBranchCourseTermWaitingList();
        return $soft_delete_status;
    }

    public function soft_delete_academy_branch_member_schedule_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_academy_branch_member_schedule($id);
        $this->academyBranchMemberSchedules();
        return $soft_delete_status;
    }

    public function soft_delete_academy_branch_scheduling_queue_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_academy_branch_scheduling_queue($id);
        $this->academyBranchSchedulingQueues();
        return $soft_delete_status;
    }

    public function soft_delete_academy_branch_scheduling_rule_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_academy_branch_scheduling_rule($id);
        $this->academyBranchSchedulingRules();
        return $soft_delete_status;
    }

    public function soft_delete_academy_branch_type_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_academy_branch_type($id);
        $this->academyBranchTypes();
        return $soft_delete_status;
    }





// =================================================================================================================
//              ACCESS SYSTEM
// ==========================================

    public function soft_delete_access_system_permission_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_access_system_permission($id);
        $this->accessSystemPermissions();
        return $soft_delete_status;
    }

    public function soft_delete_access_system_role_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_access_system_role($id);
        $this->accessSystemRoles();
        return $soft_delete_status;
    }

    public function soft_delete_access_system_role_permission_by_id(int $role_id, int $permission_id) {
        $soft_delete_status = AdminModel::soft_delete_access_system_role_permission($role_id, $permission_id);
        $this->accessSystemRolePermissions();
        return $soft_delete_status;
    }





// =================================================================================================================
//              USER DELETE METHODS
// ==========================================

    public function soft_delete_user_by_id(int $user_id) {
        $soft_delete_status = AdminModel::soft_delete_user($user_id);
        $this->users();
        return $soft_delete_status;
    }

    public function soft_delete_user_post_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_user_post($id);
        $this->userPosts();
        return $soft_delete_status;
    }

    public function soft_delete_user_comment_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_user_comment($id);
        $this->userComments();
        return $soft_delete_status;
    }

    public function soft_delete_user_rating_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_user_rating($id);
        $this->userRatings();
        return $soft_delete_status;
    }

    public function soft_delete_user_report_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_user_report($id);
        $this->userReports();
        return $soft_delete_status;
    }

    public function soft_delete_user_address_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_user_address($id);
        $this->userAddresses();
        return $soft_delete_status;
    }

    public function soft_delete_user_approval_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_user_approval($id);
        $this->userApprovals();
        return $soft_delete_status;
    }

    public function soft_delete_user_audit_log_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_user_audit_log($id);
        $this->userAuditLogs();
        return $soft_delete_status;
    }

    public function soft_delete_user_auth_provider_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_user_auth_provider($id);
        $this->userAuthProviders();
        return $soft_delete_status;
    }

    public function soft_delete_user_availability_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_user_availability($id);
        $this->userAvailabilities();
        return $soft_delete_status;
    }

    public function soft_delete_user_availability_exception_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_user_availability_exception($id);
        $this->userAvailabilityExceptions();
        return $soft_delete_status;
    }

    public function soft_delete_user_award_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_user_award($id);
        $this->userAwards();
        return $soft_delete_status;
    }

    public function soft_delete_user_badge_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_user_badge($id);
        $this->userBadges();
        return $soft_delete_status;
    }

    public function soft_delete_user_certificate_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_user_certificate($id);
        $this->userCertificates();
        return $soft_delete_status;
    }

    public function soft_delete_user_contact_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_user_contact($id);
        $this->userContacts();
        return $soft_delete_status;
    }

    public function soft_delete_user_education_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_user_education($id);
        $this->userEducations();
        return $soft_delete_status;
    }

    public function soft_delete_user_event_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_user_event($id);
        $this->userEvents();
        return $soft_delete_status;
    }

    public function soft_delete_user_experience_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_user_experience($id);
        $this->userExperiences();
        return $soft_delete_status;
    }

    public function soft_delete_user_favorite_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_user_favorite($id);
        $this->userFavorites();
        return $soft_delete_status;
    }

    public function soft_delete_user_lesson_by_id(int $user_lesson_id) {
        $soft_delete_status = AdminModel::soft_delete_user_lesson($user_lesson_id);
        // $this->userLessons();
        return $soft_delete_status;
    }

    public function soft_delete_user_instrument_by_id(int $user_instrument_id) {
        $soft_delete_status = AdminModel::soft_delete_user_instrument($user_instrument_id);
        // $this->userInstruments();
        return $soft_delete_status;
    }

    public function soft_delete_user_media_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_user_media($id);
        $this->userMedia();
        return $soft_delete_status;
    }

    public function soft_delete_user_merge_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_user_merge($id);
        $this->userMerges();
        return $soft_delete_status;
    }

    public function soft_delete_user_message_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_user_message($id);
        $this->userMessages();
        return $soft_delete_status;
    }

    public function soft_delete_user_notification_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_user_notification($id);
        $this->userNotifications();
        return $soft_delete_status;
    }

    public function soft_delete_user_permission_by_id(int $user_id, int $permission_id) {
        $soft_delete_status = AdminModel::soft_delete_user_permission($user_id, $permission_id);
        $this->userPermissions();
        return $soft_delete_status;
    }

    public function soft_delete_user_permission_cache_by_id(int $user_id, int $permission_id) {
        $soft_delete_status = AdminModel::soft_delete_user_permission_cache($user_id, $permission_id);
        $this->userPermissions();
        return $soft_delete_status;
    }

    public function soft_delete_user_point_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_user_point($id);
        $this->userPoints();
        return $soft_delete_status;
    }

    public function soft_delete_user_poll_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_user_poll($id);
        $this->userPolls();
        return $soft_delete_status;
    }

    public function soft_delete_user_poll_option_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_user_poll_option($id);
        $this->userPollOptions();
        return $soft_delete_status;
    }

    public function soft_delete_user_poll_vote_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_user_poll_vote($id);
        $this->userPollVotes();
        return $soft_delete_status;
    }

    public function soft_delete_user_profile_by_id(int $user_id) {
        $soft_delete_status = AdminModel::soft_delete_user_profile($user_id);
        $this->userProfiles();
        return $soft_delete_status;
    }

    public function soft_delete_user_publication_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_user_publication($id);
        $this->userPublications();
        return $soft_delete_status;
    }

    public function soft_delete_user_rating_summary_by_id(int $target_id, string $target_type) {
        $soft_delete_status = AdminModel::soft_delete_user_rating_summary($target_id, $target_type);
        $this->userRatingSummaries();
        return $soft_delete_status;
    }

    public function soft_delete_user_relationship_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_user_relationship($id);
        $this->userRelationships();
        return $soft_delete_status;
    }

    public function soft_delete_user_reputation_by_id(int $user_id) {
        $soft_delete_status = AdminModel::soft_delete_user_reputation($user_id);
        $this->userReputation();
        return $soft_delete_status;
    }

    public function soft_delete_user_reputation_log_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_user_reputation_log($id);
        $this->userReputationLogs();
        return $soft_delete_status;
    }

    public function soft_delete_user_review_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_user_review($id);
        $this->userReviews();
        return $soft_delete_status;
    }

    public function soft_delete_user_role_by_id(int $user_id, int $role_id) {
        $soft_delete_status = AdminModel::soft_delete_user_role($user_id, $role_id);
        $this->userRoles();
        return $soft_delete_status;
    }

    public function soft_delete_user_session_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_user_session($id);
        $this->userSessions();
        return $soft_delete_status;
    }

    public function soft_delete_user_setting_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_user_setting($id);
        $this->userSettings();
        return $soft_delete_status;
    }

    public function soft_delete_user_specialty_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_user_specialty($id);
        $this->userSpecialties();
        return $soft_delete_status;
    }

    public function soft_delete_user_verification_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_user_verification($id);
        $this->userVerifications();
        return $soft_delete_status;
    }



// =================================================================================================================
//              CONVERSATION
// ==========================================

    public function soft_delete_conversation_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_conversation($id);
        $this->conversations();
        return $soft_delete_status;
    }

    public function soft_delete_conversation_member_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_conversation_member($id);
        $this->conversationMembers();
        return $soft_delete_status;
    }



// =================================================================================================================
//              FINANCIAL SYSTEM
// ==========================================

    public function soft_delete_financial_system_account_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_financial_system_account($id);
        $this->financialSystemAccounts();
        return $soft_delete_status;
    }

    public function soft_delete_financial_system_currency_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_financial_system_currency($id);
        $this->financialSystemCurrency();
        return $soft_delete_status;
    }

    public function soft_delete_financial_system_discount_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_financial_system_discount($id);
        // $this->financialSystemDiscounts();   // اگر متد لیست دارید
        return $soft_delete_status;
    }

    public function soft_delete_financial_system_ledger_entry_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_financial_system_ledger_entry($id);
        $this->financialSystemLedgerEntries();
        return $soft_delete_status;
    }

    public function soft_delete_financial_system_payment_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_financial_system_payment($id);
        $this->financialSystemPayments();
        return $soft_delete_status;
    }

    public function soft_delete_financial_system_refund_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_financial_system_refund($id);
        $this->financialSystemRefunds();
        return $soft_delete_status;
    }

    public function soft_delete_financial_system_transaction_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_financial_system_transaction($id);
        $this->financialSystemTransactions();
        return $soft_delete_status;
    }



// =================================================================================================================
//              OTHER TABLES
// ==========================================

    public function soft_delete_tag_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_tag($id);
        $this->tags();
        return $soft_delete_status;
    }

    public function soft_delete_setting_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_setting($id);
        $this->settings();
        return $soft_delete_status;
    }

    public function soft_delete_verification_level_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_verification_level($id);
        $this->verificationLevels();
        return $soft_delete_status;
    }

    public function soft_delete_instrument_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_instrument($id);
        // $this->instruments();
        return $soft_delete_status;
    }

    public function soft_delete_language_by_id(string $code) {
        $soft_delete_status = AdminModel::soft_delete_language($code);
        $this->languages();
        return $soft_delete_status;
    }

    public function soft_delete_lesson_by_id(int $lesson_id) {
        $soft_delete_status = AdminModel::soft_delete_lesson($lesson_id);
        // $this->lessons();
        return $soft_delete_status;
    }

    public function soft_delete_level_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_level($id);
        $this->levels();
        return $soft_delete_status;
    }

    public function soft_delete_media_file_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_media_file($id);
        $this->mediaFiles();
        return $soft_delete_status;
    }

    public function soft_delete_otp_code_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_otp_code($id);
        $this->otpCodes();
        return $soft_delete_status;
    }

    public function soft_delete_password_reset_by_email(string $email) {
        $soft_delete_status = AdminModel::soft_delete_password_reset($email);
        // $this->passwordResets();   // اگر لیست دارید
        return $soft_delete_status;
    }

    public function soft_delete_sor_setting_by_id(int $setting_id) {
        $soft_delete_status = AdminModel::soft_delete_sor_setting($setting_id);
        $this->sorSettings();
        return $soft_delete_status;
    }

    public function soft_delete_system_event_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_system_event($id);
        $this->systemEvents();
        return $soft_delete_status;
    }

    public function soft_delete_taggable_by_id(int $tag_id, int $entity_id, string $entity_type) {
        $soft_delete_status = AdminModel::soft_delete_taggable($tag_id, $entity_id, $entity_type);
        $this->taggables();
        return $soft_delete_status;
    }

    public function soft_delete_translation_by_id(int $id) {
        $soft_delete_status = AdminModel::soft_delete_translation($id);
        $this->translations();
        return $soft_delete_status;
    }



    }