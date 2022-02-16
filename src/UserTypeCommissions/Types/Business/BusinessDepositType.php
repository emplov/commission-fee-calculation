<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\UserTypeCommissions\Types\Business;

use CommissionFeeCalculation\Services\Config;
use CommissionFeeCalculation\Services\Container;
use CommissionFeeCalculation\Services\Math;
use CommissionFeeCalculation\UserTypeCommissions\Contracts\TypeAbstract;

class BusinessDepositType extends TypeAbstract
{
    private Math $math;

    private Config $config;

    public function __construct()
    {
        $this->math = Container::getInstance()->get(Math::class);
        $this->config = Container::getInstance()->get(Config::class);
    }

    /**
     * {@inheritDoc}
     */
    public static function type(): string
    {
        return 'business_deposit';
    }

    /**
     * {@inheritDoc}
     */
    public function handle(int $userKey, string $amount, string $currency, string $date, int $decimalsCount): string
    {
        return $this->castToStandartFormat(
            $this->math->divide(
                $this->math->multiply(
                    $amount,
                    $this->config->get('commissions.business.deposit'),
                    $decimalsCount,
                ),
                '100',
                $decimalsCount,
            ),
            $decimalsCount,
        );
    }
}
