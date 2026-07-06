<?php

use Core\Router\Router;
use Modules\Academy\Controllers\Api\AcademyController;

Router::get('/academies',function(){
    return (new AcademyController())->index();
});










