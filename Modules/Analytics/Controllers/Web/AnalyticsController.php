<?php

namespace Modules\Analytics\Controllers\Web;

use Core\http\ResponseFactory;

class AnalyticsController {

    public function home() { return ResponseFactory::view('Analytics::home')->layout('main')->title('سُرناز | رابط کاربری'); }
    public function aboutUs() { return ResponseFactory::view('Analytics::about-us')->layout('main')->title('سُرناز | صفحه اصلی'); }
    public function contactUs() { return ResponseFactory::view('Analytics::contact-us')->layout('main')->title('سُرناز | صفحه اصلی'); }



    public function articles() { return ResponseFactory::view('Analytics::articles')->layout('main')->title('سُرناز | صفحه اصلی'); }
    public function articleDetails() { return ResponseFactory::view('Analytics::article-details')->layout('main')->title('سُرناز | صفحه اصلی'); }



    public function user() { return ResponseFactory::view('Analytics::user')->layout('main')->title('سُرناز | صفحه اصلی'); }
    public function users() { return ResponseFactory::view('Analytics::users')->layout('main')->title('سُرناز | صفحه اصلی'); }
    public function academy() { return ResponseFactory::view('Analytics::academy')->layout('main')->title('سُرناز | صفحه اصلی'); }
    public function academies() { return ResponseFactory::view('Analytics::academies')->layout('main')->title('سُرناز | صفحه اصلی'); }
    public function academyEnroll() { return ResponseFactory::view('Analytics::academy-enroll')->layout('main')->title('سُرناز | صفحه اصلی'); }
    public function sendAcademyRequest() { return ResponseFactory::view('Analytics::send-academy-request')->layout('main')->title('سُرناز | صفحه اصلی'); }



    public function login() { return ResponseFactory::view('Analytics::login')->layout('auth')->title('سُرناز | صفحه اصلی'); }
    public function register() { return ResponseFactory::view('Analytics::register')->layout('auth')->title('سُرناز | صفحه اصلی'); }
    public function forgotPassword() { return ResponseFactory::view('Analytics::forgot-password')->layout('auth')->title('سُرناز | صفحه اصلی'); }



    public function adminPanel() { return ResponseFactory::view('Analytics::admin-panel')->layout('admin')->title('سُرناز | صفحه اصلی'); }












}
