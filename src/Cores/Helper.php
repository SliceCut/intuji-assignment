<?php

use App\Cores\FlashMessage;
use App\Cores\Session;
use App\Cores\Str;
use App\Cores\Validation;
use App\View;

function env(string $key)
{
    return $_ENV[$key] ?? null;
}

function baseUrl(string $path = '')
{
    if (isset($_SERVER['HTTPS'])) {
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
    } else {
        $protocol = 'http';
    }
    return $protocol . "://" . $_SERVER['HTTP_HOST'] . '/' . $path;
}

function includeFile(string $path, array $data = [])
{
    return View::make($path, $data);
}

function asset(string $path)
{
    return  baseUrl() . $path;
}

function oldValue(string $key, string $default = "")
{
    $oldValues = Session::get(Validation::SESSION_OLD_VALUES);

    return $oldValues[$key] ?? $default;
}

function redirect(string $url, array $flashMessages = [])
{
    $FlashMessageObj = new FlashMessage;
    foreach ($flashMessages as $key => $value) {
        $FlashMessageObj->setMessage($key, $value);
    }

    Session::put(FlashMessage::SESSION_FLASH_MESSAGE, $FlashMessageObj);

    header('Location: ' . str_replace('/\/\//g', '/', baseUrl($url)));
}

function previousUrl()
{
    return $_SERVER['HTTP_REFERER'];
}

function currentUrl()
{
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}

function formMethod($method)
{
    return "<input type='hidden' name='method' value='$method'>";
}

function slugify($text, string $divider = '-')
{
    return Str::slugify($text, $divider);
}
