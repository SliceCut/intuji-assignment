<?php

namespace App\Cores;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Session
{
    /**
     * @param string $key
     * @param mixed $value
     */
    public static function put(string $key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * @param string $key
     * @param mixed $default
     */
    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function delByKey(string $key)
    {
        unset($_SESSION[$key]);
    }

    public static function exists(string $key)
    {
        return (bool) ($_SESSION[$key] ?? null);
    }
}
