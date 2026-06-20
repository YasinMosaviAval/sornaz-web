<?php

trait AcademyQueriesTrait {




// ---------------------------------------------------------------
// ---------------------------------------------------------------
// ---------------------------------------------------------------
// ---------------------------------------------------------------



    private function get_lessons() {return Db::getInstance()->query("SELECT * FROM sor_lessons");}


    public function get_academy_data_by_id(string $academy_id){
        return Db::getInstance()->first("SELECT * FROM sor_academy WHERE academy_id=:academy_id", array(
            'academy_id' => $academy_id,
        ));
    }


    private function get_users_of_academy(string $academy_id) {
        // return Db::getInstance()->query("SELECT * FROM sor_users WHERE academy_id=:academy_id ORDER BY user_id DESC", array(
        return Db::getInstance()->query("SELECT * FROM sor_users"
        // , array(
        //     'academy_id' => $academy_id
        // )
        );
    }


    private function get_classes_of_academy(string $academy_id) {
        return Db::getInstance()->query("SELECT * FROM sor_courses WHERE academy_id=:academy_id ORDER BY course_id DESC", array(
            'academy_id' => $academy_id
        ));
    }


    private function get_schedules_of_academy(string $academy_id) {
        return Db::getInstance()->query("SELECT * FROM sor_schedules");
        //         return Db::getInstance()->query("SELECT * FROM sor_schedules WHERE academy_id=:academy_id ORDER BY schedule_id DESC", array(
        //     'academy_id' => $academy_id
        // ));
        
    }


    private function get_teachers_of_academy(string $academy_id) {
        return Db::getInstance()->query("SELECT * FROM sor_users"
        // WHERE 
        //     academy_id=:academy_id AND academy_job=:academy_job1 OR 
        //     academy_id=:academy_id AND academy_job=:academy_job2 OR 
        //     academy_id=:academy_id AND academy_job=:academy_job3 
        //     ORDER BY user_id DESC
        //     "
            // , array(
            //     'academy_id' => $academy_id, 
            //     'academy_job1' => '|teacher|', 
            //     'academy_job2' => '|manager|', 
            //     'academy_job3' => '|receptor|'
            // )
        );
    }


