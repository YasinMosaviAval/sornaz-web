<?php

namespace Modules\Content\Models;

use Core\Database\Model;
use Modules\System\Models\User;

class Post extends Model {
    protected static string $table = 'posts';
    protected static string $primaryKey = 'post_id';


    public function author() {
        return $this->belongsTo(
            User::class,
            'author_id',
            'user_id'
        );
    }



}