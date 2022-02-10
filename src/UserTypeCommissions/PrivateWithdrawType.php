<?php

namespace CommissionFeeCalculation\UserTypeCommissions;

use Carbon\Carbon;

use CommissionFeeCalculation\Models\Commission;
use CommissionFeeCalculation\Models\Currencies;
use CommissionFeeCalculation\UserTypeCommissions\Contracts\TypeAbstract;

class PrivateWithdrawType extends TypeAbstract
{
    public static array $commissions = [];

    /**
     * @inheritDoc
     */
    public static function type(): string
    {
        return 'private_withdraw';
    }

    /**
     * @inheritDoc
     */
    public static function handle(int $userKey, float $amount, string $currency, array $extra = []): void
    {
        $withdrawalDate = Carbon::make($extra['date']);

        $monday = $withdrawalDate->startOfWeek();

        $sunday = $withdrawalDate->endOfWeek();

        $sum = 0;

        if (isset(self::$commissions[$userKey])) {
            foreach (self::$commissions[$userKey] as $commission) {
                if (
                    $commission['start_date'] == $monday->format('Y-m-d') &&
                    $commission['end_date'] == $sunday->format('Y-m-d')
                ) {
                    $sum += $commission['free_amount'];
                }
            }
        }

        [$amountInEur, $amountToCharge, $freeFee] = self::removeFreeAmountFee($amount, $currency, $sum);

        self::$commissions[$userKey][] = [
            'withdrawal_date' => $withdrawalDate->format('Y-m-d'),
            'start_date' => $monday->format('Y-m-d'),
            'end_date' => $sunday->format('Y-m-d'),
            'amount' => $amount,
            'amount_in_eur' => $amountInEur,
            'free_amount' => $freeFee,
            'currency' => $currency,
        ];

        $res = self::castToStandartFormat(($amountToCharge * config('private_withdraw_percent') / 100));

        Commission::addResult($res);

        // Save last withdraw date
        Commission::$data[$userKey]['last_withdraw_date'] = $extra['date'];
    }

    /**
     * @param float $amount
     * @param string $currency
     * @param float $usedWeekFreeFeeAmount
     * @return array
     */
    private static function removeFreeAmountFee(float $amount, string $currency, float $usedWeekFreeFeeAmount): array
    {
        $amountInEur = Currencies::currencyToEur($amount, $currency);

        $weekFreeFeeAmount = config('week_free_fee_amount');

        if ($usedWeekFreeFeeAmount >= $weekFreeFeeAmount) {
            return [
                $amountInEur,
                $amount,
                0,
            ];
        }

        $freeFee = $weekFreeFeeAmount - $usedWeekFreeFeeAmount;

        if ($amountInEur <= $freeFee) {
            $amount = 0;
            $feeToChargeInEur = $amountInEur;
        } else {
            $amount -= ($freeFee * Currencies::$currencies_rate[$currency]);
            $feeToChargeInEur = $freeFee;
        }

        return [
            $amountInEur,
            $amount,
            $feeToChargeInEur,
        ];
    }
}