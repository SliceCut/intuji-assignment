<?php

namespace App\Controllers;

use App\Traits\ApiResponse;
use App\View;

abstract class BaseController
{
    use ApiResponse;

    public function render(string $path, array $data = [])
    {
        return View::make($path, $data);
    }
}
