<?php

trait ModelAccountTrait {


  public static function insert_new_phone_type_users(string $phone, string $username, string $password, string $register_method){
    return Db::getInstance()->insert("INSERT INTO users (phone, username, password, register_method) VALUES (:phone, :username, :password, :register_method)", array(
      'phone' => $phone,
      'username' => $username,
      'password' => $password,
      'register_method' => $register_method,
      // 'last_visit_time' => $time,
    ));
  }

  public static function insert_new_email_type_users(string $email, string $username, string $password, string $register_method){
    return Db::getInstance()->insert("INSERT INTO users (email, username, password, register_method) VALUES (:email, :username, :password, :register_method)", array(
      'email' => $email,
      'username' => $username,
      'password' => $password,
      'register_method' => $register_method,
      // 'last_visit_time' => $time,
    ));
  }


  public static function fetch_by_email_in_users(string $email){
    return Db::getInstance()->first("SELECT * FROM users WHERE email=:email", array(
      'email' => $email,
    ));
  }

  public static function fetch_by_phone_in_users(string $phone){
    return Db::getInstance()->first("SELECT * FROM users WHERE phone=:phone", array(
      'phone' => $phone,
    ));
  }


  public static function get_fail_message(string $fail_message){
    global $config;
    $locale = $config['app']['lang'] ?? 'fa';

    return Db::getInstance()->first("SELECT * FROM sor_settings 
        LEFT OUTER JOIN translations ON sor_settings.setting_id = translations.table_id 
        WHERE locale=:locale AND translations.table_name=:table_name AND variable_name=:variable_name
    ", array(
        'locale'     => $locale,
        'table_name' => 'sor_settings',
        'variable_name' => $fail_message,
    ));
  }


  // -- LEFT OUTER JOIN user_roles ON users.user_id = user_roles.user_id 
// برای گرفتن نقش کاربر بعد از لاگین یا ثبت نام
    public static function fetch_role_by_user_id_in_users(int $user_id){
        return Db::getInstance()->first("SELECT users.user_id AS user_id, access_system_roles.name AS role FROM users 
          LEFT OUTER JOIN access_system_roles ON access_system_roles.role_id = users.role_id 
          WHERE users.user_id=:user_id", 
        array(
          'user_id' => $user_id,
        ));
    }





















// برای گرفتن مجوز های کاربر بعد از لاگین یا ثبت نام
    // public static function fetch_permissions_by_user_id_in_users(int $user_id){
    //     return Db::getInstance()->first("SELECT users.user_id AS user_id, access_system_roles.name AS role FROM users 
    //       LEFT OUTER JOIN user_roles ON users.user_id = user_roles.user_id 
    //       LEFT OUTER JOIN access_system_roles ON access_system_roles.role_id = user_roles.role_id 
    //       WHERE users.user_id=:user_id", 
    //     array(
    //       'user_id' => $user_id,
    //     ));
    // }

























    
// get_branch_id_by_user_id
    public static function set_academy_and_branches_id_in_session_by_user_id(int $user_id){
      $gender = Db::getInstance()->first("SELECT gender FROM users WHERE user_id=:user_id", 
        array(
          'user_id' => $user_id,
        ))['gender'];

        // echo $gender;
        // exit();
        switch($gender) {
            case 'academy' :
                $academy_id = Db::getInstance()->first("SELECT academy_id FROM academies WHERE user_id=:user_id", 
                    array(
                      'user_id' => $user_id,
                    ))['academy_id'];
                
                $branches_id = Db::getInstance()->query("SELECT branch_id FROM academy_branches WHERE academy_id=:academy_id", 
                    array(
                      'academy_id' => $academy_id,
                    ));
                break;
            case 'branch':
                $academy_id = Db::getInstance()->first("SELECT academy_id FROM academy_branches WHERE user_id=:user_id", 
                    array(
                      'user_id' => $user_id,
                    ))['academy_id'];
                
                $branches_id = Db::getInstance()->first("SELECT branch_id FROM academy_branches WHERE user_id=:user_id", 
                    array(
                      'user_id' => $user_id,
                    ))['branch_id'];
                break;
            default :
                $branches_id = Db::getInstance()->first("SELECT branch_id FROM academy_branch_members WHERE user_id=:user_id", 
                    array(
                      'user_id' => $user_id,
                    ))['branch_id'] ?? 0;
                
                $academy_id = Db::getInstance()->first("SELECT academy_id FROM academy_branches WHERE branch_id=:branch_id", 
                    array(
                      'branch_id' => $branches_id,
                    ))['academy_id'] ?? 0;
        }

        session_set('academy_id', $academy_id);
        session_set('branches_id', $branches_id);
        return;
    }


}