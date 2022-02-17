<?php

declare(strict_types=1);

use CommissionFeeCalculation\Repositories\Persistence\InMemoryPersistence;
use CommissionFeeCalculation\Repositories\Persistence\Persistence;
use CommissionFeeCalculation\Repositories\UserRepository;
use CommissionFeeCalculation\Services\Commission;
use CommissionFeeCalculation\Services\Config;
use CommissionFeeCalculation\Services\Converter\Convert;
use CommissionFeeCalculation\Services\Converter\CurrencyConverter;
use CommissionFeeCalculation\Services\Math;
use Psr\Container\ContainerInterface;

return [
    Math::class => new Math(),

    Persistence::class => static function (ContainerInterface $container) {
        return new InMemoryPersistence();
    },

    UserRepository::class => static function (ContainerInterface $container) {
        return new UserRepository($container->get(Persistence::class));
    },

    Config::class => static function (ContainerInterface $container) {
        $config = new Config();
        $config->setConfig(include 'config.php');

        return $config;
    },

    Convert::class => static function (ContainerInterface $container) {
        $converter = new CurrencyConverter();
        $converter->fetchRates();

        return $converter;
    },

    Commission::class => static function (ContainerInterface $container) {
        return new Commission($container->get(UserRepository::class), $container->get(Config::class));
    },
];