<?php

trait OldModelAdminTrait {


  public function add_setting(string $page, string $variable_name, string $table_name, string $value, string $url, string $source, string $status, string $icon){
    $db = Db::getInstance();
    return $db->insert("INSERT INTO sor_settings (page, variable_name, table_name, value, url, source, status, icon, created_by, updated_by)
      VALUES (:page, :variable_name, :table_name, :value, :url, :source, :status, :icon, :created_by, :updated_by)
    ", array(
      'page'          => $page,
      'variable_name' => $variable_name,
      'table_name'    => $table_name,
      'value'         => $value,
      'url'           => $url,
      'source'        => $source,
      'status'        => $status,
      'icon'          => $icon,
      'created_by'   => session_get('user_id'),
      'updated_by'    => session_get('user_id'),
    ));
  }



  public static function add_post($post_id, $author_id, 
    $title_fa, $title_en, $categories, $tags, $keywords, $cover, $date, $type, $status, 
    $password, $comment_count, $name, $pinged, $guid, $related_posts_id, $excerpt_fa, $excerpt_en, $content_fa, $content_en
  ){
    $db = Db::getInstance();
    $db->insert("INSERT INTO sor_posts (
      author_id, title_fa, title_en, categories, tags, keywords, cover, date, type, 
      status, password, comment_count, name, pinged, guid, related_posts_id, excerpt_fa, excerpt_en, content_fa, content_en
    )
    VALUES (
      :author_id, :title_fa, :title_en, :categories, :tags, :keywords, :cover, :date, :type,
      :status, :password, :comment_count, :name, :pinged, :guid, :related_posts_id, :excerpt_fa, :excerpt_en, :content_fa, :content_en
    )", array(
      // 'post_id' => $post_id,
      'author_id' => $author_id,
      'title_fa' => $title_fa,
      'title_en' => $title_en,
      'categories' => $categories,
      'tags' => $tags,
      'keywords' => $keywords,
      'cover' => $cover,
      'date' => $date,
      // 'modified' => $modified,
      'type' => $type,
      'status' => $status,
      'password' => $password,
      'comment_count' => $comment_count,
      'name' => $name,
      'pinged' => $pinged,
      'guid' => $guid,
      'related_posts_id' => $related_posts_id,
      'excerpt_fa' => $excerpt_fa,
      'excerpt_en' => $excerpt_en,
      'content_fa' => $content_fa,
      'content_en' => $content_en,
    ));
  }



  public static function update_post($post_id, $status, $modified){
    $db = Db::getInstance();
    $db->modify("UPDATE sor_posts SET modified=:modified, status=:status WHERE post_id=:post_id", array(
      'post_id' => $post_id,
      'modified' => $modified,
      'status' => $status
    ));
  }



  public static function delete_post($post_id){
    return Db::getInstance()->modify("DELETE FROM sor_posts WHERE post_id=:post_id", array('post_id' => $post_id));
  }



  public static function delete_user($user_id){
    return Db::getInstance()->modify("DELETE FROM sor_users WHERE user_id=:user_id", array('user_id' => $user_id));
  }



  public static function add_user($email, $username, $fullname_fa, $fullname_en, $mobile, $role, $academy_job, $activity_status, $picture_type, $gender, $birthday, $national_code, $active_user_id) {
    $db = Db::getInstance();
    return $db->insert("INSERT INTO sor_users (email, username, fullname_fa, fullname_en, mobile, role, academy_job, activity_status, picture_type, gender, birthday, national_code, active_user_id)
      VALUES (:email, :username, :fullname_fa, :fullname_en, :mobile, :role, :academy_job, :activity_status, :picture_type, :gender, :birthday, :national_code, :active_user_id)", array(
        'email' => $email,
        'username' => $username,
        'fullname_fa' => $fullname_fa,
        'fullname_en' => $fullname_en,
        'mobile' => $mobile,
        'role' => $role,
        'academy_job' => $academy_job,
        'activity_status' => $activity_status,
        'picture_type' => $picture_type,
        'gender' => $gender,
        'birthday' => $birthday,
        'national_code' => $national_code,
        'active_user_id' => $active_user_id,
      ));
  }



  public static function update_user(
    $user_id, $email, $username, $fullname_fa, $fullname_en, $mobile, $academy_id, $role, $academy_job, $instruments_id, $lessons_id, $start_career_date, $activity_status, $picture_type, 
    $gender, $student_level, $parent_name_fa, $parent_name_en, $parent_phone, $birthday, $academy_register_date, $national_code, $biography_fa, $biography_en, $time_sheet
  ){
      $db = Db::getInstance();
      return $db->modify("UPDATE sor_users SET email=:email, username=:username, fullname_fa=:fullname_fa, fullname_en=:fullname_en, mobile=:mobile, academy_id=:academy_id, 
        role=:role, academy_job=:academy_job, instruments_id=:instruments_id, lessons_id=:lessons_id, start_career_date=:start_career_date, 
        activity_status=:activity_status, picture_type=:picture_type, gender=:gender, student_level=:student_level, parent_name_fa=:parent_name_fa, 
        parent_name_en=:parent_name_en, parent_phone=:parent_phone, birthday=:birthday, academy_register_date=:academy_register_date, 
        national_code=:national_code, biography_fa=:biography_fa, biography_en=:biography_en, time_sheet=:time_sheet WHERE user_id=:user_id", 
      array(
        'user_id' => $user_id,
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
        'biography_fa' => $biography_fa,
        'biography_en' => $biography_en,
        'time_sheet' => $time_sheet,
    ));
  }


}