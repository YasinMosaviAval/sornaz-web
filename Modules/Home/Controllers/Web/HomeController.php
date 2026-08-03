<?php

namespace Modules\Home\Controllers\Web;

use Core\http\ResponseFactory;

class HomeController {

    public function index() { return ResponseFactory::view('Home::home')->layout('main')->title('سُرناز | مرجع آموزشگاه‌های موسیقی'); }

}
