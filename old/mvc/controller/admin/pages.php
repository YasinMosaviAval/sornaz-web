<?php

trait AdminPagesTrait {

    public function home(): void {
        $model = new PageModel();
        $data['home'] = setIndexforDataArray($model->getHomeData(), 'variable_name');
        $this->view('/page/home', "Home", $data);
    }

    
    public function backup() {
        $data['academy'] = $this->get_academy();
        $data['contact'] = $this->get_contact();
        $data['user'] = $this->get_user();
        $data['comments'] = $this->get_comments();
        $data['posts'] = $this->get_posts();
        $data['categories'] = $this->get_categories();
        $this->view("/admin/backup", "Backup", $data);
    }


    public function addRole() {
        $data['roles'] = $this->get_all_role();
        $this->view("/admin/academy/roles", "Roles", $data);
    }


    public function addPermission() {
        $data['permissions'] = $this->get_all_permission();
        $this->view("/admin/academy/permissions", "Permissions", $data);
    }


    public function addSettingPermission(string $permission_group = 'all') {
        $data['permission-group'] = $permission_group;
        $data['permissions'] = $this->get_all_permission();
        $data['setting-permissions'] = $this->get_all_access_system_setting_permissions();
        $this->view("/admin/academy/setting-permissions", "Setting Permissions", $data);
    }


    public function addRolePermission(string $permission_group = 'all') {
        $data['roles'] = $this->get_all_role();
        $data['permission-group'] = $permission_group;
        $data['permissions'] = $this->get_all_permission();
        $data['role-permissions'] = $this->get_all_access_system_role_permissions();
        $this->view("/admin/academy/role-permissions", "Role Permissions", $data);
    }


    public function adminPanel() {$data['contact_messages'] = $this->get_no_response_contact_messages(1); $this->view("/admin/index", "Admin Panel", $data);}
    public function messages() {$data['contact_messages'] = $this->get_all_contact_messages(1); $this->view("/admin/messages", "Messages", $data);}
    public function comments() {$data['comments'] = $this->get_comments_ordered_by_id_desc(); $this->view("/admin/comments", "Comments", $data);}
    public function categories() {$data['categories'] = $this->get_article_categories(); $this->view("/admin/categories", "Categories", $data);}


    public function editArticle(int $id) {
        $data['categories'] = $this->get_article_categories();
        $data['tags'] = $this->get_article_tags();
        $data['article_item'] = $this->get_posts_with_id($id);
        $this->view("/admin/edit-article", "Edit Article", $data);
    }

    public function editUser(int $user_id) {
        $data['lessons'] = $this->get_lessons();
        $data['academies'] = $this->get_academies();
        $data['user'] = $this->get_users_by_id($user_id);
        $this->view("/admin/edit-user", "Edit User", $data);
    }


    public function showArticleList(string $type_filter, string $sort_filter) {
        $data['type-filter'] = $type_filter;
        $data['sort-filter'] = $sort_filter . ' DESC';
        $sort_filter = $sort_filter . ' DESC';

        // $data['users'] = $this->get_all_users();
        
        $data['users'] = $this->get_user();

        $data['my-articles'] = $this->get_my_posts($sort_filter);
        $data['all-articles'] = $this->get_all_posts($sort_filter);
        $data['articles-post-trash'] = $this->get_filtered_posts('post', 'trash', $sort_filter);
        $data['articles-post-draft'] = $this->get_filtered_posts('post', 'draft', $sort_filter);
        $data['articles-post-pending'] = $this->get_filtered_posts('post', 'pending', $sort_filter);
        $data['articles-post-private'] = $this->get_filtered_posts('post', 'private', $sort_filter);
        $data['articles-post-published'] = $this->get_filtered_posts('post', 'published', $sort_filter);
        $data['articles-music_theory-trash'] = $this->get_filtered_posts('music_theory', 'trash', $sort_filter);
        $data['articles-music_theory-draft'] = $this->get_filtered_posts('music_theory', 'draft', $sort_filter);
        $data['articles-music_theory-pending'] = $this->get_filtered_posts('music_theory', 'pending', $sort_filter);
        $data['articles-music_theory-private'] = $this->get_filtered_posts('music_theory', 'private', $sort_filter);
        $data['articles-music_theory-published'] = $this->get_filtered_posts('music_theory', 'published', $sort_filter);

        $this->view("/admin/show-article-list", "Show Article List", $data);
    }

    // public function dashboard() {$this->view("/admin/dashboard", "Dashboard");}
    public function settings() {$this->view("/admin/settings", "Settings");}
    public function forms() {$this->view("/admin/forms", "Forms");}
    public function pages() {$this->view("/admin/pages", "Pages");}
    public function statistics() {$this->view("/admin/statistics", "Statistics");}

    public function editProduct($id) {$this->view("/admin/edit-product", "Edit Product");}
    public function addProduct() {$this->view("/admin/add-product", "Add Product");}
    public function showProductList() {$this->view("/admin/show-product-list", "Show Product List");}

    public function editPicture($id) {$this->view("/admin/edit-picture", "Edit Picture");}
    public function addPicture() {$this->view("/admin/add-picture", "Add Picture");}

    public function editVideo($id) {$this->view("/admin/edit-video", "Edit Video");}
    public function addVideo() {$this->view("/admin/add-video", "Add Video");}

