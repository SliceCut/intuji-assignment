<?php

namespace App\Cores;

class BaseProvider
{
    // Hold the class instance.
    private static $instance = null;

    protected array $configs = [];

    public function __construct()
    {
        $this->register();
    }

    // The single method to get the instance of the class.
    public static function getInstance()
    {
        if (self::$instance === null) {
            $className = get_called_class();
            self::$instance = new $className;
        }

        return self::$instance;
    }

    public function register(): void
    {
    }

    public function mergeConfigFrom(string $path, string $name)
    {
        if (!isset($this->configs[$name])) {
            $configs = require_once dirname(__DIR__) . "/../" . $path;
            $this->configs[$name] = $configs;
        }
    }

    public function getConfig(string $key, $default = null): mixed
    {
        return array_get($this->configs, $key, $default);
    }
}
