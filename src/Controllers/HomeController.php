<?php

namespace App\Controllers;

use App\Cores\Request;
use App\Cores\Session;
use App\Services\Singleton\Auth;
use App\View;

class HomeController extends BaseController
{
    public function __construct(
        protected Session $session,
        protected Auth $auth
    ) {
    }

    public function index()
    {
        return View::make("home.php", [
            "user" => $this->auth->user()
        ]);
    }
}
