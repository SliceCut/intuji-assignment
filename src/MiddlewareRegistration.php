<?php

namespace App;

use App\Middlewares\Authenticate;

class MiddlewareRegistration
{
    protected $middlewareWeb = [
        "auth" => Authenticate::class
    ];

    public function getMiddlewareWeb()
    {
        return $this->middlewareWeb;
    }
}
