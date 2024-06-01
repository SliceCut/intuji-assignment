<?php

namespace App;

use PDO;
use PDOException;

/**
 * @property \PDO $pdo
 */
class DB
{
    private PDO $pdo;

    public array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function init()
    {
        if (!isset($this->pdo)) {
            try {
                $defaultOptions = [
                    // PDO::ATTR_PERSISTENT => false,
                    // PDO::ATTR_ERRMODE=> PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ];

                $this->pdo = new PDO(
                    $this->config['driver'] . ':host=' . $this->config['host'] . ';port=' . $this->config['port'] . ';dname=' . $this->config['database'] . ';charset=utf8mb4',
                    $this->config['username'],
                    $this->config['password'],
                    $this->config['options'] ?? $defaultOptions
                );
            } catch (PDOException $e) {
                die($e->getMessage());;
            }
        }
    }

    public function __call($name, $arguments)
    {
        $this->init();
        return call_user_func_array([$this->pdo, $name], $arguments);
    }
}
