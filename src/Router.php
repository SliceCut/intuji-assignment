<?php

namespace App;

use App\Cores\Container;
use App\Cores\Request;

class Router
{

    private array $handlers;
    private const METHOD_GET = "GET";
    private const METHOD_POST = "POST";
    private const METHOD_PUT = "PUT";
    private const METHOD_DEL = "DELETE";

    private $request;
    private $middlewareRegistration;
    private $container;

    public function __construct(Container $container, Request $request, MiddlewareRegistration $middlewareRegistration)
    {
        $this->handlers = [];
        $this->container = $container;
        $this->request = $request;
        $this->middlewareRegistration = $middlewareRegistration;
    }

    public function get(string $path, $handler)
    {
        $this->addHandler($path, self::METHOD_GET, $handler);
    }

    public function post(string $path, $handler)
    {
        $this->addHandler($path, self::METHOD_POST, $handler);
    }

    public function put(string $path, $handler)
    {
        $this->addHandler($path, self::METHOD_PUT, $handler);
    }

    public function delete(string $path, $handler)
    {
        $this->addHandler($path, self::METHOD_DEL, $handler);
    }

    public function addHandler(string $path, string $method, $handler)
    {
        $this->handlers[$method . $path] = [
            'path' => $path,
            'method' => $method,
            'handler' => $handler
        ];
    }

    public function run()
    {
        $requestUrl = parse_url($_SERVER['REQUEST_URI']);
        $requestPath = $requestUrl['path'];
        $method = $_SERVER['REQUEST_METHOD'];

        $callback = null;
        foreach ($this->handlers as $handler) {
            if ($handler['path'] == $requestPath && $method == $handler['method']) {
                $callback = $handler['handler'];
            } else if (
                $handler['path'] == $requestPath &&
                $_SERVER['REQUEST_METHOD'] == self::METHOD_POST &&
                (strtolower($this->request->method) ==  strtolower(self::METHOD_PUT) || strtolower($this->request->method) == strtolower(self::METHOD_DEL))
            ) {
                $callback = $handler['handler'];
            }
        }

        if (is_string($callback) || is_array($callback)) {
            $parts = explode("@", is_array($callback) ? $callback['uses'] : $callback);
            $middlewares = is_array($callback) ? ($callback['middleware'] ?? []) : [];

            if (is_array($parts)) {
                $className = array_shift($parts);

                $handler = $this->container->get($className);

                $method = array_shift($parts);

                $callback = [$handler, $method];
            }

            foreach (is_array($middlewares) ? $middlewares : [$middlewares] as $middleware) {
                if (isset($this->middlewareRegistration->getMiddlewareWeb()[$middleware])) {
                    $middlewareClassName = $this->middlewareRegistration->getMiddlewareWeb()[$middleware];
                    $resolveMiddleware = $this->container->get($middlewareClassName);
                    $resolveMiddleware->handle(currentUrl(), $this->request);
                }
            }
        }

        if (!$callback) {
            header('HTTP/1.0 404 Not Found', true, 404);
            exit();
        }

        call_user_func_array($callback, [
            array_merge($_GET, $_POST)
        ]);
    }
}
