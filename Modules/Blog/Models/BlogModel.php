<?php

namespace Modules\Blog\Models;

use Core\Database\Model;

class BlogModel extends Model {

    protected string $table = 'posts';
    protected string $primaryKey = 'post_id';
    protected array $fillable = [
        'author_id',
        'slug',
        'categories',
        'cover',
        'cover_media_id',
        'views_count',
        'published_at',
        'type',
        'status',
        'visibility',
        'visibility_user_id',
        'password',
        'comment_count',
        'related_posts_id',
    ];
    protected array $casts = [
        // 'created_at' => 'datetime',
    ];
    protected bool $timestamps = true;
    protected bool $softDeletes = true;

}