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

        $dayOfTheWeek = $withdrawalDate->dayOfWeek;

        $subDays = $dayOfTheWeek - 1;

        $monday = Carbon::make($extra['date']);

        if ($subDays != 0) {
            $monday->subDays($subDays);
        }

        $addDays = 7 - $dayOfTheWeek;

        $sunday = Carbon::make($extra['date'])->addDays();

        if ($addDays) {
            $sunday->addDays($addDays);
        }

        $sum = 0;

        if (isset(self::$commissions[$userKey])) {
            foreach (self::$commissions[$userKey] as $commission) {
                if (Commission::$data[$userKey]['user_id'] == '1') {
                    var_dump(
                        $commission['start_date'],
                        $commission['end_date'],
                        $commission['withdrawal_date'],
                        $commission['amount'],
                        $commission['free_amount'],
                    );
//                    var_dump($monday->format('Y-m-d'), $sunday->format('Y-m-d'));
                    echo '-------------' . PHP_EOL;
                }

                if (
                    $commission['start_date'] == $monday->format('Y-m-d') &&
                    $commission['end_date'] == $sunday->format('Y-m-d')
                ) {
                    $sum += $commission['free_amount'];
                }
            }
        }

//        if (Commission::$data[$userKey]['user_id'] == '1') {
//            var_dump($amount, $currency, $sum, $withdrawalDate->format('Y-m-d'));
//            echo '----------' . PHP_EOL;
//        }

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

        $res = self::castToStandartFormat(($amountToCharge * 0.3 / 100));

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

    /**
     * @param int $userKey
     * @param string $start_date
     * @param string $end_date
     * @return array
     */
    private static function getFromList(int $userKey, string $start_date, string $end_date): array
    {
        $arr = [];

        if (isset(self::$commissions[$userKey])) {
            foreach (self::$commissions[$userKey] as $commission) {
                if ($start_date == $commission['start_date'] && $end_date == $commission['end_date']) {
                    $arr[] = $commission;
                }
            }
        }

        return $arr;
    }

}