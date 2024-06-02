<?php

namespace App\Services\Singleton;

class Auth
{
    private $user = null;
    private $token = null;

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function user(): mixed
    {
        return $this->user;
    }

    public function token(): mixed
    {
        return $this->token;
    }
}
