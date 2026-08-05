<?php

namespace Modules\Analytics\Controllers\Web;

use Core\http\ResponseFactory;

class AnalyticsController {




    public function articles() { return ResponseFactory::view('Analytics::articles')->layout('main')->title('سُرناز | صفحه اصلی'); }
    public function articleDetails() { return ResponseFactory::view('Analytics::article-details')->layout('main')->title('سُرناز | صفحه اصلی'); }



    public function user() { return ResponseFactory::view('Analytics::user')->layout('main')->title('سُرناز | صفحه اصلی'); }
    public function users() { return ResponseFactory::view('Analytics::users')->layout('main')->title('سُرناز | صفحه اصلی'); }
    public function academy() { return ResponseFactory::view('Analytics::academy')->layout('main')->title('سُرناز | صفحه اصلی'); }
    public function academies() { return ResponseFactory::view('Analytics::academies')->layout('main')->title('سُرناز | صفحه اصلی'); }
    public function academyEnroll() { return ResponseFactory::view('Analytics::academy-enroll')->layout('main')->title('سُرناز | صفحه اصلی'); }
    public function sendAcademyRequest() { return ResponseFactory::view('Analytics::send-academy-request')->layout('main')->title('سُرناز | صفحه اصلی'); }



    public function adminPanel() { return ResponseFactory::view('Analytics::admin-panel')->layout('admin')->title('سُرناز | صفحه اصلی'); }












}
