<?php

namespace App\Controllers;

use App\View;

abstract class BaseController
{
    public function render(string $path, array $data = [])
    {
        return View::make($path, $data);
    }
}
