<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\UserTypeCommissions\Types\Business;

use CommissionFeeCalculation\Services\Config;
use CommissionFeeCalculation\Services\Math;
use CommissionFeeCalculation\Services\NumberFormat;
use CommissionFeeCalculation\UserTypeCommissions\Contracts\TypeAbstract;

class BusinessDepositType implements TypeAbstract
{
    public function __construct(
        private Math $math,
        private Config $config,
        private NumberFormat $numberFormat,
    ) {
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
        return $this->numberFormat->castToStandartFormat(
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
