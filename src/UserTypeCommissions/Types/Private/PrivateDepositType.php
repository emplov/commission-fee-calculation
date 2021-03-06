<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\UserTypeCommissions\Types\Private;

use CommissionFeeCalculation\Services\Config;
use CommissionFeeCalculation\Services\Math;
use CommissionFeeCalculation\Services\NumberFormat;
use CommissionFeeCalculation\UserTypeCommissions\Contracts\TypeAbstract;

class PrivateDepositType implements TypeAbstract
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
    public function type(): string
    {
        return 'private_deposit';
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
                    $this->config->get('commissions.private.deposit'),
                    $decimalsCount,
                ),
                '100',
                $decimalsCount,
            ),
            $decimalsCount,
        );
    }
}
