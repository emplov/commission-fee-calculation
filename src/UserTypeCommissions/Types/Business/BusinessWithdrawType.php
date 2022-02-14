<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\UserTypeCommissions\Types\Business;

use CommissionFeeCalculation\Repositories\Commission;
use CommissionFeeCalculation\Repositories\User;
use CommissionFeeCalculation\Services\Config;
use CommissionFeeCalculation\Services\Container;
use CommissionFeeCalculation\Services\Math;
use CommissionFeeCalculation\UserTypeCommissions\Contracts\TypeAbstract;

class BusinessWithdrawType extends TypeAbstract
{
    private Commission $commission;

    private User $user;

    private Math $math;

    public function __construct()
    {
        $this->commission = Container::getInstance()->get(Commission::class);
        $this->user = Container::getInstance()->get(User::class);
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
    public function handle(int $userKey, string $amount, string $currency, array $extra = []): void
    {
        $this->commission->addResult(
            self::castToStandartFormat(
                $this->math->divide(
                    $this->math->multiply(
                        $amount,
                        Config::get('commissions.business.withdraw'),
                        $extra['decimals_count'],
                    ),
                    '100',
                    $extra['decimals_count'],
                ),
                $extra['decimals_count'],
            ),
        );

        // Save last withdraw date
        $this->user->users[$userKey]['last_withdraw_date'] = $extra['date'];
    }
}
