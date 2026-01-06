<?php

namespace App\Core;

use ReflectionClass;
use ReflectionException;

class Container
{
    private array $bindings = [];
    private array $instances = [];

    public function bind(string $abstract, callable $concrete = null)
    {
        $this->bindings[$abstract] = $concrete ?? function ($c) use ($abstract) {
            return $c->resolve($abstract);
        };
    }

    public function singleton(string $abstract, callable $concrete = null)
    {
        $this->bind($abstract, function ($c) use ($abstract, $concrete) {
            if (!isset($this->instances[$abstract])) {
                $resolver = $concrete ?? function ($c) use ($abstract) {
                    return $c->resolve($abstract);
                };
                $this->instances[$abstract] = $resolver($c);
            }
            return $this->instances[$abstract];
        });
    }

    public function make(string $abstract)
    {
        if (isset($this->bindings[$abstract])) {
            return call_user_func($this->bindings[$abstract], $this);
        }

        return $this->resolve($abstract);
    }

    private function resolve(string $class)
    {
        try {
            $reflector = new ReflectionClass($class);
        } catch (ReflectionException $e) {
            throw new \Exception("Class $class does not exist");
        }

        if (!$reflector->isInstantiable()) {
            throw new \Exception("Class $class is not instantiable");
        }

        $constructor = $reflector->getConstructor();

        if (!$constructor) {
            return new $class;
        }

        $parameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();

            if (!$type || $type->isBuiltin()) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new \Exception("Cannot resolve parameter: " . $parameter->getName());
                }
            } else {
                $dependencies[] = $this->make($type->getName());
            }
        }

        return $reflector->newInstanceArgs($dependencies);
    }
}
