<?php

trait ArticleQueriesTrait {



    private function get_posts_with_id(int $post_id) {
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';

        return Db::getInstance()->query("SELECT * FROM posts 
            LEFT OUTER JOIN translations ON posts.post_id = translations.table_id 
            WHERE post_id=:post_id AND locale=:locale AND table_name=:table_name
        ", array(
            'post_id'    => $post_id,
            'locale'     => $locale,
            'table_name' => 'posts',
        ));
    }


    private function get_comments_with_id(int $post_id) {
        return Db::getInstance()->query("SELECT * FROM sor_contacts WHERE type='comment' AND post_id=:post_id", array('post_id' => $post_id));
    }


    private function get_article_categories(){
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';

        return Db::getInstance()->query("SELECT * FROM sor_settings 
            LEFT OUTER JOIN translations ON sor_settings.setting_id = translations.table_id 
            WHERE locale=:locale AND translations.table_name=:table_name AND variable_name LIKE '%article_category_%'
        ", array(
            'locale'     => $locale,
            'table_name' => 'sor_settings',
        ));
    }


    private function get_all_posts(){
        return Db::getInstance()->query("SELECT * FROM posts 
            LEFT OUTER JOIN translations ON posts.post_id = translations.table_id 
            WHERE type=:type AND status=:status AND locale=:locale AND table_name=:table_name
            ORDER BY post_id DESC
        ", 
        array(
            'type'       => 'post',
            'status'     => 'published',
            'locale'     => 'fa',
            'table_name' => 'posts',
        ));
    }


    private function get_some_posts(){
        return Db::getInstance()->query("SELECT * FROM posts WHERE type=:type ORDER BY status DESC", array(
            'type' => 'revision',
            // 'status' => 'publish',
        ));
    }


}