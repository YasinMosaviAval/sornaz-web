<?php

use Core\Auth\Gate;
use Core\Http\Request;
use Core\Router\Router;
use Modules\System\Controllers\UserController;
use Modules\System\Models\User;

use Core\Database\DB;
use Modules\Content\Models\Post;
use Modules\System\Models\Role;

use Modules\System\Events\UserCreated;
use Modules\System\Listeners\CreateTranslationRecord;
use Modules\System\Listeners\SendWelcomeEmail;
use Modules\System\Listeners\WriteAuditLog;

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

Router::get('/', function () {return 'Framework Works!';});
Router::get('/about', function () {return 'About Page';});
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
Router::get('/show-message', function () {return session()->getFlash('success', 'No Message');});
Router::get('/view-test', function () {return view('users.index', ['name' => 'Yasin', 'age' => 35]);});
Router::get('/layout-test', function () {return view('users.index', ['name' => 'Yasin', 'age' => 35, 'title' => 'Users'])->layout('main');});
Router::get('/login-test', function () {auth()->login(2); return redirect('/dashboard');});
Router::get('/save-user', function () {session()->flash('success', 'User Saved'); return redirect('/show-message');});
Router::get('/user-test', function () {var_dump(auth()->user());});
Router::get('/logout-test', function () {auth()->logout(); return 'Logged Out';});
Router::get('/validation-form', function () {
        return '
        <form method="POST" action="/validation-test">
            ' . csrf_field() . '
            <input name="name">
            <button>Save</button>
        </form>
        ';
    }
);
Router::get('/gate-test', function () {
        $post = ['id' => 10, 'user_id' => 1];
        if (Gate::allows('edit-post', $post)) {
            return 'Allowed';
        }
        return 'Denied';
    }
);
Router::get('/login', function () {
    return '
        <form method="POST" action="/login">
            '.csrf_field().'
            <input name="email">
            <button>Login</button>
        </form>';
});
Router::get('/db-test', function () {
        $stmt = db()->query(
            'SELECT NOW() AS current_datetime'
        );
        return '<pre>' .
            print_r(
                $stmt->fetch(),
                true
            ) .
            '</pre>';
    }
);
Router::get('/db-tables-test', function () {
        $stmt = db()->query(
            'SHOW TABLES'
        );
        return '<pre>' .
            print_r(
                $stmt->fetchAll(),
                true
            ) .
            '</pre>';
    }
);

Router::get('/dashboard', function () {return view('dashboard.index')->layout('main');})->middleware('auth');
Router::get('/transaction-test', function () {
        transaction(function () {
            DB::table('users')->insert(['email' => 'test@test.com']);
            DB::table('posts')->insert(['cover' => 'sornaz_0001_music_00_1111111111111']);
        });
        return 'Transaction Success';
    }
);
Router::get('/model-test', function () {$user = User::find(1); return '<pre>' . print_r($user->toArray(), true) . '</pre>';});
Router::get('/model-test-column', function () { $user = User::find(1); return $user->email;});
Router::get('/model-test-save', function () {$user = new User(['email' => 'new@test.com']); $user->save();});
Router::get('/model-test-create', function () {User::create(['email' => 'test@test.com']);});
Router::get('/model-test-update', function () {$user = User::find(1); $user->update(['email' => 'updated@test.com']);});
Router::get('/model-test-delete', function () { $user = User::find(1129); $user->delete();});
Router::get('/relation-test', function () {$post = Post::find(1); $author = $post->author(); return $author->email;});
Router::get('/relation-test1', function () {$user = User::find(1); $posts = $user->posts(); return count($posts);});
Router::get('/many-test', function () {
        $user = User::find(1);
        $roles = $user->roles();
        return '<pre>' . print_r(array_map(fn($role) => $role->toArray(), $roles), true) . '</pre>';
    }
);
Router::get('/many-test1', function () {
        $role = Role::find(1);
        $users = $role->users();
        return '<pre>' . print_r(array_map(fn($user) => $user->toArray(), $users), true) . '</pre>';
    }
);
Router::get('/event-test', function () {
        $logDir = base_path('storage/logs');
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        User::creating(function ($user) {
                file_put_contents(
                    base_path('storage/logs/events.log'),
                    'creating user' . PHP_EOL,
                    FILE_APPEND
                );
            }
        );
        User::created(function ($user) {
                file_put_contents(
                    base_path('storage/logs/events.log'),
                    'created user' . PHP_EOL,
                    FILE_APPEND
                );
            }
        );
        User::create(['email' => 'event@test.com']);
        return 'done';
    }
);
Router::get('/dispatcher-test', function () {
        events()->listen(UserCreated::class, function ($event) {echo 'Listener 1<br>'; echo $event->user->email;});
        events()->listen(UserCreated::class, fn() => print 'Email Sent<br>');
        events()->listen(UserCreated::class, fn() => print 'Log Written<br>');
        events()->listen(UserCreated::class, fn() => print 'Notification Created<br>');
        $user = User::find(1);
        events()->dispatch(new UserCreated($user));
        return '';
    }
);
Router::get('/listener-test', function () {
        events()->listen(UserCreated::class, SendWelcomeEmail::class);
        events()->listen(UserCreated::class, CreateTranslationRecord::class);
        events()->listen(UserCreated::class, WriteAuditLog::class);
        $user = User::find(1);
        events()->dispatch(new UserCreated($user));
        return '';
    }
);
Router::get('/provider-test', function () {$user = User::find(1); events()->dispatch(new UserCreated($user)); return '<hr>Done';});
Router::get('/users-test', function () {return '<pre>' . print_r(User::all(), true) . '</pre>';});
Router::get('/find-test', function () {var_dump(User::find(0));});
Router::get('/soft-delete', function () {$user = User::find(0); $user->delete(); return 'Deleted';});
Router::get('/all-users', function () {return '<pre>' . print_r(User::all(), true) . '</pre>';});
Router::get('/find-user', function () {var_dump(User::find(0));});
Router::get('/with-trashed', function () {return '<pre>' . print_r(User::withTrashed()->get(), true) . '</pre>';});
Router::get('/only-trashed', function () {return '<pre>' . print_r(User::onlyTrashed()->get(), true) . '</pre>';});
Router::get('/restore-user', function () {DB::table('users')->where('user_id', 1138)->update(['deleted_at' => null]); return 'Restored';});




Router::get('/restore-test',
    function () {
        // $user = User::withTrashed()->find(1134);
        $user = User::query()->withTrashed()->where('user_id', 1134)->first();
        $user = $user ? new User($user) : null;
        // if (!($user instanceof User)) {
        if (!$user) {
            return 'User Not Found';
        }
        $user->restore();
        return 'Restored';
    }
);
Router::get('/force-delete-test',
    function () {
        // $user = User::withTrashed()->find(1134);
        $user = User::query()->withTrashed()->where('user_id', 1134)->first();
        $user = $user ? new User($user) : null;
        // die($user);
        // if (!($user instanceof User)) {
        if (!$user) {
            return 'User Not Found';
        }
        $user->forceDelete();
        return 'Deleted';
    }
);
Router::get('/soft-delete-test', function () {$user = User::find(1134); $user->delete(); return 'Soft Deleted';});














Router::post('/validation-test', function (Request $request) {$request->validate(['name'  => 'required|min:3']); return 'Valid';})->middleware('csrf');
Router::post('/users', fn() => 'CREATE USER');



Router::delete('/users/{id}', fn($id) => "DELETE USER {$id}");
