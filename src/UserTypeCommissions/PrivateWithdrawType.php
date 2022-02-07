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
        $lastWithdrawDate = Commission::$data[$userKey]['last_withdraw_date'];

        $withdrawalDate = Carbon::make($extra['date']);

        $amountToCharge = $amount;

        $dayOfTheWeek = $withdrawalDate->dayOfWeek;

        self::getFromList($userKey, $amount, $currency);

        $monday = Carbon::make($extra['date'])->subDays($dayOfTheWeek - 1);
        $sunday = Carbon::make($extra['date'])->addDays(7 - $dayOfTheWeek);

        foreach (self::$commissions[$userKey] as $commission) {
            $sum = 0;

            if (
                $commission['start_date'] == $monday->format('Y-m-d') &&
                $commission['end_date'] == $sunday->format('Y-m-d')
            ) {

            }
        }

        if (!is_null($lastWithdrawDate) && !$withdrawalDate->isSameWeek($lastWithdrawDate)) {
            [$amountInEur, $amountToCharge, $freeFee] = self::removeFreeAmountFee($amount, $currency);
        } elseif (is_null($lastWithdrawDate)) {
            [$amountInEur, $amountToCharge, $freeFee] = self::removeFreeAmountFee($amount, $currency);
        } else {
            $amountInEur = Currencies::currencyToEur($amount, $currency);
        }

        self::$commissions[$userKey][] = [
            'withdrawal_date' => $withdrawalDate->format('Y-m-d'),
            'start_date' => $monday->format('Y-m-d'),
            'end_date' => $sunday->format('Y-m-d'),
            'amount' => $amount,
            'amount_in_eur' => $amountInEur,
            'free_amount' => $freeFee,
            'currency' => $currency,
        ];

        Commission::addResult(self::castToStandartFormat(($amountToCharge * 0.3 / 100)));

        // Save last withdraw date
        Commission::$data[$userKey]['last_withdraw_date'] = $extra['date'];
    }

    /**
     * @param float $amount
     * @param string $currency
     * @return array
     */
    private static function removeFreeAmountFee(float $amount, string $currency): array
    {
        $amountInEur = Currencies::currencyToEur($amount, $currency);

        if ($amountInEur > 1000) {
            $amount -= (1000 * Currencies::$currencies_rate[$currency]);
            $feeToChargeInEur = (1000);
        } else {
            $feeToChargeInEur = $amountInEur;
            $amount = 0;
        }

        return [
            $amountInEur,
            $amount,
            $feeToChargeInEur,
        ];
    }

    /**
     * @param int $userKey
     * @param string $start_date
     * @param string $end_date
     * @return array
     */
    private static function getFromList(int $userKey, string $start_date, string $end_date): array
    {
        $arr = [];

        foreach (self::$commissions[$userKey] as $commission) {
            if ($start_date == $commission['start_date'] && $end_date == $commission['end_date']) {
                $arr[] = $commission;
            }
        }

        return $arr;
    }
}