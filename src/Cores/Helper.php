<?php

use App\Cores\Container;
use App\Cores\FlashMessage;
use App\Cores\Session;
use App\Cores\Str;
use App\Cores\Validation;
use App\Providers\AppServiceProvider;
use App\Services\Singleton\Auth;
use App\View;

function env(string $key)
{
    return $_ENV[$key] ?? null;
}

function dd(...$arg)
{
    var_dump($arg);
    exit;
}

/**
 * Get the app container
 * 
 * @return  Container
 */
function app()
{
    return Container::getInstance();
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

    if (is_valid_url($url)) {
        $target = $url;
    } else {
        $target = str_replace('/\/\//g', '/', baseUrl($url));
    }
    header('Location: ' . $target);
    exit;
}

/**
 * redirect to previous url
 * 
 * @param array $flashMessages
 */
function redirectBack($flashMessages = [])
{
    $FlashMessageObj = new FlashMessage;
    foreach ($flashMessages as $key => $value) {
        $FlashMessageObj->setMessage($key, $value);
    }

    Session::put(FlashMessage::SESSION_FLASH_MESSAGE, $FlashMessageObj);

    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
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
function array_get($array, $key, $default = null)
{
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
    return app()->make(AppServiceProvider::class)->getConfig($key, $default);
    // AppServiceProvider::getInstance()->getConfig($key, $default);
}

/**
 * Check if url is valid http url
 *
 * @param string $url
 * @return boolean
 */
function is_valid_url($url)
{
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Return a JSON response
 *
 * @param mixed $data The data to encode as JSON
 * @param int $status_code The HTTP status code (default 200)
 */
function jsonResponse($data, $status_code = 200)
{
    header('Content-Type: application/json');
    http_response_code($status_code);
    echo json_encode($data);
    exit;
}

/**
 * Get the user authentication ojbect
 * 
 * @return Auth
 */
function auth()
{
    return app()->get(Auth::class);
}

/**
 * Extract the date format from the value
 *
 * @param string $value
 * @return bool|string
 */
function extractDateFormat($value)
{
    // Regular expression to match "date_format:" followed by any characters
    $pattern = '/date_format:\s*(.+)/';
    if (preg_match($pattern, $value, $matches)) {
        return trim($matches[1]);
    } else {
        return false;
    }
}

/**
 * Check if value is date or date time
 *
 * @param string $value
 * @return boolean
 */
function isDateOrDateTime($value)
{
    // Try to parse the string as a date or datetime
    $timestamp = strtotime($value);

    // If strtotime returns a valid timestamp, the string is a date or datetime
    return $timestamp !== false;
}

function extractStringAfter($value, $after)
{
    $pattern = "/$after:(.+)/";
    if (preg_match($pattern, $value, $matches)) {
        return $matches[1];
    } else {
        return false;
    }
}

/**
 * Convert datetime to the RFC3339 format
 *
 * @param string $datetime
 * @return string
 */
function convertDateTimeToRFC3339Format($datetime)
{
    $dateTimeObject = new DateTime($datetime);
    return $dateTimeObject->format(DateTime::RFC3339);
}

/**
 * Convert datetime to the formated datetime
 *
 * @param string $datetime
 * @return string
 */
function formatDateTime($datetime, $format = "Y-m-d\TH:i")
{
    $dateTimeObject = new DateTime($datetime);
    return $dateTimeObject->format($format);
}

function objectToArray($obj)
{
    if (is_object($obj)) {
        $obj = (array) $obj;
    }

    if (is_array($obj)) {
        foreach ($obj as $key => $value) {
            $obj[$key] = objectToArray($value);
        }
    }

    return $obj;
}
