CREATE TABLE IF NOT EXISTS social_comments (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 post_id BIGINT UNSIGNED NOT NULL,
 user_id BIGINT UNSIGNED NOT NULL,
 body TEXT NOT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 deleted_at DATETIME NULL,
 INDEX social_comments_post (post_id,id),
 INDEX social_comments_user (user_id),
 FOREIGN KEY (post_id) REFERENCES social_posts(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
