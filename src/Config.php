<?php

namespace App;

class Config
{
    private array $config;

    public function __construct(array $env)
    {
        $this->config = [
            'db' => [
                'driver' => $env['DB_DRIVER'],
                'host' => $env['DB_HOST'] ?? 'localhost',
                'port' => $env['DB_PORT'],
                'database' => $env['DB_NAME'],
                'username' => $env['DB_USERNAME'],
                'password' => $env['DB_PASSWORD'],
            ]
        ];
    }

    public function __get($name)
    {
        return $this->config[$name];
    }
}
