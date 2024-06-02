<?php

$router->get('/', [
    "middleware" => ["guest"],
    "uses" => "App\Controllers\HomeController@index",
]);

/**
 * Auth Route Group
 */
$router->get('/oauth/redirect', "App\Controllers\AuthController@oauthRedirect");
$router->get('/oauth/callback', "App\Controllers\AuthController@oauthCallback");
$router->post("/logout", [
    "uses" => "App\Controllers\AuthController@logout",
    "middleware" => ["auth"]
]);

/**
 * Event route group
 */
$router->get('/event', [
    "uses" => "App\Controllers\EventController@index",
    "middleware" => ["auth"]
]);
$router->get('/event/create', [
    "uses" => "App\Controllers\EventController@create",
    "middleware" => ["auth"]
]);
$router->post('/events', [
    "uses" => "App\Controllers\EventController@store",
    "middleware" => ["auth"]
]);
$router->get('/event/edit', [
    "uses" => "App\Controllers\EventController@edit",
    "middleware" => ["auth"]
]);
$router->put('/events/update', [
    "uses" => "App\Controllers\EventController@update",
    "middleware" => ["auth"]
]);
$router->delete('/events/delete', [
    "uses" => "App\Controllers\EventController@destroy",
    "middleware" => ["auth"]
]);