    public function addArticle() {$this->view("/admin/add-article", "Add Article");}



    /*
        /admin/userManagement/managers
        /admin/userManagement/receptionists
        /admin/userManagement/teachers
        /admin/userManagement/students
    */

    public function userManagement(string $filter) {
        if(is_array(session_get('branches_id'))) {
            foreach(session_get('branches_id') as $branch) {
                $data['branches_members'][$branch['branch_id']] = $this->get_all_members_by_branch_id($branch['branch_id'], $filter);
            }
        } else {
            $data['branches_members'][session_get('branches_id')] = $this->get_all_members_by_branch_id(session_get('branches_id'), $filter);
        }
        // $data['users'] = $this->get_users();
        $data['roles']    = $this->get_academy_roles();
        $data['branches'] = $this->get_branches_of_academy_by_user_id();
        $this->view("/admin/users/user-management", 'Members', $data);
    }


    public function academyUserSchedulings() {
        foreach(session_get('branches_id') as $branch) {
            $data['branches_members'][$branch['branch_id']] = $this->get_all_members_by_branch_id($branch['branch_id']);
        }
        $data['user_availabilities'] = $this->get_all_user_availabilities();
        // $data['branches'] = $this->get_branches_of_academy_by_user_id();
        $this->view("/admin/academy/user-schedulings", "User Schedulings", $data);
    }


    public function academyBranchPhones() {
        foreach(session_get('branches_id') as $branch) {
            $data['branches_phones'][$branch['branch_id']] = $this->get_all_phones_by_branch_id($branch['branch_id']);
        }        
        $data['branches'] = $this->get_branches_of_academy_by_user_id();
        $this->view("/admin/academy/branch-phones", "Phones", $data);
    }

    public function academyBranchUrls() {
        foreach(session_get('branches_id') as $branch) {
            $data['branches_urls'][$branch['branch_id']] = $this->get_all_urls_by_branch_id($branch['branch_id']);
        }
        $data['branches'] = $this->get_branches_of_academy_by_user_id();
        $this->view("/admin/academy/branch-urls", "Urls", $data);
    }

    public function academyAddresses() {
        foreach(session_get('branches_id') as $branch) {
            $data['branches_addresses'][$branch['branch_id']] = $this->get_all_addresses_by_branch_id($branch['branch_id']);
        }
        $data['iran_cities'] = $this->get_iran_cities();
        $data['iran_provinces'] = $this->get_iran_provinces();
        $data['branches'] = $this->get_branches_of_academy_by_user_id();
        $this->view("/admin/academy/branch-addresses", "Addresses", $data);
    }


    public function academyBranches() {
        $data['branches'] = $this->get_branches_of_academy_by_user_id();
        $data['branch_types'] = $this->get_all_academy_branch_types();
        $this->view("/admin/academy/branches", "Branches", $data);
    }

    public function academyTypes() {
        $data['branch_types'] = $this->get_all_academy_branch_types();
        $this->view("/admin/academy/branch-types", "Branch Types", $data);
    }

    public function academyEnrollStudents() {
        foreach(session_get('branches_id') as $branch) {
            $data['branches_members'][$branch['branch_id']] = $this->get_all_members_by_branch_id($branch['branch_id'], 'students');
        }
        foreach(session_get('branches_id') as $branch) {
            $data['branches_course_terms'][$branch['branch_id']] = $this->get_branches_course_terms_by_branch_id($branch['branch_id']);
        }
        foreach($data['branches_course_terms'] as $course_terms) {
            foreach($course_terms as $term) {
                $data['terms_enrollments'][$term['term_id']] = $this->get_terms_enrollments_students_by_term_id($term['term_id']);
            }
        }
        // $data['discounts'] = $this->get_all_discounts();
        $data['currencies'] = $this->get_all_currency();
        $data['branches']  = $this->get_branches_of_academy();
        $this->view("/admin/academy/enroll-students", "Enroll Students", $data);
    }


    public function academyEnrollTeachers() {
        foreach(session_get('branches_id') as $branch) {
            $data['branches_members'][$branch['branch_id']] = $this->get_all_members_by_branch_id($branch['branch_id'], 'managers&receptionists&teachers');
        }
        foreach(session_get('branches_id') as $branch) {
            $data['branches_course_terms'][$branch['branch_id']] = $this->get_branches_course_terms_by_branch_id($branch['branch_id']);
        }
        foreach($data['branches_course_terms'] as $course_terms) {
            foreach($course_terms as $term) {
                // $data['terms_enrollments'][$term['term_id']] = $this->get_terms_enrollments_by_term_id($term['term_id']);
                $data['terms_enrollments'][$term['term_id']] = $this->get_terms_enrollments_teachers_by_term_id($term['term_id']);
            }
        }
        // $data['discounts'] = $this->get_all_discounts();
        $data['currencies'] = $this->get_all_currency();
        $data['branches']  = $this->get_branches_of_academy();
        $this->view("/admin/academy/enroll-teachers", "Enroll Teachers", $data);
    }

