<?php

trait AcademyPagesTrait {


    // public function home() {
        // $data['home'] = setVariableNameforDataArray($this->get_home_data());
    //     $this->view("/page/home", "Home",);
    // }




    // public function academyCategory() {$this->view("/academy/category", "Academy Category");}


        
    public function academyUsers() {
        $data['other_users'] = $this->get_teachers_of_academy(1);
        // $data['other_users'] = $this->get_other_users(session_get('academy_id'));
        $data['lessons'] = $this->get_lessons();
        $data['academy_users'] = $this->get_users_of_academy(1);
        $this->view("/admin/academy/teachers", "Academy Users", $data);
    }


    public function academyTeachers() {
        $data['lessons'] = $this->get_lessons();
        $data['teachers'] = $this->get_teachers_of_academy(session_get('academy_id'));
        $this->view("/admin/academy/teachers", "Academy Teachers", $data);
    }


    public function academyDefineClasses() {
        $data['lessons'] = $this->get_lessons();
        $data['classes'] = $this->get_classes_of_academy(session_get('academy_id'));
        $data['teachers'] = $this->get_teachers_of_academy(session_get('academy_id'));
        $this->view("/admin/academy/define-classes", "Academy Define Classes", $data);
    }


    public function academyDefineSchedule() {
        $data['classes'] = $this->get_classes_of_academy(session_get('academy_id'));
        $data['schedules'] = $this->get_schedules_of_academy(session_get('academy_id'));
        $data['students'] = $this->get_students_of_academy(session_get('academy_id'));
        $this->view("/admin/academy/schedule", "Academy Define Schedule", $data);
    }



}