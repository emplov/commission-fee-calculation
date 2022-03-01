<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Tests;

use CommissionFeeCalculation\Repositories\Persistence\InMemoryPersistence;
use CommissionFeeCalculation\Repositories\TransactionRepository;
use CommissionFeeCalculation\Repositories\UserRepository;
use CommissionFeeCalculation\Services\Commission;
use CommissionFeeCalculation\Services\Config;
use CommissionFeeCalculation\Services\Container;
use CommissionFeeCalculation\Services\Converter\Converter;
use CommissionFeeCalculation\Services\Converter\CurrencyConverter;
use CommissionFeeCalculation\Services\File;
use CommissionFeeCalculation\Services\Math;
use CommissionFeeCalculation\Services\NumberFormat;
use CommissionFeeCalculation\UserTypeCommissions\Types\Business\BusinessDepositType;
use CommissionFeeCalculation\UserTypeCommissions\Types\Business\BusinessWithdrawType;
use CommissionFeeCalculation\UserTypeCommissions\Types\Private\PrivateDepositType;
use CommissionFeeCalculation\UserTypeCommissions\Types\Private\PrivateWithdrawType;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

abstract class BaseTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $container = Container::getInstance();

        $container->addDefinitions([
            Math::class => new Math(),

            File::class => new File(),

            NumberFormat::class => static function (ContainerInterface $container) {
                return new NumberFormat($container->get(Math::class));
            },

            UserRepository::class => static function (ContainerInterface $container) {
                return new UserRepository(new InMemoryPersistence());
            },

            TransactionRepository::class => static function (ContainerInterface $container) {
                return new TransactionRepository(new InMemoryPersistence());
            },

            Config::class => static function (ContainerInterface $container) {
                $config = new Config();

                $config->setConfig(Yaml::parseFile(__DIR__.'/../src/config.yml'));

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

            BusinessDepositType::class => static function (ContainerInterface $container) {
                return new BusinessDepositType(
                    $container->get(Math::class),
                    $container->get(Config::class),
                    $container->get(NumberFormat::class),
                );
            },

            BusinessWithdrawType::class => static function (ContainerInterface $container) {
                return new BusinessWithdrawType(
                    $container->get(Math::class),
                    $container->get(Config::class),
                    $container->get(NumberFormat::class),
                );
            },

            PrivateDepositType::class => static function (ContainerInterface $container) {
                return new PrivateDepositType(
                    $container->get(Math::class),
                    $container->get(Config::class),
                    $container->get(NumberFormat::class),
                );
            },

            PrivateWithdrawType::class => static function (ContainerInterface $container) {
                return new PrivateWithdrawType(
                    $container->get(UserRepository::class),
                    $container->get(TransactionRepository::class),
                    $container->get(Config::class),
                    $container->get(Converter::class),
                    $container->get(Math::class),
                    $container->get(NumberFormat::class),
                );
            },
        ]);
    }
}
