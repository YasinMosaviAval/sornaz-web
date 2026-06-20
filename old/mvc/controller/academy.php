<?php

require_once 'academy/pages.php';
require_once 'academy/queries.php';
require_once 'academy/api.php';

class AcademyController extends BaseController{

    public function __construct() {
        grantAcademyManaging();
        // grantAcademyManager();
    }

    use AcademyPagesTrait;
    use AcademyQueriesTrait;
    use AcademyApiTrait;

}