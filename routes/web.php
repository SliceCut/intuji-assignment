<?php

use App\View;

$router->get('/', function () {
    return View::make("index.php");
});

$router->get('/login', function () {
    return View::make("login.php");
});