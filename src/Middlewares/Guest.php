<?php

namespace App\Middlewares;

use App\Cores\Middleware;
use App\Cores\Request;
use App\Cores\Session;

class Guest extends Middleware
{
    public function __construct(
        protected Session $session
    ) {
    }

    public function handle($next, Request $request)
    {
        if ($this->session->get("access_token")) {
            return redirect("event");
        }
        return $next;
    }
}
