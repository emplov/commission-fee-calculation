<?php

namespace CommissionFeeCalculation\UserTypeCommissions;

use CommissionFeeCalculation\Models\Commission;
use CommissionFeeCalculation\UserTypeCommissions\Contracts\TypeAbstract;

class BusinessWithdrawType extends TypeAbstract
{
    /**
     * @inheritDoc
     */
    public static function type(): string
    {
        return 'business_withdraw';
    }

    /**
     * @inheritDoc
     */
    public static function handle(int $userKey, float $amount, string $currency, array $extra = []): void
    {
        Commission::addResult(self::castToStandartFormat(($amount * 0.5 / 100)));

        // Save last withdraw date
        Commission::$data[$userKey]['last_withdraw_date'] = $extra['date'];
    }
}