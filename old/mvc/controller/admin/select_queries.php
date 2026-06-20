<?php

trait AdminSelectQueriesTrait {

// ==========================================================
// ==========================================================
    private function get_posts() {return Db::getInstance()->query("SELECT * FROM posts");}
    private function get_posts_ordered_by_id_desc() {return Db::getInstance()->query("SELECT * FROM posts ORDER BY post_id DESC");}

    private function get_posts_with_id(int $post_id) {
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';

        return Db::getInstance()->query("SELECT * FROM posts 
            LEFT OUTER JOIN translations ON posts.post_id = translations.table_id 
            WHERE post_id=:post_id AND locale=:locale AND table_name=:table_name
        ", array(
            'post_id'    => $post_id,
            'locale'     => $locale,
            'table_name' => 'posts',
        ));
    }


// SELECT * FROM posts LEFT OUTER JOIN users ON posts.author_id = users.user_id WHERE posts.status='published' AND type='post'


    // public static function get_all_posts(){
    //     return Db::getInstance()->query("SELECT * FROM posts");
    // }

    private function get_all_role(){
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';

        return Db::getInstance()->query("SELECT * FROM access_system_roles 
            LEFT OUTER JOIN translations ON access_system_roles.role_id = translations.table_id 
            WHERE table_name=:table_name AND locale=:locale ORDER BY role_id DESC
        ", array(
            'table_name' => 'access_system_roles',
            'locale'     => $locale,
        ));
    }

    // private function get_all_settings(){
    //     global $config;
    //     $locale = $config['app']['lang'] ?? 'fa';

    //     return Db::getInstance()->query("SELECT * FROM sor_settings 
    //         LEFT OUTER JOIN translations ON sor_settings.setting_id = translations.table_id 
    //         WHERE translations.table_name=:table_name AND locale=:locale ORDER BY setting_id DESC
    //     ", array(
    //         'table_name' => 'sor_settings',
    //         'locale' => $locale,
    //     ));
    // }


    private function get_all_menu_settings(){
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';

        return Db::getInstance()->query("SELECT * FROM sor_settings 
            LEFT OUTER JOIN translations ON sor_settings.setting_id = translations.table_id 
            WHERE translations.table_name=:table_name AND locale=:locale AND url IS NOT NULL ORDER BY setting_id DESC
        ", array(
            'table_name' => 'sor_settings',
            'locale' => $locale,
        ));
    }


    public static function get_menu_with_permissions(){
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';
        
        return Db::getInstance()->query("SELECT
            access_system_permissions.permission_id, 
            access_system_permissions.name, 
            access_system_permissions.type, 
            access_system_permissions.group_name, 
            access_system_permissions.approval, 
            sor_settings.setting_id, 
            -- sor_settings.page, 
            -- sor_settings.sort_order, 
            -- sor_settings.variable_name, 
            sor_settings.url, 
            translations.title
            FROM access_system_setting_permissions
            LEFT OUTER JOIN sor_settings ON access_system_setting_permissions.setting_id = sor_settings.setting_id 
            LEFT OUTER JOIN access_system_permissions ON access_system_permissions.permission_id = access_system_setting_permissions.permission_id 
            LEFT OUTER JOIN translations ON sor_settings.setting_id = translations.table_id 
            WHERE sor_settings.variable_name LIKE '%panel_sidebar_%'
            AND translations.locale = :locale 
            AND translations.table_name = :table_name
            ORDER BY sort_order ASC
        ", array(
            'locale'     => $locale,
            'table_name' => 'sor_settings',
        ));
    }




    public static function get_user_permissions(){
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';

        // return Db::getInstance()->query("SELECT 
        //     user_permissions.permission_id
        //     FROM users
        //     LEFT OUTER JOIN user_permissions ON users.user_id = user_permissions.user_id 
        //     LEFT OUTER JOIN translations ON user_permissions.user_permission_id = translations.table_id 
        //     WHERE users.user_id = :user_id 
        //     AND translations.locale = :locale 
        //     AND translations.table_name = :table_name
        // ", array(
        //     'user_id'    => session_get('user_id'),
        //     'locale'     => $locale,
        //     'table_name' => 'user_permissions',
        // ));


        return Db::getInstance()->query("SELECT 
            access_system_permissions.permission_id
            FROM users
            LEFT OUTER JOIN access_system_roles ON access_system_roles.role_id = users.role_id 
            LEFT OUTER JOIN access_system_role_permissions ON access_system_roles.role_id = access_system_role_permissions.role_id 
            LEFT OUTER JOIN access_system_permissions ON access_system_role_permissions.permission_id = access_system_permissions.permission_id 
            LEFT OUTER JOIN translations ON access_system_permissions.permission_id = translations.table_id 
            WHERE users.user_id = :user_id 
            AND translations.locale = :locale 
            AND translations.table_name = :table_name
        ", array(
            'user_id'    => session_get('user_id'),
            'locale'     => $locale,
            'table_name' => 'access_system_permissions',
        ));



    }




            // user_id => 1
            //  => 68
            //  => 
            //  => 
            // title => عنوان دسترسی اول



    private function get_all_permission(){
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';

        return Db::getInstance()->query("SELECT * FROM access_system_permissions 
            LEFT OUTER JOIN translations ON access_system_permissions.permission_id = translations.table_id 
            WHERE table_name=:table_name AND locale=:locale ORDER BY group_name DESC
        ", array(
            'table_name' => 'access_system_permissions',
            'locale' => $locale,
        ));
    }


    private function get_all_access_system_role_permissions(){
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';

        return Db::getInstance()->query("SELECT * FROM access_system_role_permissions 
            LEFT OUTER JOIN translations ON access_system_role_permissions.role_permission_id = translations.table_id 
            WHERE table_name=:table_name AND locale=:locale ORDER BY role_permission_id DESC
        ", array(
            'table_name' => 'access_system_role_permissions',
            'locale' => $locale,
        ));
    }


    private function get_all_access_system_setting_permissions(){
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';

        return Db::getInstance()->query("SELECT * FROM access_system_setting_permissions 
            LEFT OUTER JOIN translations ON access_system_setting_permissions.setting_permission_id = translations.table_id 
            WHERE table_name=:table_name AND locale=:locale ORDER BY setting_permission_id DESC
        ", array(
            'table_name' => 'access_system_setting_permissions',
            'locale' => $locale,
        ));
    }

    private function get_all_posts(string $sort_filter){
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';

        return Db::getInstance()->query("SELECT
            posts.*, 
            users.user_id, users.email, users.username, users.gender, users.visibility, 
            translations.translation_id, translations.locale, translations.code, translations.title, translations.brief, translations.description, translations.subject_1, translations.text_1, translations.subject_2, translations.text_2, translations.subject_3, translations.text_3, translations.content 
            FROM posts
            LEFT OUTER JOIN translations ON posts.post_id = translations.table_id 
            LEFT OUTER JOIN users ON posts.author_id = users.user_id 
            WHERE (posts.status IN (:status_1, :status_2, :status_3, :status_4, :status_5)) 
            AND (posts.type IN (:type_1, :type_2)) 
            AND translations.locale = :locale 
            AND translations.table_name = :table_name 
            ORDER BY $sort_filter
        ", array(
            'status_1'   => 'published',
            'status_2'   => 'private',
            'status_3'   => 'pending',
            'status_4'   => 'draft',
            'status_5'   => 'trash',
            'type_1'     => 'post',
            'type_2'     => 'music_theory',
            'locale'     => $locale,
            'table_name' => 'posts',
        ));
    }

    private function get_my_posts(string $sort_filter){
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';

        return Db::getInstance()->query("SELECT
            posts.*, 
            users.user_id, users.email, users.username, users.gender, users.visibility, 
            translations.translation_id, translations.locale, translations.code, translations.title, translations.brief, translations.description, translations.subject_1, translations.text_1, translations.subject_2, translations.text_2, translations.subject_3, translations.text_3, translations.content 
            FROM posts
            LEFT OUTER JOIN translations ON posts.post_id = translations.table_id 
            LEFT OUTER JOIN users ON posts.author_id = users.user_id 
            WHERE (posts.type IN (:type_1, :type_2)) 
            AND author_id=:author_id 
            AND translations.locale = :locale 
            AND translations.table_name = :table_name 
            ORDER BY $sort_filter
        ", array(
            'type_1'     => 'post',
            'type_2'     => 'music_theory',
            'author_id'  => session_get('user_id'),
            'locale'     => $locale,
            'table_name' => 'posts',
        ));
    }

    private function get_filtered_posts(string $type, string $status, string $sort_filter){
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';

        return Db::getInstance()->query("SELECT
            posts.*, 
            users.user_id, users.email, users.username, users.gender, users.visibility, 
            translations.translation_id, translations.locale, translations.code, translations.title, translations.brief, translations.description, translations.subject_1, translations.text_1, translations.subject_2, translations.text_2, translations.subject_3, translations.text_3, translations.content 
            FROM posts
            LEFT OUTER JOIN translations ON posts.post_id = translations.table_id 
            LEFT OUTER JOIN users ON posts.author_id = users.user_id 
            WHERE type=:type AND posts.status=:status
            AND translations.locale = :locale 
            AND translations.table_name = :table_name 
            ORDER BY $sort_filter
        ", array(
            'type'       => $type,
            'status'     => $status,
            'locale'     => $locale,
            'table_name' => 'posts',
        ));
    }


    private function get_academy() {return Db::getInstance()->query("SELECT * FROM academies");}
    private function get_contact() {return Db::getInstance()->query("SELECT * FROM sor_contacts WHERE type='contact'");}
    private function get_comments() {return Db::getInstance()->query("SELECT * FROM sor_contacts WHERE type='comment'");}
    private function get_user() {
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';

        return Db::getInstance()->query("SELECT * FROM users 
            LEFT OUTER JOIN translations ON users.user_id = translations.table_id
            WHERE translations.table_name=:table_name AND locale=:locale
        ", array(
            'table_name' => "users",
            'locale' => $locale,
        ));
    }


    public static function get_all_addresses(){
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';

        return Db::getInstance()->query("SELECT * FROM translations 
            LEFT OUTER JOIN user_addresses ON user_addresses.address_id = translations.table_id 
            WHERE translations.table_name=:table_name AND locale=:locale", 
        array(
            'table_name' => 'user_addresses',
            'locale' => $locale,
        ));
    }


    public static function get_all_levels(){
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';

        return Db::getInstance()->query("SELECT * FROM translations 
            LEFT OUTER JOIN levels ON levels.level_id = translations.table_id 
            WHERE translations.table_name=:table_name AND locale=:locale", 
        array(
            'table_name' => 'levels',
            'locale' => $locale,
    ));
    }



    public static function get_settings_ordered_by_id_desc() {
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';
        return Db::getInstance()->query("SELECT * FROM sor_settings 
            LEFT OUTER JOIN translations ON sor_settings.setting_id = translations.table_id 
            WHERE locale=:locale AND translations.table_name=:table_name 
            ORDER BY setting_id DESC
        ", array(
            'locale'     => $locale,
            'table_name' => 'sor_settings',
        ));
    }

    public static function get_settings() {
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';
        return Db::getInstance()->query("SELECT * FROM sor_settings
            LEFT OUTER JOIN translations ON sor_settings.setting_id = translations.table_id 
            WHERE locale=:locale AND translations.table_name=:table_name 
        ", array(
            'locale'     => $locale,
            'table_name' => 'sor_settings',
        ));
    }

    private function get_categories_ordered_by_id_desc() {
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';
        return Db::getInstance()->query("SELECT * FROM sor_settings 
            LEFT OUTER JOIN translations ON sor_settings.setting_id = translations.table_id 
            WHERE locale=:locale AND translations.table_name=:table_name AND variable_name LIKE '%article_category_%' 
            ORDER BY variable_name DESC
        ", array(
            'locale'     => $locale,
            'table_name' => 'sor_settings',
        ));
    }

    private function get_article_categories(){
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';
        return Db::getInstance()->query("SELECT * FROM sor_settings 
            LEFT OUTER JOIN translations ON sor_settings.setting_id = translations.table_id 
            WHERE locale=:locale AND translations.table_name=:table_name AND variable_name LIKE '%article_category_%'
        ", array(
            'locale'     => $locale,
            'table_name' => 'sor_settings',
        ));
    }

    private function get_article_tags(){
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';
        return Db::getInstance()->query("SELECT * FROM sor_settings 
            LEFT OUTER JOIN translations ON sor_settings.setting_id = translations.table_id 
            WHERE locale=:locale AND translations.table_name=:table_name AND variable_name LIKE '%article_tag_%'
        ", array(
            'locale'     => $locale,
            'table_name' => 'sor_settings',
        ));
    }

    private function get_categories() {
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';
        return Db::getInstance()->query("SELECT * FROM sor_settings 
            LEFT OUTER JOIN translations ON sor_settings.setting_id = translations.table_id 
            WHERE locale=:locale AND translations.table_name=:table_name AND variable_name LIKE '%article_category_%' 
            ORDER BY variable_name
        ", array(
            'locale'     => $locale,
            'table_name' => 'sor_settings',
        ));
    }



    private function get_academy_ordered_by_id_desc() {return Db::getInstance()->query("SELECT * FROM academies ORDER BY academy_id DESC");}
    private function get_contact_ordered_by_id_desc() {return Db::getInstance()->query("SELECT * FROM sor_contacts WHERE type='contact' ORDER BY contact_id DESC");}
    private function get_user_ordered_by_id_desc() {return Db::getInstance()->query("SELECT * FROM users ORDER BY user_id DESC");}
    private function get_comments_ordered_by_id_desc() {return Db::getInstance()->query("SELECT * FROM sor_contacts WHERE type='comment' ORDER BY contact_id DESC");}

    private function get_users_by_id(int $user_id) {return Db::getInstance()->query("SELECT * FROM users WHERE user_id=:user_id", array('user_id' => $user_id));}
    private function get_users() {return Db::getInstance()->query("SELECT * FROM users ORDER BY user_id DESC");}


    private function get_other_users() {return Db::getInstance()->query("SELECT * FROM sor_academy_users ORDER BY academy_users_id DESC");}
    private function static_get_other_users() {return Db::getInstance()->query("SELECT * FROM sor_academy_users ORDER BY academy_users_id DESC");}




    private function get_lessons() {return Db::getInstance()->query("SELECT * FROM sor_lessons");}
    private function get_academies() {return Db::getInstance()->query("SELECT * FROM sor_academy");}
    // private function get_teachers_of_academy($academy_id) {
    //     return Db::getInstance()->query("SELECT * FROM users WHERE 
    //         academy_id=:academy_id AND academy_job=:academy_job1 OR 
    //         academy_id=:academy_id AND academy_job=:academy_job2 OR 
    //         academy_id=:academy_id AND academy_job=:academy_job3 
    //         ORDER BY user_id DESC
    //         ", array(
    //             'academy_id' => $academy_id, 
    //             'academy_job1' => '|teacher|', 
    //             'academy_job2' => '|manager|', 
    //             'academy_job3' => '|receptor|'
    //         )
    //     );
    // }
    private function get_teachers_of_academy() {
        return Db::getInstance()->query("SELECT * FROM users");
    }
    
    private function get_users_of_academy() {return Db::getInstance()->query("SELECT * FROM users ORDER BY user_id DESC");}
    private function get_students_of_academy() {return Db::getInstance()->query("SELECT * FROM users ORDER BY user_id DESC");}
    private function get_classes_of_academy(int $academy_id) {return Db::getInstance()->query("SELECT * FROM sor_courses WHERE academy_id=:academy_id ORDER BY course_id DESC", array('academy_id' => $academy_id));}
    private function get_schedules_of_academy(int $academy_id) {return Db::getInstance()->query("SELECT * FROM sor_schedules WHERE academy_id=:academy_id ORDER BY schedule_id DESC", array('academy_id' => $academy_id));}

    private function get_all_contact_messages(int $id) {
        return Db::getInstance()->query("SELECT * FROM sor_contacts 
            WHERE user_id=:user_id OR receiver_user_id=:receiver_user_id 
            ORDER BY contact_id DESC", 
        array(
            'user_id' => $id,
            'receiver_user_id' => $id,
        ));
    }

    private function get_no_response_contact_messages(int $id) {
        return Db::getInstance()->query("SELECT * FROM sor_contacts 
            WHERE user_id=:user_id AND parent = 0
            OR receiver_user_id=:receiver_user_id AND parent = 0
            ORDER BY contact_id DESC
        ", array(
            'user_id' => $id,
            'receiver_user_id' => $id,
        ));
    }




