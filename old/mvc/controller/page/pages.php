<?php

trait PagePagesTrait {


    // public function home() {
    //     $data['home'] = setIndexforDataArray($this->get_home_data(), 'variable_name');
    //     View::render("/page/home.php", "Home", $data);
    // }

// old - top ============================================================================

    public function home(): void {
        $model = new PageModel();
        $data['home'] = setIndexforDataArray($model->getHomeData(), 'variable_name');
        $this->view('/page/home', "Home", $data);
    }


    public function contactUs() {
        $data['contact-us'] = setIndexforDataArray($this->get_contact_us_categories(), 'variable_name');
        $this->view("/page/contact-us", "Contact Us", $data);
    }


    public function aboutUs() {
        $data['about_us'] = setIndexforDataArray($this->get_about_us_content(), 'variable_name');
        $this->view("/page/about-us", "About Us", $data);
    }




    // ====================================================================================================


    public function profile($user_id) {
        $data['profile'] = $this->get_user_by_id($user_id);
        $this->view("/page/profile", "Profile", $data);
    }


    public function users() { $this->view("/page/users.php", "Users"); }

    
    public function academyEnroll() {$this->view("/page/enroll", "Academy Enroll");}
    public function sendAcademyRequestForm() {$this->view("/page/academy-request-form", "Academy Request Form");}

    public function academies() { $this->view("/page/academies", "Academies"); }
    public function academies1() { $this->view("/page/academies1", "Academies 1"); }
    public function academies2() { $this->view("/page/academies2", "Academies 2"); }
    public function academies3() { $this->view("/page/academies3", "Academies 3"); }
    public function academies4() { $this->view("/page/academies4", "Academies 4"); }
    public function academies5() { $this->view("/page/academies5", "Academies 5"); }
    public function academies6() { $this->view("/page/academies6", "Academies 6"); }
    public function academies7() { $this->view("/page/academies7", "Academies 7"); }
    public function academies8() { $this->view("/page/academies8", "Academies 8"); }
    public function academies9() { $this->view("/page/academies9", "Academies 9"); }


    public function profileAcademy($academy_id) {
        $data['academy'] = $this->get_academy_by_id($academy_id);
        $this->view("/page/academy-profile", "Academy Profile", $data);
    }


    public function errorPage() { $this->view("/page/error", "Error"); }

}
