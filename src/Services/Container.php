<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Services;

use Closure;
use CommissionFeeCalculation\Repositories\Persistence\InMemoryPersistence;
use CommissionFeeCalculation\Repositories\TransactionRepository;
use CommissionFeeCalculation\Repositories\UserRepository;
use CommissionFeeCalculation\Services\Converter\Converter;
use CommissionFeeCalculation\Services\Converter\CurrencyConverter;
use CommissionFeeCalculation\UserTypeCommissions\Types\Business\BusinessDepositType;
use CommissionFeeCalculation\UserTypeCommissions\Types\Business\BusinessWithdrawType;
use CommissionFeeCalculation\UserTypeCommissions\Types\Private\PrivateDepositType;
use CommissionFeeCalculation\UserTypeCommissions\Types\Private\PrivateWithdrawType;
use Exception;
use Psr\Container\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

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

        if ($value instanceof Closure) {
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

    public function init(): void
    {
        $this->addDefinitions([
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

                $config->setConfig(Yaml::parseFile(__DIR__.'/../config.yml'));

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
