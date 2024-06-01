<?php

namespace App\Cores;

class BaseProvider
{
    protected $container;
    protected array $configs = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->register();
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
