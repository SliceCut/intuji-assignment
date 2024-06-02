<?php

namespace App\Controllers;

use App\View;

class HomeController extends BaseController
{
    public function __construct(
    ) {
    }

    public function index()
    {
        return View::make("index.php");
    }
}
