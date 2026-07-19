<?php

use Core\Auth\Gate;

Gate::define(
    'edit-post',
    function ($user, $post) {
        return $user['id'] === $post['user_id'];
    }
);

Gate::define(
    'delete-post',
    function ($user, $post) {
        return $user['id'] === $post['user_id'];
    }
);