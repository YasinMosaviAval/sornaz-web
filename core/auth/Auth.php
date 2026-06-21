<?php

namespace Core\Auth;

class Auth
{
    protected string $sessionKey = '_auth_user';

    public function check(): bool
    {
        return session()->has(
            $this->sessionKey
        );
    }

    public function user(): ?array
    {
        return session()->get(
            $this->sessionKey
        );
    }

    public function id(): ?int
    {
        return $this->user()['id']
            ?? null;
    }

    public function login(
        array $user
    ): void {

        session()->put(
            $this->sessionKey,
            $user
        );
    }

    public function logout(): void
    {
        session()->forget(
            $this->sessionKey
        );
    }
}