    public function academyWaitingList() {
        foreach(session_get('branches_id') as $branch) {
            $data['branches_members'][$branch['branch_id']] = $this->get_all_members_by_branch_id($branch['branch_id']);
        }
        foreach(session_get('branches_id') as $branch) {
            $data['branches_course_terms'][$branch['branch_id']] = $this->get_branches_course_terms_by_branch_id($branch['branch_id']);
        }
        foreach($data['branches_course_terms'] as $course_terms) {
            foreach($course_terms as $term) {
                $data['terms_waiting_list'][$term['term_id']] = $this->get_terms_waiting_list_by_term_id($term['term_id']);
            }
        }
        $data['branches'] = $this->get_branches_of_academy();
        // dump($data['branches_course_terms']);
        $this->view("/admin/academy/waiting-list", "Waiting List", $data);
    }


    public function userAvailabilityExceptions() {
        foreach(session_get('branches_id') as $branch) {
            $data['branches_members'][$branch['branch_id']] = $this->get_all_members_by_branch_id($branch['branch_id']);
        }
        // $data['users_availability_exceptions'] = $this->get_users_availability_exceptions();
        $data['users_exceptions'] = $this->get_users_exceptions();
        foreach(session_get('branches_id') as $branch) {
            // $data['branches_users_data'][$branch['branch_id']] = $this->get_branches_users_data();
            // $data['branches_users_data'][$branch['branch_id']] = $this->get_branches_users_data_by_branch_id(24);
            $data['branches_users_data'][$branch['branch_id']] = $this->get_branches_users_data_by_branch_id($branch['branch_id']);
        }
        $data['branches'] = $this->get_branches_of_academy_by_user_id();
        $this->view("/admin/academy/user-availability-exceptions", "Availability Exceptions", $data);
    }

    public function academySessions() {
        foreach(session_get('branches_id') as $branch) {
            $data['branches_term_sessions'][$branch['branch_id']] = $this->get_branches_terms_sessions_by_branch_id($branch['branch_id']);
        }
        foreach(session_get('branches_id') as $branch) {
            $data['branches_course_terms'][$branch['branch_id']] = $this->get_branches_course_terms_by_branch_id($branch['branch_id']);
        }
        foreach(session_get('branches_id') as $branch) {
            $data['branches_urls'][$branch['branch_id']] = $this->get_all_urls_by_branch_id($branch['branch_id']);
        }
        foreach(session_get('branches_id') as $branch) {
            $data['branches_classrooms'][$branch['branch_id']] = $this->get_classrooms_by_branch_id($branch['branch_id']);
        }
        foreach(session_get('branches_id') as $branch) {
            $data['branches_courses'][$branch['branch_id']] = $this->get_all_courses_by_branch_id($branch['branch_id']);
        }
        $data['currencies'] = $this->get_all_currency();
        $data['branches'] = $this->get_branches_of_academy_by_user_id();
        $this->view("/admin/academy/sessions", "Sessions", $data);
    }

    public function academyAttendances() {$this->view("/admin/academy/attendances", "Attendances");}
    public function academyReserves() {$this->view("/admin/academy/reserves", "Reserves");}
    public function academySchedulingRoles() {$this->view("/admin/academy/scheduling-roles", "Scheduling Roles");}
    public function academyDiscounts() {$this->view("/admin/academy/discounts", "Discounts");}

    // public function academyPayment() {$this->view("/admin/academy/payment", "Academy Payment");}
    public function academyPayments() {$this->view("/admin/academy/payments", "Payments");}

    public function academyLedgerTransactions() {$this->view("/admin/academy/ledger-transactions", "Ledger Transactions");}
    public function academyRoles() {$this->view("/admin/academy/roles", "Roles");}
    public function academyPermissions() {$this->view("/admin/academy/permissions", "Permissions");}
    public function academyNotifications() {$this->view("/admin/academy/notifications", "Notifications");}
    public function academySystemSettings() {$this->view("/admin/academy/system-settings", "System Settings");}

    public function academyContracts() {
        $data['currencies'] = $this->get_all_currency();
        foreach(session_get('branches_id') as $branch) {
            $data['branches_members'][$branch['branch_id']] = $this->get_all_members_by_branch_id($branch['branch_id']);
        }
        $data['branches'] = $this->get_branches_of_academy_by_user_id();
        $this->view("/admin/academy/contracts", "Contracts", $data);
    }

    public function academyClassrooms() {
        // $academy_id = $this->get_academy_id_by_user_id(session_get('user_id'));
        // $branches_id = $this->get_branches_by_academy_id($academy_id);
        $branches_id = session_get('branches_id');
        
        if(is_array($branches_id)) {
            foreach($branches_id as $branch) {
                $data['classroom_types'][$branch['branch_id']] = $this->get_classroom_types_by_branch_id($branch['branch_id']);
            }
            foreach($branches_id as $branch) {
                $data['classrooms'][$branch['branch_id']] = $this->get_classrooms_by_branch_id($branch['branch_id']);
            }
        } else {
            $data['classroom_types'][$branches_id] = $this->get_classroom_types_by_branch_id($branches_id);
            $data['classrooms'][$branches_id] = $this->get_classrooms_by_branch_id($branches_id);
        }
        // $data['classroom_types'] = $this->get_classroom_types();
        $data['branches'] = $this->get_branches_of_academy_by_user_id();
        $this->view("/admin/academy/classrooms", "classrooms", $data);
    }

