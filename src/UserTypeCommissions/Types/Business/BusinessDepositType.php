<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\UserTypeCommissions\Types\Business;

use CommissionFeeCalculation\Repositories\Commission;
use CommissionFeeCalculation\Services\Config;
use CommissionFeeCalculation\Services\Container;
use CommissionFeeCalculation\Services\Math;
use CommissionFeeCalculation\UserTypeCommissions\Contracts\TypeAbstract;

class BusinessDepositType extends TypeAbstract
{
    private Commission $commission;

    private Math $math;

    public function __construct()
    {
        $this->commission = Container::getInstance()->get(Commission::class);
        $this->math = Container::getInstance()->get(Math::class);
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
    public function handle(int $userKey, string $amount, string $currency, string $date, int $decimalsCount): void
    {
        $this->commission->addResult(
            $this->castToStandartFormat(
                $this->math->divide(
                    $this->math->multiply(
                        $amount,
                        Config::get('commissions.business.deposit'),
                        $decimalsCount,
                    ),
                    '100',
                    $decimalsCount,
                ),
                $decimalsCount,
            ),
        );
    }
}
