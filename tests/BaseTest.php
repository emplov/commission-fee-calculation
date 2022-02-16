<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Tests;

use CommissionFeeCalculation\Entities\User;
use CommissionFeeCalculation\Services\Commission;
use CommissionFeeCalculation\Services\Config;
use CommissionFeeCalculation\Services\Container;
use CommissionFeeCalculation\Services\Converter\Convert;
use CommissionFeeCalculation\Services\Converter\CurrencyConverter;
use CommissionFeeCalculation\Services\Math;
use Psr\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $container = Container::getInstance();

        $container->addDefinitions([
            User::class => new User(),
            Config::class => static function (ContainerInterface $container) {
                $config = new Config();
                $config->setConfig(include __DIR__ . './../src/config.php');

                return $config;
            },
            Math::class => new Math(),
            Convert::class => static function (ContainerInterface $container) {
                $converter = new CurrencyConverter();

                $converter->setRate('EUR', 1);
                $converter->setRate('USD', 1.1497);
                $converter->setRate('JPY', 129.53);

                return $converter;
            },
            Commission::class => static function (ContainerInterface $container) {
                return new Commission($container->get(User::class), $container->get(Config::class));
            },
        ]);
    }
}