<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Services;

use Exception;
use Psr\Container\ContainerInterface;

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

        if ($value instanceof \Closure) {
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
}
