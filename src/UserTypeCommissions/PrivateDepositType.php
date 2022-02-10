<?php

namespace CommissionFeeCalculation\UserTypeCommissions;

use CommissionFeeCalculation\Models\Commission;
use CommissionFeeCalculation\UserTypeCommissions\Contracts\TypeAbstract;

class PrivateDepositType extends TypeAbstract
{
    /**
     * @inheritDoc
     */
    public static function type(): string
    {
        return 'private_deposit';
    }

    /**
     * @inheritDoc
     */
    public static function handle(int $userKey, float $amount, string $currency, array $extra = []): void
    {
        Commission::addResult(self::castToStandartFormat(($amount * config('private_deposit_percent') / 100)));

        // Save last deposit date
        Commission::$data[$userKey]['last_deposit_date'] = $extra['date'];
    }
}