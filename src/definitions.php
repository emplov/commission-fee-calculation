<?php

declare(strict_types=1);

use CommissionFeeCalculation\Entities\User;
use CommissionFeeCalculation\Services\Commission;
use CommissionFeeCalculation\Services\Config;
use CommissionFeeCalculation\Services\Converter\Convert;
use CommissionFeeCalculation\Services\Converter\CurrencyConverter;
use CommissionFeeCalculation\Services\Math;
use Psr\Container\ContainerInterface;

return [
    User::class => new User(),
    Config::class => static function (ContainerInterface $container) {
        $config = new Config();
        $config->setConfig(include 'config.php');

        return $config;
    },
    Math::class => new Math(),
    Convert::class => static function (ContainerInterface $container) {
        $converter = new CurrencyConverter();
        $converter->fetchRates();

        return $converter;
    },
    Commission::class => static function (ContainerInterface $container) {
        return new Commission($container->get(User::class), $container->get(Config::class));
    },
];
