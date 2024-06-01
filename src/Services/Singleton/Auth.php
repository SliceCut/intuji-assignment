<?php

namespace App\Services\Singleton;

class Auth
{
    private $user = null;

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function user(): mixed
    {
        return $this->user;
    }
}
