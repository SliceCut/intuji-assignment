<?php

use App\View;

$router->get('/', function () {
    return View::make("index.php");
});