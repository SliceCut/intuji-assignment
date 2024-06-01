<?php

use App\Cores\FlashMessage;
use App\Cores\Session;
use App\Cores\Str;
use App\Cores\Validation;
use App\Providers\AppServiceProvider;
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

/**
 * Get a value from a nested array using dot notation for keys.
 *
 * @param array $array The array to search.
 * @param string $key The dot-notated key string.
 * @param mixed $default The default value to return if the key is not found.
 * @return mixed The value found in the array or the default value.
 */
function array_get($array, $key, $default = null) {
    if (!is_array($array)) {
        return $default;
    }

    if (array_key_exists($key, $array)) {
        return $array[$key];
    }

    $keys = explode('.', $key);

    foreach ($keys as $key) {
        if (!is_array($array) || !array_key_exists($key, $array)) {
            return $default;
        }
        $array = $array[$key];
    }

    return $array;
}

/**
 * Get the value from the configuration file
 * 
 * @param string $key
 * @param ?mixed $default
 * @return mixed
 */
function config($key, $default = null)
{
    return AppServiceProvider::getInstance()->getConfig($key, $default);
}