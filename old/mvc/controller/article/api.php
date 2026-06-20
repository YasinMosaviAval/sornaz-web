<?php

trait ArticleApiTrait {

    public function api_article_details($post_id) {
        echo json_encode($this->get_posts_with_id($post_id));
    }


    public function api_article_list() {
        $categories = $this->get_article_categories();
        $posts = $this->get_all_posts();
        echo json_encode([$posts, $categories]);
    }

}