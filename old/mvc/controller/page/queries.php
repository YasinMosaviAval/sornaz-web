<?php

trait PageQueriesTrait {



    public function add_new_academy() {
        $email = post('email');
        // show registration form if email not provided
        if ($email == null) {
            $this->sendAcademyRequestForm();
            return;
        }
        
        // check registration info and register if is valid information
        $username = post('username');
        $password1 = post('password1');
        $password2 = post('password2');
        $time = getCurrentDateTime();
        
        // $record = AccountModel::fetch_by_email($email);
        $record = AccountModel::fetch_by_email_in_users($email);
        if ($record != null){
            $data['message'] = AccountModel::get_fail_message('fail_message_1');
            $this->view("/page/message", "Fail Message", $data);
        }

        if (strlen($password1)<3 || strlen($password2)<3){
            $data['message'] = AccountModel::get_fail_message('fail_message_2');
            $this->view("/page/message", "Fail Message", $data);
        }

        if ($password1 != $password2){
            $data['message'] = AccountModel::get_fail_message('fail_message_3');
            $this->view("/page/message", "Fail Message", $data);
        }

        $password = encryptPassword($password1);

        global $config;
        $locale = $config['app']['lang'] ?? 'fa';
        $title = post('fullname');
        $brief = post('brief');
        $description = post('biography');
        $gender = 'academy';
        $table_name = 'users';
        $role_id = 6; // academy_owner
        
        $model = new pageModel();
        $user_id = $model->insert_new_users($email, $username, $password, $gender, $time);
        $user_role_id = $model->insert_new_user_role($user_id, $role_id);
        $model->insert_new_translation($table_name, $user_id, $locale, $title, $brief, $description);
        // $model->insert_new_user_translation($user_id, $locale, $fullname, $brief, $biography, $created_by);
        $academy_id = $model->insert_new_academy($user_id);
        
        $this->sendAcademyRequestForm();
    }


// =========================================================================
// =========================================================================
// =========================================================================

    public function send_new_message() {
        $author_email = post('email') == null ? post('author_email') : post('email');
        $author = post('author');
        $user_id = post('user_id');
        $post_id = post('post_id');
        $type = post('type');
        $content = post('content');
        $receiver_user_id = post('receiver_user_id');
        $agent = $_SERVER['HTTP_USER_AGENT'];
        
        
        /** ******** */
        $parent = post('parent');
        /** ******** */
        
        $insert_status = PageModel::add_contact($author_email, $author, $parent, $user_id, $post_id, $type, $content, $receiver_user_id, $agent);

        // $contact_status = AccountController::add_new_message($insert_status, $name, $email, $manager_id, $type);
    }

  // ============================================================================

  // public function send_new_message(): void {
  //   $model = new PageModel();

  //   $model->addContact(
  //     authorEmail:     $this->post('email') ?? $this->post('author_email', ''),
  //     author:          $this->post('author', ''),
  //     parent:          (int) $this->post('parent', 0),
  //     userId:          (int) $this->post('user_id', 0),
  //     postId:          (int) $this->post('post_id', 0),
  //     type:            $this->post('type', ''),
  //     content:         $this->post('content', ''),
  //     receiverUserId:  (int) $this->post('receiver_user_id', 0),
  //     agent:           $_SERVER['HTTP_USER_AGENT'] ?? ''
  //   );
  // }



    private function get_home_data() {
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';

        return Db::getInstance()->query("SELECT * FROM sor_settings
            LEFT OUTER JOIN translations ON sor_settings.setting_id = translations.table_id 
            WHERE locale=:locale AND translations.table_name=:table_name AND page=:page
        ", array(
            'locale'     => $locale,
            'table_name' => 'sor_settings',
            'page' => 'home'
        ));
    }

    private function get_contact_us_categories() {
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';

        return Db::getInstance()->query("SELECT * FROM sor_settings
            LEFT OUTER JOIN translations ON sor_settings.setting_id = translations.table_id 
            WHERE locale=:locale AND translations.table_name=:table_name AND page=:page
        ", array(
            'locale'     => $locale,
            'table_name' => 'sor_settings',
            'page' => 'contact_us'
        ));
    }

    private function get_about_us_content() {
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';

        return Db::getInstance()->query("SELECT * FROM sor_settings
            LEFT OUTER JOIN translations ON sor_settings.setting_id = translations.table_id 
            WHERE locale=:locale AND translations.table_name=:table_name AND page=:page
        ", array(
            'locale'     => $locale,
            'table_name' => 'sor_settings',
            'page' => 'about_us'
        ));
    }





  private function get_academy_by_id(int $academy_id) {return Db::getInstance()->query("SELECT * FROM sor_academy WHERE academy_id=:academy_id", array('academy_id' => $academy_id));}
  private function get_user_by_id(int $user_id) {return Db::getInstance()->query("SELECT * FROM sor_users WHERE user_id=:user_id", array('user_id' => $user_id));}
  
}