    public function academyClassroomTypes() {
        // foreach(session_get('branches_id') as $branch) {
        //     $data['classroom_types'][$branch['branch_id']] = $this->get_classroom_types_by_branch_id($branch['branch_id']);
        // }
        $branches_id = session_get('branches_id');
        
        if(is_array($branches_id)) {
            foreach($branches_id as $branch) {
                $data['classroom_types'][$branch['branch_id']] = $this->get_classroom_types_by_branch_id($branch['branch_id']);
            }
        } else {
            $data['classroom_types'][$branches_id] = $this->get_classroom_types_by_branch_id($branches_id);
        }
        $data['branches'] = $this->get_branches_of_academy_by_user_id();
        $this->view("/admin/academy/classroom-types", "Classroom Types", $data);
    }

    public function academyClassroomAssets() {
        // $academy_id = $this->get_academy_id_by_user_id(session_get('user_id'));
        // $branches_id = $this->get_branches_by_academy_id($academy_id);
        $branches_id = session_get('branches_id');
        
        if(is_array($branches_id)) {
            foreach($branches_id as $branch) {
                $data['classrooms'][$branch['branch_id']] = $this->get_classrooms_by_branch_id($branch['branch_id']);
            }
        } else {
            $data['classrooms'][$branches_id] = $this->get_classrooms_by_branch_id($branches_id);
        }
        foreach($data['classrooms'] as $classrooms) {
            foreach($classrooms as $classroom) {
                $data['classroom_assets'][$classroom['classroom_id']] = $this->get_classroom_assets_by_classroom_id($classroom['classroom_id']);
            }
        }
        $data['branches'] = $this->get_branches_of_academy_by_user_id();
        $this->view("/admin/academy/classroom_assets", "Classroom Assets", $data);
    }

    public function academyCourses() {
        $data['levels'] = $this->get_academic_levels();
        foreach(session_get('branches_id') as $branch) {
            $data['branches_courses'][$branch['branch_id']] = $this->get_all_courses_by_branch_id($branch['branch_id']);
        }
        $data['branches'] = $this->get_branches_of_academy_by_user_id();
        $this->view("/admin/academy/courses", "Courses", $data);
    }

    public function academyTerms() {
        $data['currencies'] = $this->get_all_currency();
        foreach(session_get('branches_id') as $branch) {
            $data['branches_courses'][$branch['branch_id']] = $this->get_all_courses_by_branch_id($branch['branch_id']);
        }
        
        foreach($data['branches_courses'] as $courses) {
            foreach($courses as $course) {
                $data['courses_terms'][$course['course_id']] = $this->get_course_terms_by_course_id($course['course_id']);
            }
        }
        $data['branches'] = $this->get_branches_of_academy();
        $this->view("/admin/academy/terms", "Terms", $data);
    }

    public function academyUserProfile() {$this->view("/admin/academy/user-profile", "Academy User Profile");}


    // ==========================================
    //              ACADEMY MODULE
    // ==========================================

    public function academies() {
        // $data['academies'] = $this->get_all_academies();
        $data['academies'] = 0;
        $this->view("/admin/academy/academies", "آموزشگاه‌ها", $data);
    }

    // public function academyBranches() {
    //     $data['branches'] = $this->get_all_academy_branches();
    //     $this->view("/admin/academy/branches", "شعب آموزشگاه", $data);
    // }

    // public function academyBranchPhones() {
    //     $data['phones'] = $this->get_all_academy_branch_phones();
    //     $this->view("/admin/academy/branch-phones", "شماره‌های تماس شعب", $data);
    // }

    // public function academyBranchUrls() {
    //     $data['urls'] = $this->get_all_academy_branch_urls();
    //     $this->view("/admin/academy/branch-urls", "لینک‌های شعبه", $data);
    // }

    public function academyBranchBookings() {
        $data['bookings'] = $this->get_all_academy_branch_bookings();
        $this->view("/admin/academy/branch-bookings", "رزروها", $data);
    }

    public function academyBranchClassrooms() {
        $data['classrooms'] = $this->get_all_academy_branch_classrooms();
        $this->view("/admin/academy/branch-classrooms", "کلاس‌ها و اتاق‌ها", $data);
    }

    public function academyBranchClassroomAssets() {
        $data['assets'] = $this->get_all_academy_branch_classroom_assets();
        $this->view("/admin/academy/branch-classroom-assets", "تجهیزات کلاس‌ها", $data);
    }
    // academy_branch_member_contracts
    public function academyBranchContracts() {
        $data['contracts'] = $this->get_all_academy_branch_contracts();
        $this->view("/admin/academy/branch-contracts", "قراردادهای شعبه", $data);
    }

    public function academyBranchCourses() {
        $data['courses'] = $this->get_all_academy_branch_courses();
        $this->view("/admin/academy/branch-courses", "دوره‌های شعبه", $data);
    }

    public function academyBranchCourseTerms() {
        $data['terms'] = $this->get_all_academy_branch_course_terms();
        $this->view("/admin/academy/branch-course-terms", "ترم‌های دوره", $data);
    }

    public function academyBranchCourseTermEnrollments() {
        $data['enrollments'] = $this->get_all_academy_branch_course_term_enrollments();
        $this->view("/admin/academy/branch-course-term-enrollments", "ثبت‌نام‌ها", $data);
    }

