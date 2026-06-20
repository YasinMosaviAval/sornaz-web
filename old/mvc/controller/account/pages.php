<?php

trait AccountPagesTrait {


    public function home() {
        $data['home'] = setIndexforDataArray($this->get_home_data(), 'variable_name');
        $this->view("/page/home", "Home", $data);
    }



    public function otpCode() {
        $data['home'] = setIndexforDataArray($this->get_home_data(), 'variable_name');
        $data['otp-code'] = '123456';
        $this->view("/page/otp-code", "OTP Code", $data);
    }



    public function logout(){
        session_destroy();
        // header("Location: " . fullBaseUrl());
        session_start();
        session_regenerate_id();
        // initializeSettings();
        $this->home();
    }



    private function go_to_login_page() {
        $this->view("/account/login", "Login");
    }




    public function login() {
        // $data['home'] = setIndexforDataArray($this->get_home_data(), 'variable_name');
        // $data['header'] = setIndexforDataArray($this->get_header_data(), 'variable_name');
        // $data['footer'] = setIndexforDataArray($this->get_footer_data(), 'variable_name');

        $email = post('email');

        if ($email == null) {
            $this->go_to_login_page();
            return;
        }

        $email_record_in_database = AccountModel::fetch_by_email_in_users($email);
        $user_id = $email_record_in_database['user_id'];
        $user_role = AccountModel::fetch_role_by_user_id_in_users($user_id);
        AccountModel::set_academy_and_branches_id_in_session_by_user_id($user_id);
        // dump($user_role);
        $this->check_validatoin_of_login_informatoin($email_record_in_database, $user_role);
        $this->home();
    }




    public function register(){
        // dump($_POST);

        $email = post('email');
        $phone = post('phone');
        $register_method = post('register_method');

        setPageTitle("Register an account");

        // show registration form if email not provided
        if ($email == null && $register_method == 'email') {
            $this->go_to_register_page();
            return;
        }

        // show registration form if email not provided
        if ($phone == null && $register_method == 'phone') {
            $this->go_to_register_page();
            return;
        }

        // show registration form if email not provided
        if ($phone == null && $email == null) {
            $this->go_to_register_page();
            return;
        }

        // check registration info and register if is valid information
        $password1 = post('password1') ?? '';
        $password2 = post('password2') ?? '';
        $username  = $register_method == 'phone' ? $phone : $email;
        // $this->sendOtpToPhone();
        
        // $record = AccountModel::fetch_by_email($email);
        $record = $register_method == 'phone' ? AccountModel::fetch_by_phone_in_users($phone) : AccountModel::fetch_by_email_in_users($email);
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

        $hashedPassword = encryptPassword($password1);

        // $sor_user_id = AccountModel::insert($email, $fullname_fa, $fullname_en, $username, $hashedPassword, $time);
        $user_id = $register_method == 'phone' ? AccountModel::insert_new_phone_type_users($phone, $username, $hashedPassword, $register_method)
                                                : AccountModel::insert_new_email_type_users($email, $username, $hashedPassword, $register_method);


        $author_email = $email ?? '-------@---.---' ;
        $author = $username ;
        $parent = 0;
        $post_id = 6;
        $receiver_user_id = 0;
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $content = "New User Registered in Sornaz Application, " . $author_email . " with User id " . $user_id;
        $type = "message";
        PageModel::add_register_contact($user_id, $author_email, $author, $post_id, $content, $parent, $receiver_user_id, $agent, $type);
        
        setPageTitle("Register Succeed");
        // message('success', 'شما با موفقیت ثبت نام شدید،<a href="' . baseUrl() . '/login">ورود به سایت</a>', "/account/login.php");
        // $record_in_database = AccountModel::fetch_by_email($email);
        // $record_in_database = AccountModel::fetch_by_email_in_users($email);
        // $this->check_validatoin_of_login_informatoin($record_in_database, true);

        if($register_method == 'phone') {
            $record_in_database = AccountModel::fetch_by_phone_in_users($phone);
            $user_id = $record_in_database['user_id'];
        } else {
            $record_in_database = AccountModel::fetch_by_email_in_users($email);
            $user_id = $record_in_database['user_id'];
        }

        $user_role = AccountModel::fetch_role_by_user_id_in_users($user_id);
        AccountModel::set_academy_and_branches_id_in_session_by_user_id($user_id);
        $this->check_validatoin_of_login_informatoin($record_in_database, $user_role);
        // $this->sendOtpToPhone();
        // $sent = Mailer::sendOtp(
        //     toEmail: $email,
        //     code:    '483920',
        //     purpose: 'register' // 'register' | 'login' | 'reset'
        // );
        // $this->otpCode();
        $this->home();
    }




    private function check_validatoin_of_login_informatoin(array $email_record_in_database, array $user_role, bool $login_after_rgister = false) {
        $password = post('password');  
        if ($email_record_in_database == null) {
            setPageTitle("Login Failure");
        } else {
            $hashedPassword = encryptPassword($password);
            if ($hashedPassword == $email_record_in_database['password'] || $login_after_rgister) {
                $this->set_user_informatoin_in_session($email_record_in_database);
                $this->set_user_informatoin_in_session($user_role);
                setPageTitle("Login Succeed");
            } else {
                setPageTitle("Wrong Password");
            }
        }
    }



    private function set_user_informatoin_in_session(array $email_record_in_database) {
        foreach($email_record_in_database as $key => $value) {
            session_set($key, $value);
        }
    }



    private function go_to_register_page() { $this->view("/account/register", "Register"); }
    public function forgotPassword(){ $this->view("/account/forgot-password", "Forgot Password"); }
    public function resetPassword(){ $this->view("/account/reset-password", "Reset Password"); }
    public function forgetPassword(){ $this->view("/account/forget-password", "Forget Password"); }

}