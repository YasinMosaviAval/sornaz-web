<?php

require_once 'article/pages.php';
require_once 'article/queries.php';
require_once 'article/api.php';

class ArticleController extends BaseController {
    
    public function __construct() {
        // grantAcademyManaging();
        // grantAcademyManager();
    }

    use ArticleQueriesTrait;
    use ArticlePageSTrait;
    use ArticleApiTrait;

}