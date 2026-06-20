<?php

require_once 'page/pages.php';
require_once 'page/queries.php';
require_once 'page/api.php';


class PageController extends BaseController {

    public function __construct() {
        // grantPanel();
    }

    use PagePagesTrait;
    use PageQueriesTrait;
    use PageApiTrait;

}
