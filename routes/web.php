<?php

use Core\Router\Router;
use Modules\System\Controllers\UserController;

/*
    Router::get('/test', function () {
        $container = new \Core\Container\Container();
        $controller = $container->make(\Modules\System\Controllers\UserController::class);
        return $controller->index();
    });

    Router::get('/hello/{name}', function ($name) {return "Hello {$name}";});
*/


Router::group(
    ['prefix' => '/admin'],
    function () {
        Router::get('/users', fn() => 'Admin Users');
        Router::get('/posts', fn() => 'Admin Posts');
        Router::get('/users/{id}', fn($id) => "User {$id}");
        Router::group(
            ['prefix' => '/academy'],
            function () {
                Router::get('/students', fn() => 'Students');
            }
        );
    }
);


// Router::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth');

// Router::group(
//     [
//         'prefix' => '/admin',
//         'namespace' => 'Modules\\System\\Controllers'
//     ],
//     function () {
//         Router::get('/users', ['UserController', 'index']);
//     }
// );

// Router::group(
//     [
//         'prefix' => '/admin',
//         'middleware' => ['auth']
//     ],
//     function () {
//         Router::get('/dashboard', [DashboardController::class, 'index']);
//     }
// );

Router::get('/', function () {return 'Framework Works!';});
Router::get('/about', function () {return 'About Page';});

Router::get('/users', fn() => 'Users')->middleware('auth');
// Router::get('/users', fn() => 'GET USERS');
Router::get('/users/{id}', [UserController::class,'show']);
Router::get('/users/{id}/posts/{post_id}', [UserController::class, 'show']);


Router::post('/users', fn() => 'CREATE USER');

Router::delete('/users/{id}', fn($id) => "DELETE USER {$id}");
