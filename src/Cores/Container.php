<?php

namespace App\Cores;

use Exception;
use ReflectionClass;

class Container
{
    private static $instance = null;
    private $instances = [];
    private $bindings = [];
    public $singletons = [];

    // The single method to get the instance of the class.
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Container;
        }

        return self::$instance;
    }

    public function bind($key, $resolver) {
        $this->bindings[$key] = $resolver;
    }

    public function singleton($key, $resolver) {
        $this->singletons[$key] = $resolver;
    }

    public function get($class)
    {
        if (!isset($this->instances[$class])) {
            $this->instances[$class] = $this->resolve($class);
        }

        return $this->instances[$class];
    }

    public function make($key) {
        if (isset($this->singletons[$key])) {
            if (is_callable($this->singletons[$key])) {
                $this->singletons[$key] = $this->singletons[$key]();
            } elseif (is_string($this->singletons[$key])) {
                $this->singletons[$key] = $this->build($this->singletons[$key]);
            }
            return $this->singletons[$key];
        }

        return $this->get($key);
    }

    private function resolve($class)
    {
        $reflectionClass = new ReflectionClass($class);

        $constructor = $reflectionClass->getConstructor();
        if (!$constructor) {
            return new $class();
        }

        $parameters = $constructor->getParameters();
        $dependencies = $this->resolveDependencies($parameters);

        return $reflectionClass->newInstanceArgs($dependencies);
    }

    private function resolveDependencies($parameters)
    {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();
            if ($type && !$type->isBuiltin()) {
                $dependencies[] = $this->get($type->getName());
            } else {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new Exception("Cannot resolve dependency {$parameter->name}");
                }
            }
        }

        return $dependencies;
    }

    private function build($class) {
        $reflector = new ReflectionClass($class);

        if (!$reflector->isInstantiable()) {
            throw new Exception("Class {$class} is not instantiable");
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return new $class;
        }

        $parameters = $constructor->getParameters();
        $dependencies = array_map(function($parameter) {
            if ($parameter->getClass()) {
                return $this->build($parameter->getClass()->name);
            }

            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }

            throw new Exception("Unable to resolve dependency: {$parameter->name}");
        }, $parameters);

        return $reflector->newInstanceArgs($dependencies);
    }
}
