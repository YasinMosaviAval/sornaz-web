<?php

require_once 'old/old_model_article.php';
require_once 'new/model_article.php';

class ArticleModel {

    use OldModelArticleTrait;
    use ModelArticleTrait;

}
