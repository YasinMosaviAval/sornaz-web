<?php

require_once 'user/pages.php';
require_once 'user/queries.php';
require_once 'user/api.php';

class UserController extends BaseController {

    public function __construct() {
        // grantPanel();
    }

    use UserPagesTrait;
    use UserQueriesTrait;
    use UserApiTrait;

}
