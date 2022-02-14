<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Tests;

use CommissionFeeCalculation\Services\Converter\CurrencyConverter;
use CommissionFeeCalculation\Services\Converter\Convert;
use CommissionFeeCalculation\Repositories\Commission;
use CommissionFeeCalculation\Services\Container;
use CommissionFeeCalculation\Repositories\User;
use CommissionFeeCalculation\Services\Config;
use CommissionFeeCalculation\Services\Math;

use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $container = Container::getInstance();

        $container->add(Math::class, new Math());
        $converter = new CurrencyConverter();

        $converter->setRate('EUR', 1);
        $converter->setRate('USD', 1.1497);
        $converter->setRate('JPY', 129.53);

        $container->add(Convert::class, $converter);
        $container->add(User::class, new User());
        $container->add(Commission::class, new Commission());

        Config::setConfig(include 'src/config.php');
    }
}