// ==========================================================
// ==========================================================
// ==========================================================
// ==========================================================

    public static function get_all_currency(){
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';

        return Db::getInstance()->query("SELECT * FROM financial_system_currency 
            LEFT OUTER JOIN translations ON financial_system_currency.currency_id = translations.table_id 
            WHERE translations.table_name=:table_name AND locale=:locale", 
        array(
            'table_name' => 'financial_system_currency',
            'locale' => $locale,
        ));
    }

    public static function get_academic_levels(){
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';

        return Db::getInstance()->query("SELECT * FROM translations 
            LEFT OUTER JOIN levels ON levels.level_id = translations.table_id 
            WHERE translations.table_name=:table_name AND type=:type AND locale=:locale", 
        array(
            'table_name' => 'levels',
            'type' => 'academic',
            'locale' => $locale,
        ));
    }

    public static function get_all_phones_by_branch_id(int $branch_id){
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';

        return Db::getInstance()->query("SELECT * FROM user_contacts 
            LEFT OUTER JOIN translations ON user_contacts.user_contact_id = translations.table_id 
            WHERE translations.table_name=:table_name AND mode=:mode AND user_id=:user_id AND locale=:locale", 
        array(
            'table_name' => 'user_contacts',
            'mode' => 'phone',
            'user_id' => self::get_user_id_by_branch_id($branch_id),
            'locale' => $locale,
        ));
    }


    public static function get_all_urls_by_branch_id(int $branch_id){
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';

        return Db::getInstance()->query("SELECT * FROM user_contacts 
            LEFT OUTER JOIN translations ON user_contacts.user_contact_id = translations.table_id 
            WHERE translations.table_name=:table_name AND mode=:mode AND user_id=:user_id AND locale=:locale", 
        array(
            'table_name' => 'user_contacts',
            'mode' => 'social',
            'user_id' => self::get_user_id_by_branch_id($branch_id),
            'locale' => $locale,
        ));
    }


    public static function get_all_addresses_by_branch_id(int $branch_id){
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';

        return Db::getInstance()->query("SELECT * FROM user_addresses 
            LEFT OUTER JOIN translations ON user_addresses.address_id = translations.table_id 
            WHERE translations.table_name=:table_name AND addresses_table_id=:addresses_table_id AND locale=:locale", 
        array(
            'table_name' => 'user_addresses',
            'addresses_table_id' => self::get_user_id_by_branch_id($branch_id),
            'locale' => $locale,
        ));
    }


    public static function get_all_members_by_branch_id(int $branch_id, string $filter = 'all'){
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';
        $where_statement = " translations.table_name='users' AND branch_id=" . $branch_id . " AND locale='" . $locale . "'";
        $role_filter = '';
        $filter_array = explode('&', $filter);
        foreach($filter_array as $role_name){
            if($role_name == 'managers'){$role_filter .= ($role_filter == '') ? $where_statement . " AND access_system_roles.role_id=7" : " OR " . $where_statement . " AND access_system_roles.role_id=7";}
            if($role_name == 'receptionists'){$role_filter .= ($role_filter == '') ? $where_statement . " AND access_system_roles.role_id=8" : " OR " . $where_statement . " AND access_system_roles.role_id=8";}
            if($role_name == 'teachers'){$role_filter .= ($role_filter == '') ? $where_statement . " AND access_system_roles.role_id=9" : " OR " . $where_statement . " AND access_system_roles.role_id=9";}
            if($role_name == 'students'){$role_filter .= ($role_filter == '') ? $where_statement . " AND access_system_roles.role_id=10" : " OR " . $where_statement . " AND access_system_roles.role_id=10";}
            elseif($role_name == 'all'){$role_filter = $where_statement;}
        }
        return Db::getInstance()->query("SELECT * FROM academy_branch_members 
            LEFT OUTER JOIN translations ON academy_branch_members.user_id = translations.table_id 
            LEFT OUTER JOIN access_system_roles ON academy_branch_members.role_id = access_system_roles.role_id 
            WHERE" . $role_filter);
    }



    public static function get_academy_roles(){
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';

        return Db::getInstance()->query("SELECT * FROM access_system_roles 
            LEFT OUTER JOIN translations ON access_system_roles.role_id = translations.table_id 
            WHERE translations.table_name=:table_name AND type=:type AND locale=:locale", 
        array(
            'table_name' => 'access_system_roles',
            'type' => 'academy',
            'locale' => $locale,
        ));
    }

    public static function get_branches_of_academy_by_user_id(){
        $branches = [];
        // dump(session_get('branches_id'));
        
        if(is_array(session_get('branches_id'))) {
            foreach(session_get('branches_id') as $branch_id) {
                $branch_user_id = Db::getInstance()->first("SELECT * FROM academy_branches 
                    WHERE branch_id=:branch_id",
                array(
                    'branch_id' => $branch_id['branch_id'],
                ))['user_id'];
                $branches[$branch_id['branch_id']] = Db::getInstance()->first("SELECT * FROM users 
                    LEFT OUTER JOIN translations ON users.user_id = translations.table_id 
                    WHERE users.user_id=:user_id AND table_name=:table_name", 
                array(
                    'user_id' => $branch_user_id,
                    'table_name' => 'users',
                ));
            }
        } else {
            $branches[session_get('branches_id')] = Db::getInstance()->first("SELECT * FROM users 
                LEFT OUTER JOIN translations ON users.user_id = translations.table_id 
                WHERE users.user_id=:user_id AND table_name=:table_name", 
            array(
                'user_id' => session_get('branches_id'),
                'table_name' => 'users',
            ));
        }
        
        return $branches;
    }

    
    public static function get_branches_of_academy(){
        $branches = [];
        foreach(session_get('branches_id') as $branch_id) {
            $branch_user_id = Db::getInstance()->first("SELECT * FROM academy_branches 
                WHERE branch_id=:branch_id",
            array(
                'branch_id' => $branch_id['branch_id'],
            ))['user_id'];
            $branches[$branch_id['branch_id']] = Db::getInstance()->first("SELECT * FROM users 
                LEFT OUTER JOIN translations ON users.user_id = translations.table_id 
                WHERE users.user_id=:user_id AND table_name=:table_name", 
            array(
                'user_id' => $branch_user_id,
                'table_name' => 'users',
            ));
        }
        return $branches;
    }
    

    public static function get_classrooms_by_branch_id(int $branch_id){
        return Db::getInstance()->query("SELECT * FROM academy_branch_classrooms 
            LEFT OUTER JOIN translations ON academy_branch_classrooms.classroom_id = translations.table_id 
            WHERE academy_branch_classrooms.branch_id=:branch_id AND table_name=:table_name", 
        array(
            'branch_id' => $branch_id,
            'table_name' => 'academy_branch_classrooms',
        ));
    }


    public static function get_classroom_types_by_branch_id(int $branch_id){
        return Db::getInstance()->query("SELECT * FROM academy_branch_classroom_types 
            LEFT OUTER JOIN translations ON academy_branch_classroom_types.classroom_type_id = translations.table_id 
            WHERE academy_branch_classroom_types.branch_id=:branch_id AND table_name=:table_name", 
        array(
            'branch_id' => $branch_id,
            'table_name' => 'academy_branch_classroom_types',
        ));
    }



    public static function get_all_academy_branch_course_term_invoices_by_branch_id(int $branch_id){
        $term_array = self::get_branches_course_terms_by_branch_id($branch_id);
        $invoices = [];
        foreach($term_array as $term){
            $invoices[$term['term_id']] = Db::getInstance()->query("SELECT * FROM academy_branch_course_term_invoices 
                LEFT OUTER JOIN translations ON academy_branch_course_term_invoices.term_invoice_id = translations.table_id 
                WHERE academy_branch_course_term_invoices.term_id=:term_id AND table_name=:table_name", 
            array(
                'term_id' => $term['term_id'],
                'table_name' => 'academy_branch_course_term_invoices',
            ));
        }
        return $invoices;
    }




    public static function get_classroom_assets_by_classroom_id(int $classroom_id){
        return Db::getInstance()->query("SELECT * FROM academy_branch_classroom_assets 
            LEFT OUTER JOIN translations ON academy_branch_classroom_assets.classroom_asset_id = translations.table_id 
            WHERE academy_branch_classroom_assets.classroom_id=:classroom_id AND table_name=:table_name", 
        array(
            'classroom_id' => $classroom_id,
            'table_name' => 'academy_branch_classroom_assets',
        ));
    }



    public static function get_course_terms_by_course_id(int $course_id){
        return Db::getInstance()->query("SELECT * FROM academy_branch_course_terms 
            LEFT OUTER JOIN translations ON academy_branch_course_terms.term_id = translations.table_id 
            WHERE academy_branch_course_terms.course_id=:course_id AND table_name=:table_name", 
        array(
            'course_id' => $course_id,
            'table_name' => 'academy_branch_course_terms',
        ));
    }



    public static function get_terms_enrollments_by_term_id(int $term_id){
        return Db::getInstance()->query("SELECT * FROM academy_branch_course_term_enrollments 
            LEFT OUTER JOIN translations ON academy_branch_course_term_enrollments.term_enrollment_id = translations.table_id 
            WHERE academy_branch_course_term_enrollments.term_id=:term_id AND table_name=:table_name", 
        array(
            'term_id' => $term_id,
            'table_name' => 'academy_branch_course_term_enrollments',
        ));
    }



    public static function get_terms_enrollments_students_by_term_id(int $term_id){
        return Db::getInstance()->query("SELECT * FROM academy_branch_course_term_enrollments 
            LEFT OUTER JOIN translations ON academy_branch_course_term_enrollments.term_enrollment_id = translations.table_id 
            WHERE term_id=:term_id AND type=:type AND table_name=:table_name", 
        array(
            'term_id' => $term_id,
            'type' => 'student',
            'table_name' => 'academy_branch_course_term_enrollments',
        ));
    }


    // ترتیب نوشتن شرط ها مهم هست که برای چه جدولی باشد
    // اول باید شروط جدول اصلی را نوشت
    public static function get_terms_enrollments_teachers_by_term_id(int $term_id){
        return Db::getInstance()->query("SELECT * FROM academy_branch_course_term_enrollments 
            LEFT OUTER JOIN translations ON academy_branch_course_term_enrollments.term_enrollment_id = translations.table_id 
            WHERE term_id=:term_id AND type=:type AND table_name=:table_name", 
        array(
            'term_id' => $term_id,
            'type' => 'teacher',
            'table_name' => 'academy_branch_course_term_enrollments',
        ));
    }


    public static function get_terms_waiting_list_by_term_id(int $term_id){
        return Db::getInstance()->query("SELECT * FROM academy_branch_course_term_waiting_list 
            LEFT OUTER JOIN translations ON academy_branch_course_term_waiting_list.term_waiting_list_id = translations.table_id 
            WHERE academy_branch_course_term_waiting_list.term_id=:term_id AND table_name=:table_name", 
        array(
            'term_id' => $term_id,
            'table_name' => 'academy_branch_course_term_waiting_list',
        ));
    }




    public static function get_all_courses_by_branch_id(int $branch_id){
        return Db::getInstance()->query("SELECT * FROM academy_branch_courses 
            LEFT OUTER JOIN translations ON academy_branch_courses.course_id = translations.table_id 
            WHERE academy_branch_courses.branch_id=:branch_id AND table_name=:table_name",
        array(
            'branch_id' => $branch_id,
            'table_name' => 'academy_branch_courses',
        ));
    }



    public static function get_all_user_experiences_by_user_id(int $user_id){
        return Db::getInstance()->query("SELECT * FROM translations 
            LEFT OUTER JOIN user_experiences ON user_experiences.user_experience_id = translations.table_id 
            WHERE user_experiences.user_id=:user_id AND table_name=:table_name AND user_experiences.deleted_by IS NULL",
        array(
            'user_id' => $user_id,
            'table_name' => 'user_experiences',
        ));
    }


    public static function get_all_user_events_by_user_id(int $user_id){
        return Db::getInstance()->query("SELECT * FROM translations 
            LEFT OUTER JOIN user_events ON user_events.user_event_id = translations.table_id 
            WHERE user_events.user_id=:user_id AND table_name=:table_name AND user_events.deleted_by IS NULL",
        array(
            'user_id' => $user_id,
            'table_name' => 'user_events',
        ));
    }


    public static function get_all_user_educations_by_user_id(int $user_id){
        return Db::getInstance()->query("SELECT * FROM translations 
            LEFT OUTER JOIN user_educations ON user_educations.user_education_id = translations.table_id 
            WHERE user_educations.user_id=:user_id AND table_name=:table_name AND user_educations.deleted_by IS NULL",
        array(
            'user_id' => $user_id,
            'table_name' => 'user_educations',
        ));
    }


    public static function get_all_user_awards_by_user_id(int $user_id){
        return Db::getInstance()->query("SELECT * FROM translations 
            LEFT OUTER JOIN user_awards ON user_awards.user_award_id = translations.table_id 
            WHERE user_awards.user_id=:user_id AND table_name=:table_name AND user_awards.deleted_by IS NULL",
        array(
            'user_id' => $user_id,
            'table_name' => 'user_awards',
        ));
    }


    public static function get_all_user_certificates_by_user_id(int $user_id){
        return Db::getInstance()->query("SELECT * FROM translations 
            LEFT OUTER JOIN user_certificates ON user_certificates.user_certificate_id = translations.table_id 
            WHERE user_certificates.user_id=:user_id AND table_name=:table_name AND user_certificates.deleted_by IS NULL",
        array(
            'user_id' => $user_id,
            'table_name' => 'user_certificates',
        ));
    }


    public static function get_all_user_instruments_by_user_id(int $user_id){
        return Db::getInstance()->query("SELECT * FROM translations 
            LEFT OUTER JOIN user_instruments ON user_instruments.user_instrument_id = translations.table_id 
            WHERE user_instruments.user_id=:user_id AND table_name=:table_name AND user_instruments.deleted_by IS NULL",
        array(
            'user_id' => $user_id,
            'table_name' => 'user_instruments',
        ));
    }


    public static function get_all_user_lessons_by_user_id(int $user_id){
        return Db::getInstance()->query("SELECT * FROM translations 
            LEFT OUTER JOIN user_lessons ON user_lessons.user_lesson_id = translations.table_id 
            WHERE user_lessons.user_id=:user_id AND table_name=:table_name AND user_lessons.deleted_by IS NULL",
        array(
            'user_id' => $user_id,
            'table_name' => 'user_lessons',
        ));
    }


    public static function get_all_media_files_by_user_id(int $user_id){
        return Db::getInstance()->query("SELECT * FROM translations 
            LEFT OUTER JOIN media_files ON media_files.media_file_id = translations.table_id 
            WHERE media_files.user_id=:user_id AND table_name=:table_name AND media_files.deleted_by IS NULL",
        array(
            'user_id' => $user_id,
            'table_name' => 'media_files',
        ));
    }


    public static function get_all_user_polls_by_user_id(int $user_id){
        return Db::getInstance()->query("SELECT * FROM translations 
            LEFT OUTER JOIN user_polls ON user_polls.user_poll_id = translations.table_id 
            WHERE user_polls.owner_id=:owner_id AND table_name=:table_name AND user_polls.deleted_by IS NULL",
        array(
            'owner_id' => $user_id,
            'table_name' => 'user_polls',
        ));
    }


    public static function get_user_poll_by_poll_id(int $user_poll_id){
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';

        return Db::getInstance()->query("SELECT * FROM translations 
            LEFT OUTER JOIN user_polls ON user_polls.user_poll_id = translations.table_id 
            WHERE user_polls.user_poll_id=:user_poll_id AND table_name=:table_name AND locale=:locale",
        array(
            'user_poll_id' => $user_poll_id,
            'table_name' => 'user_polls',
            'locale' => $locale,
        ));
    }


    public static function get_user_lesson_by_lesson_id(int $user_lesson_id){
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';

        return Db::getInstance()->query("SELECT * FROM translations 
            LEFT OUTER JOIN user_lessons ON user_lessons.user_lesson_id = translations.table_id 
            WHERE user_lessons.user_lesson_id=:user_lesson_id AND table_name=:table_name AND locale=:locale",
        array(
            'user_lesson_id' => $user_lesson_id,
            'table_name' => 'user_lessons',
            'locale' => $locale,
        ));
    }

    public static function get_user_instrument_by_instrument_id(int $user_instrument_id){
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';

        return Db::getInstance()->query("SELECT * FROM translations 
            LEFT OUTER JOIN user_instruments ON user_instruments.user_instrument_id = translations.table_id 
            WHERE user_instruments.user_instrument_id=:user_instrument_id AND table_name=:table_name AND locale=:locale",
        array(
            'user_instrument_id' => $user_instrument_id,
            'table_name' => 'user_instruments',
            'locale' => $locale,
        ));
    }


    public static function get_term_invoice_by_id(int $invoice_id){
        return Db::getInstance()->query("SELECT * FROM academy_branch_course_term_invoices 
            LEFT OUTER JOIN translations ON academy_branch_course_term_invoices.term_invoice_id = translations.table_id 
            WHERE academy_branch_course_term_invoices.term_invoice_id=:term_invoice_id AND table_name=:table_name",
        array(
            'term_invoice_id' => $invoice_id,
            'table_name' => 'academy_branch_course_term_invoices',
        ));
    }

    public static function get_all_academy_branch_course_term_invoice_installments_by_invoice_id(int $invoice_id){
        return Db::getInstance()->query("SELECT * FROM academy_branch_course_term_invoice_installments 
            LEFT OUTER JOIN translations ON academy_branch_course_term_invoice_installments.term_invoice_installment_id = translations.table_id 
            WHERE academy_branch_course_term_invoice_installments.invoice_id=:invoice_id AND table_name=:table_name",
        array(
            'invoice_id' => $invoice_id,
            'table_name' => 'academy_branch_course_term_invoice_installments',
        ));
    }





    // public static function get_user_poll_by_poll_id(int $poll_id){
    //     return Db::getInstance()->query("SELECT * FROM user_polls 
    //         LEFT OUTER JOIN translations ON user_polls.user_poll_id = translations.table_id 
    //         WHERE user_polls.user_poll_id=:poll_id AND table_name=:table_name",
    //     array(
    //         'poll_id' => $poll_id,
    //         'table_name' => 'user_polls',
    //     ));
    // }


    public static function get_all_user_poll_options_by_poll_id(int $poll_id){
        return Db::getInstance()->query("SELECT * FROM user_poll_options 
            LEFT OUTER JOIN translations ON user_poll_options.user_poll_option_id = translations.table_id 
            WHERE user_poll_options.poll_id=:poll_id AND table_name=:table_name
            ORDER BY user_poll_options.sort_order ASC",
        array(
            'poll_id' => $poll_id,
            'table_name' => 'user_poll_options',
        ));
    }


    public static function get_votes_count_of_user_polls_by_poll_id(int $poll_id){
        return Db::getInstance()->first("SELECT user_poll_id, votes_count FROM user_polls 
            WHERE user_polls.user_poll_id=:user_poll_id",
        array(
            'user_poll_id' => $poll_id,
        ));
    }

    public static function get_votes_count_of_user_poll_options_by_poll_id(int $poll_id){
        return Db::getInstance()->first("SELECT user_poll_option_id, votes_count FROM user_poll_options 
            WHERE user_poll_options.user_poll_option_id=:poll_id",
        array(
            'poll_id' => $poll_id,
        ));
    }


    public static function get_all_user_poll_votes_by_poll_id(int $poll_id){
        return Db::getInstance()->query("SELECT * FROM user_poll_votes 
            LEFT OUTER JOIN translations ON user_poll_votes.user_poll_vote_id = translations.table_id 
            WHERE user_poll_votes.poll_id=:poll_id AND table_name=:table_name",
        array(
            'poll_id' => $poll_id,
            'table_name' => 'user_poll_votes',
        ));
    }


    public static function get_academy_branch_course_term_session_by_id(int $session_id){
        return Db::getInstance()->query("SELECT * FROM academy_branch_course_term_sessions 
            LEFT OUTER JOIN translations ON academy_branch_course_term_sessions.term_session_id = translations.table_id 
            WHERE academy_branch_course_term_sessions.term_session_id=:term_session_id AND table_name=:table_name",
        array(
            'term_session_id' => $session_id,
            'table_name' => 'academy_branch_course_term_sessions',
        ));
    }


    public static function get_academy_branch_course_term_session_changes_by_session_id(int $session_id){
        return Db::getInstance()->query("SELECT * FROM academy_branch_course_term_session_changes 
            LEFT OUTER JOIN translations ON academy_branch_course_term_session_changes.term_session_change_id = translations.table_id 
            WHERE academy_branch_course_term_session_changes.session_id=:session_id AND table_name=:table_name",
        array(
            'session_id' => $session_id,
            'table_name' => 'academy_branch_course_term_session_changes',
        ));
    }


    public static function get_academy_branch_course_term_session_classrooms_by_session_id(int $session_id){
        return Db::getInstance()->query("SELECT * FROM academy_branch_course_term_session_classrooms 
            LEFT OUTER JOIN translations ON academy_branch_course_term_session_classrooms.term_session_classroom_id = translations.table_id 
            WHERE academy_branch_course_term_session_classrooms.session_id=:session_id AND table_name=:table_name",
        array(
            'session_id' => $session_id,
            'table_name' => 'academy_branch_course_term_session_classrooms',
        ));
    }


    public static function get_academy_branch_course_term_session_attendances_by_session_id(int $session_id){
        return Db::getInstance()->query("SELECT * FROM academy_branch_course_term_session_attendances 
            LEFT OUTER JOIN translations ON academy_branch_course_term_session_attendances.session_attendance_id = translations.table_id 
            WHERE academy_branch_course_term_session_attendances.session_id=:session_id AND table_name=:table_name",
        array(
            'session_id' => $session_id,
            'table_name' => 'academy_branch_course_term_session_attendances',
        ));
    }

    
    public static function get_all_members_of_term_session_by_session_id(int $session_id){
        $term_id = Db::getInstance()->first("SELECT term_id FROM academy_branch_course_term_sessions WHERE term_session_id=:term_session_id", array('term_session_id' => $session_id))['term_id'];
        return Db::getInstance()->query("SELECT * FROM academy_branch_members 
            LEFT OUTER JOIN translations ON academy_branch_members.member_id = translations.table_id 
            LEFT OUTER JOIN academy_branch_course_term_enrollments ON academy_branch_members.member_id = academy_branch_course_term_enrollments.member_id 
            WHERE academy_branch_course_term_enrollments.term_id=:term_id AND table_name=:table_name",
        array(
            'term_id' => $term_id,
            'table_name' => 'users',
        ));
    }


// =====================================================
// =====================================================
// =====================================================
// =====================================================
// =====================================================
// =====================================================
// =====================================================




    public static function get_all_academy_branch_types(){
        return Db::getInstance()->query("SELECT * FROM academy_branch_types 
            LEFT OUTER JOIN translations ON academy_branch_types.academy_branch_type_id = translations.table_id 
            WHERE table_name=:table_name", 
        array(
            'table_name' => 'academy_branch_types',
        ));
    }



    public static function get_all_user_availabilities(){
        return Db::getInstance()->query("SELECT * FROM user_availabilities 
            LEFT OUTER JOIN translations ON user_availabilities.user_availability_id = translations.table_id 
            WHERE table_name=:table_name", 
        array(
            'table_name' => 'user_availabilities',
        ));
    }


    public static function get_users_exceptions(){
        return Db::getInstance()->query("SELECT * FROM user_availability_exceptions 
            LEFT OUTER JOIN translations ON user_availability_exceptions.user_availability_exception_id = translations.table_id 
            WHERE table_name=:table_name", 
        array(
            'table_name' => 'user_availability_exceptions',
        ));
    }

    public static function get_branches_users_data_by_branch_id(int $branch_id){
        return Db::getInstance()->query("SELECT * FROM users 
            LEFT OUTER JOIN academy_branch_members ON users.user_id = academy_branch_members.user_id 
            LEFT OUTER JOIN translations ON users.user_id = translations.table_id 
            WHERE table_name=:table_name AND branch_id=:branch_id", 
        array(
            'table_name' => 'users',
            'branch_id' => $branch_id,
        ));
    }


    public static function get_branches_course_terms_by_branch_id(int $branch_id){
        return Db::getInstance()->query("SELECT * FROM academy_branch_course_terms 
            LEFT OUTER JOIN academy_branch_courses ON academy_branch_course_terms.course_id = academy_branch_courses.course_id 
            LEFT OUTER JOIN translations ON academy_branch_courses.course_id = translations.table_id 
            WHERE academy_branch_courses.branch_id=:branch_id AND table_name=:table_name", 
        array(
            'branch_id' => $branch_id,
            'table_name' => 'academy_branch_course_terms',
        ));
    }


    public static function get_branches_terms_sessions_by_branch_id(int $branch_id){
        return Db::getInstance()->query("SELECT * FROM academy_branch_course_term_sessions 
            LEFT OUTER JOIN academy_branch_course_terms ON academy_branch_course_term_sessions.term_id = academy_branch_course_terms.term_id 
            LEFT OUTER JOIN academy_branch_courses ON academy_branch_courses.course_id = academy_branch_course_terms.course_id 
            LEFT OUTER JOIN translations ON academy_branch_course_term_sessions.term_session_id = translations.table_id 
            WHERE academy_branch_courses.branch_id=:branch_id AND table_name=:table_name", 
        array(
            'branch_id' => $branch_id,
            'table_name' => 'academy_branch_course_term_sessions',
        ));
    }


    public static function get_rows_of_user_table() {return Db::getInstance()->query("SELECT COUNT(*) AS total_count FROM users");}
    public static function get_last_id_from_users_table() {return Db::getInstance()->query("SELECT MAX(id) AS last_id FROM users");}
    public function get_academy_id_by_user_id(int $user_id){return Db::getInstance()->first("SELECT academy_id FROM academies WHERE user_id=:user_id", array('user_id' => $user_id))['academy_id'];}
    public function get_branch_id_by_user_id(int $user_id){return Db::getInstance()->first("SELECT branch_id FROM academy_branches WHERE user_id=:user_id", array('user_id' => $user_id))['branch_id'];}
    public function get_branches_by_academy_id(int $academy_id){return Db::getInstance()->query("SELECT branch_id FROM academy_branches WHERE academy_id=:academy_id", array('academy_id' => $academy_id));}
    public function get_user_id_by_academy_idd(int $academy_id){return Db::getInstance()->first("SELECT user_id FROM academies WHERE academy_id=:academy_id", array('academy_id' => $academy_id))['user_id'];}
    public function get_user_id_by_branch_idd(int $branch_id){return Db::getInstance()->first("SELECT user_id FROM academy_branches WHERE branch_id=:branch_id", array('branch_id' => $branch_id))['user_id'];}
    public static function get_user_id_by_academy_id(int $academy_id){return Db::getInstance()->first("SELECT user_id FROM academies WHERE academy_id=:academy_id", array('academy_id' => $academy_id))['user_id'];}
    public static function get_user_id_by_branch_id(int $branch_id){return Db::getInstance()->first("SELECT user_id FROM academy_branches WHERE branch_id=:branch_id", array('branch_id' => $branch_id))['user_id'];}
    // public static function get_user_by_branch_id(int $branch_id){return Db::getInstance()->first("SELECT * FROM academy_branches WHERE branch_id=:branch_id", array('branch_id' => $branch_id));}


    private function get_iran_cities() {return Db::getInstance()->query("SELECT * FROM world_iran_cities");}
    private function get_iran_cities_filtered() {return Db::getInstance()->query("SELECT * FROM world_iran_cities_filtered");}
    private function get_iran_counties() {return Db::getInstance()->query("SELECT * FROM world_iran_counties");}
    private function get_iran_districts() {return Db::getInstance()->query("SELECT * FROM world_iran_districts");}
    private function get_iran_provinces() {return Db::getInstance()->query("SELECT * FROM world_iran_provinces");}
    private function get_iran_rurals() {return Db::getInstance()->query("SELECT * FROM world_iran_rurals");}












}