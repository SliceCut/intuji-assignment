<?php

namespace App\Cores;

use Exception;
use ReflectionClass;

class Container
{
    private $instances = [];

    public function get($class)
    {
        if (!isset($this->instances[$class])) {
            $this->instances[$class] = $this->resolve($class);
        }

        return $this->instances[$class];
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
}
