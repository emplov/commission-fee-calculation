<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\UserTypeCommissions\Types\Privete;

use Carbon\Carbon;
use CommissionFeeCalculation\Services\Config;
use CommissionFeeCalculation\Services\Container;
use CommissionFeeCalculation\Services\Converter\Convert;
use CommissionFeeCalculation\Services\Math;
use CommissionFeeCalculation\UserTypeCommissions\Contracts\TypeAbstract;

class PrivateWithdrawType extends TypeAbstract
{
    public static array $commissions = [];

    private Convert $convert;

    private Math $math;

    private Config $config;

    public function __construct()
    {
        $this->config = Container::getInstance()->get(Config::class);
        $this->convert = Container::getInstance()->get(Convert::class);
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
    public function handle(int $userKey, string $amount, string $currency, string $date, int $decimalsCount): string
    {
        $withdrawalDate = Carbon::make($date);

        $monday = $withdrawalDate->startOfWeek();

        $sunday = $withdrawalDate->endOfWeek();

        $usedWeekFreeFeeAmount = '0';

        if (isset(self::$commissions[$userKey])) {
            foreach (self::$commissions[$userKey] as $commission) {
                if (
                    $commission['start_date'] === $monday->format('Y-m-d') &&
                    $commission['end_date'] === $sunday->format('Y-m-d')
                ) {
                    $usedWeekFreeFeeAmount = $this->math->add(
                        $usedWeekFreeFeeAmount,
                        $commission['free_amount'],
                        $decimalsCount,
                    );
                }
            }
        }

        [$convertedAmount, $amountToCharge, $freeFee] = $this->removeFreeAmountFee($amount, $currency, $usedWeekFreeFeeAmount);

        self::$commissions[$userKey][] = [
            'withdrawal_date' => $withdrawalDate->format('Y-m-d'),
            'start_date' => $monday->format('Y-m-d'),
            'end_date' => $sunday->format('Y-m-d'),
            'amount' => $amount,
            'amount_in_eur' => $convertedAmount,
            'free_amount' => $this->roundNumber((string) $freeFee, $decimalsCount),
            'currency' => $currency,
        ];

        return $this->castToStandartFormat(
            $this->math->divide(
                $this->math->multiply(
                    $amountToCharge,
                    $this->config->get('commissions.private.withdraw.percent'),
                ),
                '100',
            ),
            $decimalsCount,
        );
    }

    private function removeFreeAmountFee(string $amount, string $currency, string $usedWeekFreeFeeAmount): array
    {
        $convertedAmount = $this->convert->convert($amount, $currency);

        $weekFreeFeeAmount = $this->config->get('commissions.private.withdraw.week_free_fee_amount');

        if ($usedWeekFreeFeeAmount >= $weekFreeFeeAmount) {
            return [
                $convertedAmount,
                $amount,
                '0',
            ];
        }

        $freeFee = $this->math->sub($weekFreeFeeAmount, $usedWeekFreeFeeAmount);

        if ((float) $convertedAmount <= (float) $freeFee) {
            $amount = '0';
            $feeToChargeInEur = $convertedAmount;
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
            $convertedAmount,
            $amount,
            $feeToChargeInEur,
        ];
    }
}