    private function get_students_of_academy(string $academy_id) {
        return Db::getInstance()->query("SELECT * FROM sor_users WHERE academy_id=:academy_id AND academy_job=:academy_job ORDER BY user_id DESC", array(
            'academy_id' => $academy_id,
            'academy_job' => '|student|'
        ));
    }


// ---------------------------------------------------------------
// ---------------------------------------------------------------
// ---------------------------------------------------------------
// ---------------------------------------------------------------

/*
    public function register_academy_user(){
        // dump($_POST);
        // exit();


        $email = post('email');
        
        $record = AccountModel::fetch_by_email($email);
        if ($record != null){
            $this->staticAcademyTeachers();
            return;
        }
        $job = explode('-', post('role'))[1];

        $username = post('username');
        $fullname_fa = post('fullname_fa');
        $fullname_en = post('fullname_en');
        $mobile = post('mobile');
        $academy_id = post('academy_id');
        $role = '|' . explode('-', post('role'))[0] . '|';
        $academy_job = '|' . $job . '|';
        $instruments_id = post('instruments_id');
        $lessons_id = post('lessons_id');
        $start_career_date = post('start_career_date');
        $activity_status = post('activity_status');
        // $picture_type = $_FILES['picture_type']['name'];
        
        $picture_type = $_FILES['picture_type']['name'] == '' ? null : explode('.', $_FILES['picture_type']['name'])[1];
        // $picture_type = substr($_FILES['picture_type']['name'], strlen($_FILES['picture_type']['name']) - 3);
        
        $gender = post('gender');
        $student_level = post('student_level');
        $parent_name_fa = post('parent_name_fa');
        $parent_name_en = post('parent_name_en');
        $parent_phone = post('parent_phone');
        $birthday = post('birthday');
        $academy_register_date = post('academy_register_date');
        $national_code = post('national_code');

        $time_sheet = '';
        foreach($_POST as $key => $value){
            if(strhas($key, 'time_sheet')) {
                $time_sheet = $value . ',' . $time_sheet;
            }
        }

        // echo $time_sheet;
        // exit();


        $new_user_id = AcademyModel::add_user($email, $username, $fullname_fa, $fullname_en, $mobile, $academy_id, $role, $academy_job, $instruments_id, $lessons_id, $start_career_date, $activity_status, $picture_type, $gender, $student_level, $parent_name_fa, $parent_name_en, $parent_phone, $birthday, $academy_register_date, $national_code, $time_sheet);
        

        $user_id = session_get('user_id');
        $author_email = $email ;
        $author = $username ;
        $post_id = 6;
        $parent = 0;
        $type = 'message';

        $agent = $_SERVER['HTTP_USER_AGENT'];
        
        $academy_data = $this->get_academy_data_by_id($academy_id);
        $academy_name = $academy_data['name'];
        $receiver_user_id = $academy_data['manager_id'];
        $content = "New User Registered in Sornaz Application, " . $author_email . " with User id " . $new_user_id . " by " . session_get('username') . " with User id " . $user_id . " as " . $job . " for " . $academy_name . " with Academy id " . $academy_id;
        PageModel::add_register_contact($user_id, $author_email, $author, $post_id, $content, $parent, $receiver_user_id, $agent, $type);
        

        // setPageTitle("New User Register Succeed");
        $this->staticAcademyTeachers();
    }


    public function define_new_class(){
        // dump($_POST);
        // exit();

        $academy_id = post('academy_id');
        $lesson_id = post('lesson_id');
        $teachers_id = post('teachers_id');
        $level = post('level');
        $capacity = post('capacity');
        $section_number = post('section_number');
        $payment_fa = post('payment_fa');
        $payment_en = post('payment_en');
        $day = post('day');
        $start_time = post('start_time');
        $duration = post('duration');
        $set_class_user_id = post('set_class_user_id');
        $description_fa = post('description_fa');
        $description_en = post('description_en');

        $teacher = Db::getInstance()->query("SELECT * FROM sor_users WHERE user_id=:user_id", array('user_id' => $teachers_id));
        $lesson = Db::getInstance()->query("SELECT * FROM sor_lessons WHERE lesson_id=:lesson_id", array('lesson_id' => $lesson_id));



        $dayOfWeek = Db::getInstance()->query("SELECT * FROM sor_settings WHERE variable_name LIKE '%weekly_day_%' AND value=:value", array('value' => $day));
        $hourOfDay = Db::getInstance()->query("SELECT * FROM sor_settings WHERE variable_name LIKE '%daily_hour_%' AND value=:value", array('value' => $start_time));




        $brief_fa = $teacher[0]['fullname_fa'] . ' - ' . $lesson[0]['name_fa'] . ' - ' . $dayOfWeek[0]['text_fa'] . ' - ' . $hourOfDay[0]['text_fa'];
        $brief_en = $teacher[0]['fullname_en'] . ' - ' . $lesson[0]['name_en'] . ' - ' . $dayOfWeek[0]['text_en'] . ' - ' . $hourOfDay[0]['text_en'];


        AcademyModel::add_class($academy_id, $lesson_id, $teachers_id, $level, $capacity, $section_number, $payment_fa, $payment_en, $day, $start_time, $duration, $set_class_user_id, $description_fa, $description_en, $brief_fa, $brief_en);
        
        $user_id = session_get('user_id');
        $post_id = 7;
        $type = 'message';
        $content = 'Define new Class for ' . $brief_en;

        $academy_data = $this->get_academy_data_by_id($academy_id);
        // $academy_name = $academy_data['name'];
        $receiver_user_id = $academy_data['manager_id'];
        $agent = $_SERVER['HTTP_USER_AGENT'];

        $author_email = $teacher[0]['email'];
        $author = $teacher[0]['username'];
        $parent = 0;
        $contact_id = PageModel::add_contact($author_email, $author, $parent, $user_id, $post_id, $type, $content, $receiver_user_id, $agent);
        // setPageTitle("New User Register Succeed");
        $this->staticAcademyDefineClasses();
    }


    public function define_new_schedule(){
        // dump($_POST);
        // exit();

        $academy_id = post('academy_id');
        $student_id = post('student_id');
        $course_id = post('course_id');
        $description_fa = post('description_fa');
        $description_en = post('description_en');
        $set_schedule_user_id = post('set_schedule_user_id');

        AcademyModel::add_schedule($academy_id, $student_id, $course_id, $description_fa, $description_en, $set_schedule_user_id);
        
        // setPageTitle("New User Register Succeed");
        $this->staticAcademyDefineSchedule();
    }
*/




}