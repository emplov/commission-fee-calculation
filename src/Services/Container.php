<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Services;

use Closure;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionException;

class Container implements ContainerInterface
{
    private static ?self $_instance = null;

    private array $definitions = [];

    private array $resolvedEntries = [];

    public static function getInstance(): self
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function get(string $id)
    {
        if (!$this->has($id)) {
            throw new Exception("No entry or class found for '$id'");
        }

        if (array_key_exists($id, $this->resolvedEntries)) {
            return $this->resolvedEntries[$id];
        }

        $value = $this->definitions[$id];

        if ($value instanceof Closure) {
            $value = $value($this);
        }

        $this->resolvedEntries[$id] = $value;

        return $value;
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->definitions) || array_key_exists($id, $this->resolvedEntries);
    }

    public function addDefinitions(array $definitions): void
    {
        $this->definitions = array_merge(
            $definitions,
            [ContainerInterface::class => $this]
        );
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws ReflectionException
     * @throws NotFoundExceptionInterface
     */
    public function make(string $class)
    {
        $reflectionClass = new ReflectionClass($class);

        $constructor = $reflectionClass->getConstructor();

        $parameters = [];

        foreach ($constructor->getParameters() as $parameter) {
            if (!$parameter->isOptional()) {
                $parameters[$parameter->getName()] = $this->get($parameter->getType()->getName());
            }
        }

        return new $class(...$parameters);
    }
}
