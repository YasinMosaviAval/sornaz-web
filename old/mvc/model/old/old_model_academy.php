<?php

trait OldModelAcademyTrait {

/*
  public static function add_user(
    $email, $username, $fullname_fa, $fullname_en, $mobile, $academy_id, $role, $academy_job, $instruments_id, $lessons_id, $start_career_date, $activity_status, $picture_type, 
    $gender, $student_level, $parent_name_fa, $parent_name_en, $parent_phone, $birthday, $academy_register_date, $national_code, $time_sheet = null
  ) {
    return Db::getInstance()->insert("INSERT INTO sor_users (email, username, fullname_fa, fullname_en, mobile, academy_id, role, academy_job, instruments_id, lessons_id, start_career_date, activity_status, picture_type, gender, student_level, parent_name_fa, parent_name_en, parent_phone, birthday, academy_register_date, national_code, time_sheet)
      VALUES (:email, :username, :fullname_fa, :fullname_en, :mobile, :academy_id, :role, :academy_job, :instruments_id, :lessons_id, :start_career_date, :activity_status, :picture_type, :gender, :student_level, :parent_name_fa, :parent_name_en, :parent_phone, :birthday, :academy_register_date, :national_code, :time_sheet)", array(
        'email' => $email,
        'username' => $username,
        'fullname_fa' => $fullname_fa,
        'fullname_en' => $fullname_en,
        'mobile' => $mobile,
        'academy_id' => $academy_id,
        'role' => $role,
        'academy_job' => $academy_job,
        'instruments_id' => $instruments_id,
        'lessons_id' => $lessons_id,
        'start_career_date' => $start_career_date,
        'activity_status' => $activity_status,
        'picture_type' => $picture_type,
        'gender' => $gender,
        'student_level' => $student_level,
        'parent_name_fa' => $parent_name_fa,
        'parent_name_en' => $parent_name_en,
        'parent_phone' => $parent_phone,
        'birthday' => $birthday,
        'academy_register_date' => $academy_register_date,
        'national_code' => $national_code,
        'time_sheet' => $time_sheet,
      )
    );
  }


  
  public static function add_class(
    $academy_id, $lesson_id, $teachers_id, $level, $capacity, $section_number, $payment_fa, $payment_en, $day, $start_time, $duration, $set_class_user_id, $description_fa, $description_en, $brief_fa, $brief_en
  ) {
    return Db::getInstance()->insert("INSERT INTO sor_classes (academy_id, lesson_id, teachers_id, level, capacity, section_number, payment_fa, payment_en, day, start_time, duration, set_class_user_id, description_fa, description_en, brief_fa, brief_en)
      VALUES (:academy_id, :lesson_id, :teachers_id, :level, :capacity, :section_number, :payment_fa, :payment_en, :day, :start_time, :duration, :set_class_user_id, :description_fa, :description_en, :brief_fa, :brief_en)", array(
        'academy_id' => $academy_id,
        'lesson_id' => $lesson_id,
        'teachers_id' => $teachers_id,
        'level' => $level,
        'capacity' => $capacity,
        'section_number' => $section_number,
        'payment_fa' => $payment_fa,
        'payment_en' => $payment_en,
        'day' => $day,
        'start_time' => $start_time,
        'duration' => $duration,
        'set_class_user_id' => $set_class_user_id,
        'description_fa' => $description_fa,
        'description_en' => $description_en,
        'brief_fa' => $brief_fa,
        'brief_en' => $brief_en,
      )
    );
  }




  public static function add_schedule(
    $academy_id, $student_id, $class_id, $description_fa, $description_en, $set_schedule_user_id
  ) {
    return Db::getInstance()->insert("INSERT INTO sor_schedule (academy_id, student_id, class_id, description_fa, description_en, set_schedule_user_id)
      VALUES (:academy_id, :student_id, :class_id, :description_fa, :description_en, :set_schedule_user_id)", array(
        'academy_id' => $academy_id,
        'student_id' => $student_id,
        'class_id' => $class_id,
        'description_fa' => $description_fa,
        'description_en' => $description_en,
        'set_schedule_user_id' => $set_schedule_user_id,
      )
    );
  }


*/



  // public static function update_post($post_id, $status, $modified){
  //   
  //   return Db::getInstance()->modify("UPDATE sor_posts SET modified=:modified, status=:status WHERE post_id=:post_id", array(
  //     'post_id' => $post_id,
  //     'modified' => $modified,
  //     'status' => $status
  //   ));
  // }


  // public static function delete_post($post_id){
  //   return Db::getInstance()->modify("DELETE FROM sor_posts WHERE post_id=:post_id", array('post_id' => $post_id));
  // }






}