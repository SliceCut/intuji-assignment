<?php

namespace App\Cores;

use Josantonius\Request\Request as RequestRequest;

class Request
{
    private $data = [];

    public function __construct()
    {
        $this->setData();
    }

    public function all()
    {
        return $this->data;
    }

    public function put(string $key, $data)
    {
        $this->data[$key] = $data;
    }

    public function get(string $key, $default = null)
    {
        return RequestRequest::get('GET')[$key] ?? $default;
    }

    public function input(string $key, $default = null)
    {
        return RequestRequest::get('POST')[$key] ?? $default;
    }

    public function merge(array $data)
    {
        $this->data = array_merge($this->data, $data);
    }

    public function file($key)
    {
        return $_FILES[$key];
    }

    public function hasFile($key)
    {
        $file = ($_FILES[$key] ?? null);

        return (bool)($file['name'] ?? null);
    }

    private function setData()
    {
        if ($_SERVER["REQUEST_METHOD"] == 'GET') {
            $this->data = RequestRequest::get('GET');
        }

        if ($_SERVER["REQUEST_METHOD"] == 'POST') {
            $this->data = RequestRequest::post('POST');
        }

        if ($_SERVER["REQUEST_METHOD"] == 'PUT') {
            $this->data = RequestRequest::put('PUT');
        }

        if ($_SERVER["REQUEST_METHOD"] == 'DELETE') {
            $this->data = RequestRequest::del('DELETE');
        }
    }

    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }
}
