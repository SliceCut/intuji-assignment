<?php

namespace App;

use App\Cores\ErrorBag;
use App\Cores\FlashMessage;
use App\Cores\Session;
use App\Cores\Validation;

class View
{
    public static function make(string $path, array $data = [])
    {
        /**
         * set the variable
         */
        foreach ($data as $key => $value) {
            $$key = $value;
        }

        $error = App::error();
        $flashMessage = App::flashMessage();

        require dirname(__DIR__) . "/views/$path";
    }
}
