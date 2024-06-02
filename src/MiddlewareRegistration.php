<?php

namespace App;

use App\Middlewares\Authenticate;
use App\Middlewares\Guest;

class MiddlewareRegistration
{
    protected $middlewareWeb = [
        "auth" => Authenticate::class,
        "guest" => Guest::class
    ];

    public function getMiddlewareWeb()
    {
        return $this->middlewareWeb;
    }
}
