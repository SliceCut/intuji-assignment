<?php

namespace App;

class MiddlewareRegistration
{
    protected $middlewareWeb = [
        //put here your middleware
    ];

    public function getMiddlewareWeb()
    {
        return $this->middlewareWeb;
    }
}
