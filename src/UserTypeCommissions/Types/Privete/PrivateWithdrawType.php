<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\UserTypeCommissions\Types\Privete;

use Carbon\Carbon;
use CommissionFeeCalculation\Repositories\Commission;
use CommissionFeeCalculation\Repositories\User;
use CommissionFeeCalculation\Services\Config;
use CommissionFeeCalculation\Services\Container;
use CommissionFeeCalculation\Services\Converter\Convert;
use CommissionFeeCalculation\Services\Math;
use CommissionFeeCalculation\UserTypeCommissions\Contracts\TypeAbstract;

class PrivateWithdrawType extends TypeAbstract
{
    public static array $commissions = [];

    private Convert $convert;

    private Commission $commission;

    private User $user;

    private Math $math;

    public function __construct()
    {
        $this->commission = Container::getInstance()->get(Commission::class);
        $this->convert = Container::getInstance()->get(Convert::class);
        $this->user = Container::getInstance()->get(User::class);
        $this->math = Container::getInstance()->get(Math::class);
    }

    /**
     * {@inheritDoc}
     */
    public static function type(): string
    {
        return 'private_withdraw';
    }

    /**
     * {@inheritDoc}
     */
    public function handle(int $userKey, string $amount, string $currency, array $extra = []): void
    {
        $withdrawalDate = Carbon::make($extra['date']);

        $monday = $withdrawalDate->startOfWeek();

        $sunday = $withdrawalDate->endOfWeek();

        $sum = '0';

        if (isset(self::$commissions[$userKey])) {
            foreach (self::$commissions[$userKey] as $commission) {
                if (
                    $commission['start_date'] === $monday->format('Y-m-d') &&
                    $commission['end_date'] === $sunday->format('Y-m-d')
                ) {
                    $sum = $this->math->add(
                        $sum,
                        $commission['free_amount'],
                        $extra['decimals_count'],
                    );
                }
            }
        }

        [$amountInEur, $amountToCharge, $freeFee] = $this->removeFreeAmountFee($amount, $currency, $sum);

        self::$commissions[$userKey][] = [
            'withdrawal_date' => $withdrawalDate->format('Y-m-d'),
            'start_date' => $monday->format('Y-m-d'),
            'end_date' => $sunday->format('Y-m-d'),
            'amount' => $amount,
            'amount_in_eur' => $amountInEur,
            'free_amount' => self::roundNumber(strval($freeFee), (int) $extra['decimals_count']),
            'currency' => $currency,
        ];

        $res = self::castToStandartFormat(
            $this->math->divide(
                $this->math->multiply(
                    $amountToCharge,
                    Config::get('commissions.private.withdraw.percent'),
                ),
                '100',
            ),
            $extra['decimals_count'],
        );

        $this->commission->addResult($res);

        // Save last withdraw date
        $this->user->users[$userKey]['last_withdraw_date'] = $extra['date'];
    }

    /**
     * @param int $decimalsCount
     */
    private function removeFreeAmountFee(string $amount, string $currency, string $usedWeekFreeFeeAmount): array
    {
        $amountInEur = $this->convert->convert($amount, $currency);

        $weekFreeFeeAmount = Config::get('commissions.private.withdraw.week_free_fee_amount');

        if ($usedWeekFreeFeeAmount >= $weekFreeFeeAmount) {
            return [
                $amountInEur,
                $amount,
                0,
            ];
        }

        $freeFee = $this->math->sub($weekFreeFeeAmount, $usedWeekFreeFeeAmount);

        if ($amountInEur <= $freeFee) {
            $amount = '0';
            $feeToChargeInEur = $amountInEur;
        } else {
            $amount = $this->math->sub(
                $amount,
                $this->math->multiply(
                    $freeFee,
                    $this->math->convertFloat($this->convert->getRate($currency)),
                ),
            );
            $feeToChargeInEur = $freeFee;
        }

        return [
            $amountInEur,
            $amount,
            $feeToChargeInEur,
        ];
    }
}
