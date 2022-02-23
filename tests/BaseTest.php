<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Tests;

use CommissionFeeCalculation\Repositories\Persistence\InMemoryPersistence;
use CommissionFeeCalculation\Repositories\Persistence\Persistence;
use CommissionFeeCalculation\Repositories\UserRepository;
use CommissionFeeCalculation\Services\Commission;
use CommissionFeeCalculation\Services\Config;
use CommissionFeeCalculation\Services\Container;
use CommissionFeeCalculation\Services\Converter\Converter;
use CommissionFeeCalculation\Services\Converter\CurrencyConverter;
use CommissionFeeCalculation\Services\Math;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

abstract class BaseTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $container = Container::getInstance();

        $container->addDefinitions([
            Math::class => new Math(),

            Persistence::class => static function (ContainerInterface $container) {
                return new InMemoryPersistence();
            },

            UserRepository::class => static function (ContainerInterface $container) {
                return new UserRepository($container->get(Persistence::class));
            },

            Config::class => static function (ContainerInterface $container) {
                $config = new Config();
                $config->setConfig(include __DIR__.'./../src/config.php');

                return $config;
            },

            Converter::class => static function (ContainerInterface $container) {
                $converter = new CurrencyConverter();

                $converter->setRate('EUR', 1);
                $converter->setRate('USD', 1.1497);
                $converter->setRate('JPY', 129.53);

                return $converter;
            },

            Commission::class => static function (ContainerInterface $container) {
                return new Commission($container->get(UserRepository::class), $container->get(Config::class));
            },
        ]);
    }
}
