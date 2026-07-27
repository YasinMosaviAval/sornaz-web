<?php

use Core\Auth\Gate;

Gate::define(
    'edit-post',
    function ($user, $post) {
        return $user['user_id'] === $post['author_id'];
    }
);

Gate::define(
    'delete-post',
    function ($user, $post) {
        return $user['user_id'] === $post['author_id'];
    }
);
