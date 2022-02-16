<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\UserTypeCommissions\Types\Business;

use CommissionFeeCalculation\Services\Config;
use CommissionFeeCalculation\Services\Container;
use CommissionFeeCalculation\Services\Math;
use CommissionFeeCalculation\UserTypeCommissions\Contracts\TypeAbstract;

class BusinessWithdrawType extends TypeAbstract
{
    private Math $math;

    public function __construct()
    {
        $this->math = Container::getInstance()->get(Math::class);
    }

    /**
     * {@inheritDoc}
     */
    public static function type(): string
    {
        return 'business_withdraw';
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
                    Config::get('commissions.business.withdraw'),
                    $decimalsCount,
                ),
                '100',
                $decimalsCount,
            ),
            $decimalsCount,
        );
    }
}
