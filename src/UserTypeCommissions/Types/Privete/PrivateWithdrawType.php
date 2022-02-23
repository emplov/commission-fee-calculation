<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\UserTypeCommissions\Types\Privete;

use Carbon\Carbon;
use CommissionFeeCalculation\Entities\UsedCommission;
use CommissionFeeCalculation\Repositories\UserRepository;
use CommissionFeeCalculation\Services\Config;
use CommissionFeeCalculation\Services\Converter\Convert;
use CommissionFeeCalculation\Services\Math;
use CommissionFeeCalculation\UserTypeCommissions\Contracts\TypeAbstract;

class PrivateWithdrawType extends TypeAbstract
{
    private Convert $convert;

    private Math $math;

    private Config $config;

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository, Config $config, Convert $convert, Math $math)
    {
        $this->userRepository = $userRepository;
        $this->config = $config;
        $this->convert = $convert;
        $this->math = $math;
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
        $user = $this->userRepository->find($userKey);

        if (!$user) {
            throw new \Exception("User [$userKey] not found.");
        }

        $withdrawalDate = Carbon::make($date);

        $monday = $withdrawalDate->startOfWeek();

        $sunday = $withdrawalDate->endOfWeek();

        $usedWeekFreeFeeAmount = '0';

        if ($user->hasTransactions(self::type())) {
            /** @var UsedCommission $commission */
            foreach ($user->getTransactionsByType(self::type()) as $commission) {
                if (
                    $commission->getWeekStartDate() === $monday->format('Y-m-d') &&
                    $commission->getWeekEndDate() === $sunday->format('Y-m-d')
                ) {
                    $usedWeekFreeFeeAmount = $this->math->add(
                        $usedWeekFreeFeeAmount,
                        $commission->getFreeAmount(),
                        $decimalsCount,
                    );
                }
            }
        }

        [$amountToCharge, $freeFee] = $this->removeFreeAmountFee($amount, $currency, $usedWeekFreeFeeAmount);

        $user->addTransaction(self::type(), new UsedCommission(
            $date,
            $monday->format('Y-m-d'),
            $sunday->format('Y-m-d'),
            $this->roundNumber((string) $freeFee, $decimalsCount),
        ));

        $this->userRepository->save($user);

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
            $amount,
            $feeToChargeInEur,
        ];
    }
}
