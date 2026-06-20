<?php

trait OldModelPageTrait {


  public static function add_contact(string $author_email, string $author, string $parent, int $user_id, int $post_id, string $type, string $content, int $receiver_user_id, string $agent): int{
    return Db::getInstance()->insert("INSERT INTO sor_contacts (
      author_email, author, parent, user_id, post_id, type, content, receiver_user_id, agent)
    VALUES (
      :author_email, :author, :parent, :user_id, :post_id, :type, :content, :receiver_user_id, :agent
    )", array(
      'author_email' => $author_email, 
      'author' => $author, 
      'parent' => $parent, 
      'user_id' => $user_id, 
      'post_id' => $post_id, 
      'type' => $type, 
      'content' => $content, 
      'receiver_user_id' => $receiver_user_id, 
      'agent' => $agent,
    ));
  }



  public static function add_register_contact(int $user_id, string $author_email, string $author, int $post_id, string $content, string $parent, int $receiver_user_id, string $agent, string $type): int{
    return Db::getInstance()->insert("INSERT INTO sor_contacts (
      user_id, author_email, author, post_id, content, parent, receiver_user_id, agent, type
    )
    VALUES (
      :user_id, :author_email, :author, :post_id, :content, :parent, :receiver_user_id, :agent, :type
    )", array(
      'user_id' => $user_id,
      'author_email' => $author_email,
      'author' => $author,
      'post_id' => $post_id,
      'content' => $content,
      'parent' => $parent,
      'receiver_user_id' => $receiver_user_id,
      'agent' => $agent,
      'type' => $type,
    ));
  }



  



  // public static function update_post($post_id, $status, $modified){
  //   $db = Db::getInstance();
  //   $db->modify("UPDATE sor_posts SET modified=:modified, status=:status WHERE post_id=:post_id", array(
  //     'post_id' => $post_id,
  //     'modified' => $modified,
  //     'status' => $status
  //   ));
  // }


  // public static function delete_post($post_id){
  //   return Db::getInstance()->modify("DELETE FROM sor_posts WHERE post_id=:post_id", array('post_id' => $post_id));
  // }




  
}