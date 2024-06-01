<?php

use App\View;

$router->get('/', function () {
    return View::make("index.php");
});


$router->get('/oauth/redirect', "App\Controllers\AuthController@oauthRedirect");
$router->get('/oauth/callback', "App\Controllers\AuthController@oauthCallback");

$router->get('/home', [
    "uses" => "App\Controllers\HomeController@index",
    "middleware" => ["auth"]
]);