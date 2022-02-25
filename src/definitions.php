<?php

declare(strict_types=1);

use CommissionFeeCalculation\Repositories\Persistence\InMemoryPersistence;
use CommissionFeeCalculation\Repositories\UsedCommissionRepository;
use CommissionFeeCalculation\Repositories\UserRepository;
use CommissionFeeCalculation\Services\Commission;
use CommissionFeeCalculation\Services\Config;
use CommissionFeeCalculation\Services\Converter\Converter;
use CommissionFeeCalculation\Services\Converter\CurrencyConverter;
use CommissionFeeCalculation\Services\File;
use CommissionFeeCalculation\Services\Math;
use CommissionFeeCalculation\Services\NumberFormat;
use Psr\Container\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

return [
    Math::class => new Math(),

    File::class => new File(),

    NumberFormat::class => static function (ContainerInterface $container) {
        return new NumberFormat($container->get(Math::class));
    },

    UserRepository::class => static function (ContainerInterface $container) {
        return new UserRepository(new InMemoryPersistence());
    },

    UsedCommissionRepository::class => static function (ContainerInterface $container) {
        return new UsedCommissionRepository(new InMemoryPersistence());
    },

    Config::class => static function (ContainerInterface $container) {
        $config = new Config();

        $config->setConfig(Yaml::parseFile(__DIR__.'/config.yml'));

        return $config;
    },

    Converter::class => static function (ContainerInterface $container) {
        $converter = new CurrencyConverter();
        $converter->fetchRates();

        return $converter;
    },

    Commission::class => static function (ContainerInterface $container) {
        return new Commission($container->get(UserRepository::class), $container->get(Config::class));
    },
];
