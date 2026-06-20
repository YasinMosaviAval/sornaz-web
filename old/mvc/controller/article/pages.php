<?php

trait ArticlePagesTrait {


//   public function showArticleList() {View::render("/admin/show-article-list.php", "Show Article List");}
//   public function addArticle() {View::render("/admin/add-article.php", "Add Article");}
//   public function editArticle() {View::render("/admin/edit-article.php", "Edit Article");}
//   public function categories() {View::render("/admin/categories.php", "Categories");}
//   public function comments() {View::render("/admin/comments.php", "Comments");}



    public function articleDetails($post_id): void {
        $data['categories'] = $this->get_article_categories();
        $data['article'] = $this->get_posts_with_id($post_id);
        $related_articles = explode(',', $data['article'][0]['related_posts_id']);
        if(sizeof($related_articles) > 1) {
            for($i = 0; $i < sizeof($related_articles); $i++) {
                $data['related_articles'][$i] = $this->get_posts_with_id($related_articles[$i])[0];
            }
        } else {
            $data['related_articles'] = array();
        }
        $data['comments'] = $this->get_comments_with_id($post_id);
        $this->view("/article/article-details", "Article Details", $data);
    }


    public function articleList(): void {
        $data['categories'] = $this->get_article_categories();
        $data['posts'] = $this->get_all_posts();
        $this->view("/article/article-list", "Article List", $data);
    }

    public function articleListshowing() {
        // View::renderPartial("/article/wp/article-list-showing.php", $this->get_some_posts());
        $this->partial("/article/wp/article-list-showing", $this->get_some_posts());
    }


}