    public function academyInvoices() {
        foreach(session_get('branches_id') as $branch) {
            $data['invoices'][$branch['branch_id']] = $this->get_all_academy_branch_course_term_invoices_by_branch_id($branch['branch_id']);
        }
        // $data['currencies'] = $this->get_all_currency();
        foreach(session_get('branches_id') as $branch) {
            $data['branches_courses'][$branch['branch_id']] = $this->get_all_courses_by_branch_id($branch['branch_id']);
        }
        foreach($data['branches_courses'] as $courses) {
            foreach($courses as $course) {
                $data['courses_terms'][$course['course_id']] = $this->get_course_terms_by_course_id($course['course_id']);
            }
        }
        $data['branches'] = $this->get_branches_of_academy_by_user_id();
        $this->view("/admin/academy/invoices", "فاکتورهای ترم", $data);
    }

    // public function academyBranchCourseTermInvoiceDiscounts() {
    //     $data['discounts'] = $this->get_all_academy_branch_course_term_invoice_discounts();
    //     $this->view("/admin/academy/branch-course-term-invoice-discounts", "تخفیف‌های فاکتور", $data);
    // }

    // ==========================================================================================================
    // ==========================================================================================================
    // ==========================================================================================================
    // ==========================================================================================================
    // ==========================================================================================================
    // ==========================================================================================================
    // ==========================================================================================================
    // ==========================================================================================================
    // ==========================================================================================================
    // ==========================================================================================================
    // ==========================================================================================================
    // ==========================================================================================================
    // ==========================================================================================================
    // ==========================================================================================================
    // ==========================================================================================================
    // ==========================================================================================================
    // ==========================================================================================================
    // ==========================================================================================================
    // ==========================================================================================================
    // ==========================================================================================================
    // ==========================================================================================================
    // ==========================================================================================================
    // ==========================================================================================================
    // ==========================================================================================================
    // ==========================================================================================================



    public function invoiceInstallments(int $invoice_id) {
        $data['invoice'] = $this->get_term_invoice_by_id($invoice_id);
        $data['installments'] = $this->get_all_academy_branch_course_term_invoice_installments_by_invoice_id($invoice_id);
        $this->view("/admin/academy/invoice_installments", "اقساط فاکتور", $data);
    }

    public function academyBranchCourseTermSessions() {
        $data['sessions'] = $this->get_all_academy_branch_course_term_sessions();
        $this->view("/admin/academy/branch-course-term-sessions", "جلسات ترم", $data);
    }

    public function sessionAttendances(int $session_id) {
        $data['session'] = $this->get_academy_branch_course_term_session_by_id($session_id);
        $data['members'] = $this->get_all_members_of_term_session_by_session_id($session_id);
        $data['attendances'] = $this->get_academy_branch_course_term_session_attendances_by_session_id($session_id);
        $this->view("/admin/academy/session_attendances", "حضور و غیاب", $data);
    }

    public function sessionChanges(int $session_id) {
        $data['session'] = $this->get_academy_branch_course_term_session_by_id($session_id);
        foreach(session_get('branches_id') as $branch) {
            $data['branches_members'][$branch['branch_id']] = $this->get_all_members_by_branch_id($branch['branch_id'], 'teachers');
        }
        foreach(session_get('branches_id') as $branch) {
            $data['branches_classrooms'][$branch['branch_id']] = $this->get_classrooms_by_branch_id($branch['branch_id']);
        }
        $data['changes'] = $this->get_academy_branch_course_term_session_changes_by_session_id($session_id);
        $this->view("/admin/academy/session_changes", "تغییرات جلسات", $data);
    }

    public function sessionClassrooms(int $session_id) {
        $data['session'] = $this->get_academy_branch_course_term_session_by_id($session_id);
        $data['classrooms'] = $this->get_academy_branch_course_term_session_classrooms_by_session_id($session_id);
        foreach(session_get('branches_id') as $branch) {
            $data['branches_classrooms'][$branch['branch_id']] = $this->get_classrooms_by_branch_id($branch['branch_id']);
        }
        $this->view("/admin/academy/session_classrooms", "کلاس‌های جلسات", $data);
    }


    public function academyBranchCourseTermTeachers() {
        $data['teachers'] = $this->get_all_academy_branch_course_term_teachers();
        $this->view("/admin/academy/branch-course-term-teachers", "معلمان ترم", $data);
    }

    public function academyBranchCourseTermWaitingList() {
        $data['waiting_lists'] = $this->get_all_academy_branch_course_term_waiting_list();
        $this->view("/admin/academy/branch-course-term-waiting-list", "لیست انتظار", $data);
    }

    public function academyBranchMembers() {
        $data['members'] = $this->get_all_academy_branch_members();
        $this->view("/admin/academy/branch-members", "اعضای شعبه", $data);
    }

    public function academyBranchMemberPermissions() {
        $data['permissions'] = $this->get_all_academy_branch_member_permissions();
        $this->view("/admin/academy/branch-member-permissions", "مجوزهای اعضا", $data);
    }

    public function academyBranchMemberSchedules() {
        $data['schedules'] = $this->get_all_academy_branch_member_schedules();
        $this->view("/admin/academy/branch-member-schedules", "برنامه زمانی اعضا", $data);
    }

    public function academyBranchSchedulingQueue() {
        $data['queue'] = $this->get_all_academy_branch_scheduling_queue();
        $this->view("/admin/academy/branch-scheduling-queue", "صف زمان‌بندی", $data);
    }

