<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Services;

use Exception;

use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private static ?self $_instance = null;

    private array $container = [];

    public static function getInstance(): ?Container
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function get(string $id)
    {
        if (!$this->has($id)) {
            throw new Exception('['.$id.'] container not found.');
        }

        return $this->container[$id];
    }

    public function has(string $id): bool
    {
        return isset($this->container[$id]);
    }

    public function add(string $id, mixed $container)
    {
        $this->container[$id] = $container;
    }
}
