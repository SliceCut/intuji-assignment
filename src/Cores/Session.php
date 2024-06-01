<?php

namespace App\Cores;

session_start();

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

    public static function clearAll()
    {
        return session_destroy();
    }

    public static function exists(string $key)
    {
        return (bool) ($_SESSION[$key] ?? null);
    }
}
