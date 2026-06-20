<?php

trait OldModelAccountTrait {


  public static function insert(string $email, string $fullname_fa, string $fullname_en, string $username, string $hashedPassword, string $time){
    return Db::getInstance()->insert("INSERT INTO sor_users (email, fullname_fa, fullname_en, username, password, register_time, last_visit_time) VALUES (:email, :fullname_fa, :fullname_en, :username, :password, :register_time, :last_visit_time)", array(
      'email' => $email,
      'fullname_fa' => $fullname_fa,
      'fullname_en' => $fullname_en,
      'username' => $username,
      'password' => $hashedPassword,
      'register_time' => $time,
      'last_visit_time' => $time,
    ));
  }






  public static function fetch_by_email(string $email){
    $record = Db::getInstance()->first("SELECT * FROM sor_users WHERE email=:email", array(
      'email' => $email,
    ));
    return $record;
  }



  public static function update_role_of_manager_of_academy(int $user_id, string $role, int $academy_id, string $academy_job){
    Db::getInstance()->modify("UPDATE sor_users SET role=:role, academy_id=:academy_id, academy_job=:academy_job WHERE user_id=:user_id", array(
      'user_id' => $user_id,
      'role' => $role,
      'academy_id' => $academy_id,
      'academy_job' => $academy_job,
    ));
  }




  public static function promote_user(int $userId, string $role){
    Db::getInstance()->modify("UPDATE sor_users SET role=:role WHERE user_id=:user_id", array(
      'user_id' => $userId,
      'role' => $role,
    ));
  }



  public static function get_user_role(int $userId){
    $record = Db::getInstance()->first("SELECT role FROM sor_users WHERE user_id=:user_id", array(
      'user_id' => $userId,
    ));
    return $record['role'];
  }



}