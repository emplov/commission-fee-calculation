<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\DTO\Contracts;

use ReflectionProperty;
use ReflectionClass;

abstract class DTOAbstract
{
    public function __construct(...$parameters)
    {
        $class = new ReflectionClass(static::class);

        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
            $property = $reflectionProperty->getName();
            $this->{$property} = $parameters[$property];
        }
    }
}
