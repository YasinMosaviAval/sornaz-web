<?php
namespace Modules\System\Controllers\Web;

use Core\http\ResponseFactory;
use Core\validation\ValidationException;
use Modules\System\Services\UserService;
use Modules\System\Requests\UserStoreRequest;

class UserController {

    public function __construct(protected UserService $service) {
    }


    public function login() {
        $identifier = trim($_POST['identifier'] ?? '');
        $password   = $_POST['password'] ?? '';
        if (!$identifier || !$password) {
            return redirect('/login')
                ->withInput($_POST)
                ->withErrors(['identifier' => 'نام‌کاربری/ایمیل و رمز عبور الزامی است.']);
        }
        $user = $this->service->attempt($identifier, $password);
        if (!$user) {
            return redirect('/login')
                ->withInput($_POST)
                ->withErrors(['identifier' => 'نام‌کاربری/ایمیل یا رمز عبور اشتباه است.']);
        }
        auth()->login($user['user_id']);
        return redirect('/page/home');
    }


    public function logout() {
        auth()->logout();

        return redirect('/page/home');
    }


    public function store() {
        try {
            $request = new UserStoreRequest($_POST);
            $data = $request->validated();
            $userId = $this->service->register($data);
            if (!$userId) {
                return redirect('/register')
                    ->withInput($_POST)
                    ->withErrors(['username' => 'ثبت‌نام ناموفق بود.']);
            }
            return redirect('/login');
        } catch (ValidationException $e) {
            return redirect('/register')
                ->withInput($_POST)
                ->withErrors($e->getErrors());
        }
    }




}