    public function academyBranchSchedulingRules() {
        $data['rules'] = $this->get_all_academy_branch_scheduling_rules();
        $this->view("/admin/academy/branch-scheduling-rules", "قوانین زمان‌بندی", $data);
    }

    // ==========================================
    //              ACCESS & PERMISSION
    // ==========================================

    public function accessSystemPermissions() {
        $data['permissions'] = $this->get_all_access_system_permissions();
        $this->view("/admin/access/permissions", "مجوزهای سیستمی", $data);
    }

    public function accessSystemRoles() {
        $data['roles'] = $this->get_all_access_system_roles();
        $this->view("/admin/access/roles", "نقش‌های سیستمی", $data);
    }

    public function accessSystemRolePermissions() {
        $data['role_permissions'] = $this->get_all_access_system_role_permissions();
        $this->view("/admin/access/role-permissions", "مجوزهای نقش‌ها", $data);
    }

    // ==========================================
    //              CONVERSATION
    // ==========================================

    public function conversations() {
        $data['conversations'] = $this->get_all_conversations();
        $this->view("/admin/conversations/list", "گفتگوها", $data);
    }

    public function conversationMembers() {
        $data['members'] = $this->get_all_conversation_members();
        $this->view("/admin/conversations/members", "اعضای گفتگو", $data);
    }

    // ==========================================
    //              FINANCIAL SYSTEM
    // ==========================================

    public function financialSystemAccounts() {
        $data['accounts'] = $this->get_all_financial_system_accounts();
        $this->view("/admin/financial/accounts", "حساب‌های مالی", $data);
    }

    public function financialSystemCurrency() {
        $data['currencies'] = $this->get_all_financial_system_currency();
        $this->view("/admin/financial/currency", "ارزها", $data);
    }

    public function financialSystemDiscounts() {
        $data['discounts'] = $this->get_all_financial_system_discounts();
        $this->view("/admin/financial/discounts", "تخفیف‌ها", $data);
    }

    public function financialSystemLedgerEntries() {
        $data['entries'] = $this->get_all_financial_system_ledger_entries();
        $this->view("/admin/financial/ledger", "دفترکل", $data);
    }

    public function financialSystemPayments() {
        $data['payments'] = $this->get_all_financial_system_payments();
        $this->view("/admin/financial/payments", "پرداخت‌ها", $data);
    }

    public function financialSystemRefunds() {
        $data['refunds'] = $this->get_all_financial_system_refunds();
        $this->view("/admin/financial/refunds", "بازگشت وجوه", $data);
    }

    public function financialSystemTransactions() {
        $data['transactions'] = $this->get_all_financial_system_transactions();
        $this->view("/admin/financial/transactions", "تراکنش‌ها", $data);
    }

    // academy_branch_booking_enrollments
    // academy_branch_classroom_types
    // academy_branch_types
    // lessons
    // password_resets
    // system_events
    // taggables
    // translations
    // user_auth_providers
    // user_availability_exceptions
    // user_contacts
    // user_lessons
    // user_permissions
    // user_permission_cache
    // user_poll_options
    // user_poll_votes
    // user_rating_summaries
    // user_relationships
    // user_reputation
    // user_roles
    // user_sessions
    // user_settings

    // ==========================================
    //              USER MODULE
    // ==========================================

    public function userExperiences() {
        $data['iran_cities'] = $this->get_iran_cities();
        $data['iran_provinces'] = $this->get_iran_provinces();
        foreach(session_get('branches_id') as $branch) {
            $branch_user_id = $this->get_user_id_by_branch_id($branch['branch_id']);
            $data['experiences'][$branch['branch_id']] = $this->get_all_user_experiences_by_user_id($branch_user_id);
        }
        $data['branches'] = $this->get_branches_of_academy();
        $data['users'] = $this->get_user();
        $this->view("/admin/users/user_experience", "تجربیات کاری", $data);
    }

    public function userAwards() {
        foreach(session_get('branches_id') as $branch) {
            $branch_user_id = $this->get_user_id_by_branch_id($branch['branch_id']);
            $data['awards'][$branch['branch_id']] = $this->get_all_user_awards_by_user_id($branch_user_id);
        }
        $data['branches'] = $this->get_branches_of_academy();
        $data['users'] = $this->get_user();
        $this->view("/admin/users/user_award", "جوایز کاربران", $data);
    }

    public function userCertificates() {
        foreach(session_get('branches_id') as $branch) {
            $branch_user_id = $this->get_user_id_by_branch_id($branch['branch_id']);
            $data['certificates'][$branch['branch_id']] = $this->get_all_user_certificates_by_user_id($branch_user_id);
        }
        $data['branches'] = $this->get_branches_of_academy();
        $data['users'] = $this->get_user();
        $this->view("/admin/users/user_certificate", "گواهینامه‌ها", $data);
    }

    public function userEducations() {
        foreach(session_get('branches_id') as $branch) {
            $branch_user_id = $this->get_user_id_by_branch_id($branch['branch_id']);
            $data['educations'][$branch['branch_id']] = $this->get_all_user_educations_by_user_id($branch_user_id);
        }
        $data['branches'] = $this->get_branches_of_academy();
        $data['users'] = $this->get_user();
        $this->view("/admin/users/user_education", "تحصیلات", $data);
    }

