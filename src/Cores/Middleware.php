<?php

namespace App\Cores;

abstract class Middleware
{
    public function handle($next)
    {
        return $next;
    }
}
