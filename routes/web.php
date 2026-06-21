<?php

use Core\Http\Request;
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

// Router::get('/users', fn() => 'Users')->middleware('auth');
// Router::get('/users', fn() => 'GET USERS');
Router::get('/users', function () {return 'Users Page';});
Router::get('/users/{id}', [UserController::class,'show']);
Router::get('/users/{id}/posts/{post_id}', [UserController::class, 'show']);

Router::get('/test', function () {session()->put('name', 'Yasin'); return 'saved';});
Router::get('/app-test', function () {return get_class(app());});
Router::get('/helper-test', function () {return get_class(session());});
Router::get('/show', function () {return session()->get('name');});
Router::get('/flash-show', function () {return session()->getFlash('success', 'No Message');});
Router::get('/flash-set', function () {session()->flash('success', 'User Saved'); return 'Flash Created';});

Router::get('/redirect-test', function () {return redirect('/users');});
Router::get('/back-test', function () {return back();});

Router::get(
    '/save-user',
    function () {
        session()->flash('success', 'User Saved');
        return redirect('/show-message');
    }
);
Router::get('/show-message', function () {return session()->getFlash('success', 'No Message');});
Router::get('/view-test', function () {return view('users.index', ['name' => 'Yasin', 'age' => 35]);});
Router::get('/layout-test', function () {return view('users.index', ['name' => 'Yasin', 'age' => 35, 'title' => 'Users'])->layout('main');});


Router::get('/login-test', function () {

    auth()->login([
        'id' => 15,
        'name' => 'Yasin'
    ]);

    return 'Logged In';
});
Router::get('/user-test', function () {

    var_dump(
        auth()->user()
    );
});
Router::get('/logout-test', function () {

    auth()->logout();

    return 'Logged Out';
});

Router::get(
    '/dashboard',
    function () {

        return 'Dashboard';
    }
)->middleware('auth');

Router::get('/login', function () {

    return '
    <form method="POST" action="/login">

        '.csrf_field().'

        <input name="email">

        <button>
            Login
        </button>

    </form>';
});


Router::post('/login', function (Request $request) {

    auth()->login([
        'id' => 1,
        'email' => $_POST['email']
    ]);

    return redirect('/dashboard');
});

Router::get('/dashboard', function () {

    return view(
        'dashboard.index'
    )->layout('main');

})->middleware('auth');
// Router::get(
//     '/validation-form',
//     function () {
//         return '
//         <form method="POST" action="/validation-test">
//             Name
//             <input name="name" value="' . old('name') . '">
//             <br>
//             ' . error('name') . '
//             <br><br>
//             Email
//             <input name="email" value="' . old('email') . '">
//             <br>
//             ' . error('email') . '
//             <br><br>
//             <button>Submit</button>
//         </form>
//         ';
//     }
// );
Router::get(
    '/validation-form',
    function () {
        return '
        <form method="POST" action="/validation-test">
            ' . csrf_field() . '
            <input name="name">
            <button>Save</button>
        </form>
        ';
    }
);
Router::post(
    '/validation-test',
    function (Request $request) {
        $request->validate([
            'name'  => 'required|min:3',
            // 'email' => 'required|email'
        ]);
        return 'Valid';
    }
)->middleware('csrf');




Router::post('/users', fn() => 'CREATE USER');

Router::delete('/users/{id}', fn($id) => "DELETE USER {$id}");