    public function userEvents() {
        $data['iran_cities'] = $this->get_iran_cities();
        $data['iran_provinces'] = $this->get_iran_provinces();
        foreach(session_get('branches_id') as $branch) {
            $branch_user_id = $this->get_user_id_by_branch_id($branch['branch_id']);
            $data['events'][$branch['branch_id']] = $this->get_all_user_events_by_user_id($branch_user_id);
        }
        $data['branches'] = $this->get_branches_of_academy();
        $data['users'] = $this->get_user();
        // $data['addresses'] = $this->get_all_addresses();
        $this->view("/admin/users/user_event", "رویدادها", $data);
    }



    public function editUserInstrument(int $user_instrument_id) {
        $data['levels'] = $this->get_academic_levels();
        $data['instrument'] = $this->get_user_instrument_by_instrument_id($user_instrument_id)[0];
        // $data['branches'] = $this->get_branches_of_academy_by_user_id();
        $data['users'] = $this->get_user();
        $this->view("/admin/users/edit_user_instrument", "ویرایش سازها", $data);
    }

    public function userInstruments() {
        $data['levels'] = $this->get_academic_levels();
        foreach(session_get('branches_id') as $branch) {
            $branch_user_id = $this->get_user_id_by_branch_id($branch['branch_id']);
            $data['instruments'][$branch['branch_id']] = $this->get_all_user_instruments_by_user_id($branch_user_id);
        }
        $data['branches'] = $this->get_branches_of_academy_by_user_id();
        $data['users'] = $this->get_user();
        // $data['levels'] = $this->get_all_levels();
        $this->view("/admin/users/user_instrument", "سازهای کاربران", $data);
    }



    public function editUserLesson(int $user_lesson_id) {
        $data['levels'] = $this->get_academic_levels();
        $data['lesson'] = $this->get_user_lesson_by_lesson_id($user_lesson_id)[0];
        // $data['branches'] = $this->get_branches_of_academy_by_user_id();
        $data['users'] = $this->get_user();
        $this->view("/admin/users/edit_user_lesson", "ویرایش درس ها", $data);
    }


    public function userLessons() {
        $data['levels'] = $this->get_academic_levels();
        foreach(session_get('branches_id') as $branch) {
            $branch_user_id = $this->get_user_id_by_branch_id($branch['branch_id']);
            $data['lessons'][$branch['branch_id']] = $this->get_all_user_lessons_by_user_id($branch_user_id);
        }
        $data['branches'] = $this->get_branches_of_academy_by_user_id();
        $data['users'] = $this->get_user();
        $this->view("/admin/users/user_lesson", "درس ها", $data);
    }



    public function editUserPoll(int $user_poll_id) {
        $data['poll'] = $this->get_user_poll_by_poll_id($user_poll_id)[0];
        // $data['branches'] = $this->get_branches_of_academy_by_user_id();
        $data['users'] = $this->get_user();
        $this->view("/admin/users/edit_user_poll", "ویرایش نظرسنجی‌", $data);
    }

    public function userPolls() {
        foreach(session_get('branches_id') as $branch) {
            $branch_user_id = $this->get_user_id_by_branch_id($branch['branch_id']);
            $data['polls'][$branch['branch_id']] = $this->get_all_user_polls_by_user_id($branch_user_id);
        }
        $data['branches'] = $this->get_branches_of_academy_by_user_id();
        $data['users'] = $this->get_user();
        $this->view("/admin/users/user_poll", "نظرسنجی‌ها", $data);
    }

    public function userPollOptions(int $poll_id) {
        $data['poll'] = $this->get_user_poll_by_poll_id($poll_id);
        $data['poll_options'] = $this->get_all_user_poll_options_by_poll_id($poll_id);
        $data['users'] = $this->get_user();
        $this->view("/admin/users/user_poll_option", "گزینه های نظرسنجی‌", $data);
    }

    public function userPollVotes(int $poll_id) {
        $data['poll'] = $this->get_user_poll_by_poll_id($poll_id);
        $data['poll_options'] = $this->get_all_user_poll_options_by_poll_id($poll_id);
        $data['poll_votes'] = $this->get_all_user_poll_votes_by_poll_id($poll_id);
        $data['users'] = $this->get_user();
        $this->view("/admin/users/user_poll_vote", "آرای نظرسنجی‌", $data);
    }


    public function showMediaList() {
        foreach(session_get('branches_id') as $branch) {
            $branch_user_id = $this->get_user_id_by_branch_id($branch['branch_id']);
            $data['medias'][$branch['branch_id']] = $this->get_all_media_files_by_user_id($branch_user_id);
        }
        $data['users'] = $this->get_user();
        $data['branches'] = $this->get_branches_of_academy_by_user_id();
        $this->view("/admin/show-media-list", "رسانه‌های کاربران", $data);
    }


        // $data['users'] = $this->get_user();
    // public function instruments() {
    //     $data['instruments'] = $this->get_all_instruments();
    //     $this->view("/admin/instruments", "سازها", $data);
    // }
    
    // public function lessons() {
    //     $data['lessons'] = $this->get_all_lessons();
    //     $this->view("/admin/lessons", "درس ها", $data);
    // }
    
    // ========================================================================================================================
    // ========================================================================================================================
    // ========================================================================================================================
    // ========================================================================================================================
    // ========================================================================================================================


