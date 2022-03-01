<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\UserTypeCommissions\Types\Business;

use CommissionFeeCalculation\Services\Config;
use CommissionFeeCalculation\Services\Math;
use CommissionFeeCalculation\Services\NumberFormat;
use CommissionFeeCalculation\UserTypeCommissions\Contracts\TypeAbstract;

class BusinessWithdrawType implements TypeAbstract
{
    public function __construct(
        private Config $config,
        private Math $math,
        private NumberFormat $numberFormat,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function type(): string
    {
        return 'business_withdraw';
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
                    $this->config->get('commissions.business.withdraw'),
                    $decimalsCount,
                ),
                '100',
                $decimalsCount,
            ),
            $decimalsCount,
        );
    }
}
