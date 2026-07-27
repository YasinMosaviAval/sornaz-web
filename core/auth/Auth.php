<?php

namespace Core\Auth;

use Modules\System\Contracts\UserRepositoryInterface;

class Auth {

    protected string $sessionKey = '_auth_user';


    public function __construct(protected UserRepositoryInterface $users) {
    }


    public function check(): bool {
        return session()->has($this->sessionKey);
    }


    public function user(): ?array {
        $id = $this->id();
        if (!$id) {
            return null;
        }
        return $this->users->find($id);
    }


    public function id(): ?int {
        return session()->get($this->sessionKey);
    }


    public function login(int $userId): void {
        session()->put($this->sessionKey, $userId);
    }


    public function logout(): void {
        session()->forget($this->sessionKey);
    }




}