    /*
        academy_beanch_booking_enrollments
        academy_beanch_cource_term_invoice_installments
        academy_beanch_cource_term_session_attendances
        academy_beanch_cource_term_session_classrooms
        academy_beanch_cource_term_session_changes
        academy_beanch_cource_term_session_exceptions
        academy_beanch_member_roles
        academy_beanch_member_permissions
        academy_beanch_member_role_permissions
        academy_beanch_member_schedules
        academy_beanch_scheduling_rules
        academy_beanch_scheduling_queues
    */


    public function users() {
        $data['users'] = $this->get_all_users();
        $this->view("/admin/users/list", "کاربران", $data);
    }

    public function userAddresses() {
        $data['addresses'] = $this->get_all_user_addresses();
        $this->view("/admin/users/addresses", "آدرس کاربران", $data);
    }

    public function userApprovals() {
        $data['approvals'] = $this->get_all_user_approvals();
        $this->view("/admin/users/approvals", "تاییدیه‌ها", $data);
    }

    public function userAuditLogs() {
        $data['logs'] = $this->get_all_user_audit_logs();
        $this->view("/admin/users/audit-logs", "لاگ‌های فعالیت", $data);
    }

    public function userAvailabilities() {
        $data['availabilities'] = $this->get_all_user_availabilities();
        $this->view("/admin/users/availabilities", "در دسترس بودن کاربران", $data);
    }

    public function userBadges() {
        $data['badges'] = $this->get_all_user_badges();
        $this->view("/admin/users/badges", "نشان‌ها", $data);
    }

    public function userComments() {
        $data['comments'] = $this->get_all_user_comments();
        $this->view("/admin/users/comments", "کامنت‌ها", $data);
    }

    public function userFavorites() {
        $data['favorites'] = $this->get_all_user_favorites();
        $this->view("/admin/users/favorites", "علاقه‌مندی‌ها", $data);
    }

    public function userMerges() {
        $data['merges'] = $this->get_all_user_merges();
        $this->view("/admin/users/merges", "ادغام کاربران", $data);
    }

    public function userMessages() {
        $data['messages'] = $this->get_all_user_messages();
        $this->view("/admin/users/messages", "پیام‌ها", $data);
    }

    public function userNotifications() {
        $data['notifications'] = $this->get_all_user_notifications();
        $this->view("/admin/users/notifications", "اعلان‌ها", $data);
    }

    public function userPoints() {
        $data['points'] = $this->get_all_user_points();
        $this->view("/admin/users/points", "امتیازات کاربران", $data);
    }


    public function userPosts() {
        $data['posts'] = $this->get_all_user_posts();
        $this->view("/admin/users/posts", "پست‌های کاربران", $data);
    }

    public function userProfiles() {
        $data['profiles'] = $this->get_all_user_profiles();
        $this->view("/admin/users/profiles", "پروفایل کاربران", $data);
    }

    public function userPublications() {
        $data['publications'] = $this->get_all_user_publications();
        $this->view("/admin/users/publications", "انتشارات", $data);
    }

    public function userRatings() {
        $data['ratings'] = $this->get_all_user_ratings();
        $this->view("/admin/users/ratings", "امتیازدهی‌ها", $data);
    }

    public function userReports() {
        $data['reports'] = $this->get_all_user_reports();
        $this->view("/admin/users/reports", "گزارش‌های کاربران", $data);
    }

    public function userReputationLogs() {
        $data['logs'] = $this->get_all_user_reputation_logs();
        $this->view("/admin/users/reputation-logs", "لاگ اعتبار", $data);
    }

    public function userReviews() {
        $data['reviews'] = $this->get_all_user_reviews();
        $this->view("/admin/users/reviews", "نظرات", $data);
    }

    public function userSpecialties() {
        $data['specialties'] = $this->get_all_user_specialties();
        $this->view("/admin/users/specialties", "تخصص‌ها", $data);
    }

    public function userVerifications() {
        $data['verifications'] = $this->get_all_user_verifications();
        $this->view("/admin/users/verifications", "احراز هویت‌ها", $data);
    }

    // ==========================================
    //              OTHER TABLES
    // ==========================================

    public function languages() {
        $data['languages'] = $this->get_all_languages();
        $this->view("/admin/languages", "زبان‌ها", $data);
    }

    public function levels() {
        $data['levels'] = $this->get_all_levels();
        $this->view("/admin/levels", "سطوح", $data);
    }

    public function mediaFiles() {
        $data['media'] = $this->get_all_media_files();
        $this->view("/admin/media/list", "فایل‌های رسانه", $data);
    }

    public function otpCodes() {
        $data['otps'] = $this->get_all_otp_codes();
        $this->view("/admin/otp/list", "کدهای یکبار مصرف", $data);
    }

    // public function settings() {
    //     $data['settings'] = $this->get_all_settings();
    //     $this->view("/admin/settings", "تنظیمات سیستم", $data);
    // }

    public function tags() {
        $data['tags'] = $this->get_all_tags();
        $this->view("/admin/tags", "تگ‌ها", $data);
    }

    public function verificationLevels() {
        $data['levels'] = $this->get_all_verification_levels();
        $this->view("/admin/verification/levels", "سطوح احراز هویت", $data);
    }


}