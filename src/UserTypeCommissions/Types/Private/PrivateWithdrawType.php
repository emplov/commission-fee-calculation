<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\UserTypeCommissions\Types\Private;

use Carbon\Carbon;
use CommissionFeeCalculation\Entities\UsedFreeCommission;
use CommissionFeeCalculation\Entities\User;
use CommissionFeeCalculation\Exceptions\ScriptException;
use CommissionFeeCalculation\Repositories\UsedCommissionRepository;
use CommissionFeeCalculation\Repositories\UserRepository;
use CommissionFeeCalculation\Services\Config;
use CommissionFeeCalculation\Services\Converter\Converter;
use CommissionFeeCalculation\Services\Math;
use CommissionFeeCalculation\Services\NumberFormat;
use CommissionFeeCalculation\UserTypeCommissions\Contracts\TypeAbstract;

class PrivateWithdrawType implements TypeAbstract
{
    public function __construct(
        private UserRepository $userRepository,
        private UsedCommissionRepository $usedCommissionRepository,
        private Config $config,
        private Converter $convert,
        private Math $math,
        private NumberFormat $numberFormat,
    ) {
    }

    public function type(): string
    {
        return 'private_withdraw';
    }

    /**
     * {@inheritDoc}
     */
    public function handle(int $userKey, string $amount, string $currency, string $date, int $decimalsCount): string
    {
        // Get user
        $user = $this->userRepository->find($userKey);

        // Check is user not null
        if (!$user) {
            throw new \Exception("User [$userKey] not found.");
        }

        // Make Carbon object of withdrawal date
        $withdrawalDate = Carbon::make($date);

        // Get withdrawal date week's monday date
        $monday = $withdrawalDate->startOfWeek();

        // Get withdrawal date week's sunday date
        $sunday = $withdrawalDate->endOfWeek();

        // Get this week used free fee amount
        $usedWeeklyFreeFeeAmount = $this->getUsedWeeklyFreeAmount($user, $monday, $sunday, $decimalsCount);

        $weeklyFreeFeeAmount = $this->config->get('commissions.private.withdraw.weekly_free_fee_amount');

        $convertedAmount = $this->convert->convert($amount, $currency);

        // Remove this week used free fee from amount
        $amountToCharge = $this->removeFreeAmountFee($amount, $currency, $convertedAmount, $usedWeeklyFreeFeeAmount, $weeklyFreeFeeAmount);

        // Get used free fee
        $usedFreeFee = $this->getUsedFreeFee($usedWeeklyFreeFeeAmount, $weeklyFreeFeeAmount, $convertedAmount);

        // Save used free fee
        $usedCommission = new UsedFreeCommission(
            $user->getUserID(),
            $this->type(),
            $date,
            $monday->format('Y-m-d'),
            $sunday->format('Y-m-d'),
            $this->numberFormat->roundNumber($usedFreeFee, $decimalsCount),
        );

        $usedCommissionId = $this->usedCommissionRepository->save($usedCommission);

        // Add to users used_commissions_list
        $user->addUsedFreeFeeCommission($this->type(), $usedCommissionId);

        // Save user
        $this->userRepository->save($user);

        // Formula
        // amountToCharge * {commissions.private.withdraw.percent} / 100
        return $this->numberFormat->castToStandartFormat(
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

    private function getUsedFreeFee(string $usedWeeklyFreeFeeAmount, string $weeklyFreeFeeAmount, string $convertedAmount): string
    {
        if ($usedWeeklyFreeFeeAmount > $weeklyFreeFeeAmount) {
            $freeFee = '0';
        } elseif ($convertedAmount <= $this->math->sub($weeklyFreeFeeAmount, $usedWeeklyFreeFeeAmount)) {
            $freeFee = $convertedAmount;
        } else {
            $freeFee = $this->math->sub($weeklyFreeFeeAmount, $usedWeeklyFreeFeeAmount);
        }

        return $freeFee;
    }

    private function removeFreeAmountFee(
        string $amount,
        string $currency,
        string $convertedAmount,
        string $usedWeeklyFreeFeeAmount,
        string $weeklyFreeFeeAmount,
    ): string {
        // Amount that is going to be charged
        $amountToCharge = $amount;

        // If used weekly free amount was exceeded
        // then charge commission from whole amount
        if ($usedWeeklyFreeFeeAmount >= $weeklyFreeFeeAmount) {
            return $amountToCharge;
        }

        // If not exceeded

        // Get not used weekly free fee
        $notUsedWeeklyFreeFee = $this->math->sub($weeklyFreeFeeAmount, $usedWeeklyFreeFeeAmount);

        // If amount less or equal to not used weekly free fee
        if ((float) $convertedAmount <= (float) $notUsedWeeklyFreeFee) {
            // Then not charge commission from amount
            $amountToCharge = '0';
        } else {
            // if amount more than not used weekly free fee
            // then minus not used weekly free fee from amount
            $amountToCharge = $this->math->sub(
                $amountToCharge,
                $this->math->multiply(
                    $notUsedWeeklyFreeFee,
                    $this->math->convertFloat($this->convert->getRate($currency)),
                ),
            );
        }

        // Return amount to charge which is used in commission calculating
        return $amountToCharge;
    }

    private function getUsedWeeklyFreeAmount(User $user, Carbon $monday, Carbon $sunday, int $decimalsCount): string
    {
        $usedWeeklyFreeFeeAmount = '0';

        if ($user->hasUsedFreeFeeCommissions($this->type())) {
            /* @var UsedFreeCommission $usedCommission */
            foreach ($user->getUsedCommissionsByType($this->type()) as $usedCommissionId) {
                $usedCommission = $this->usedCommissionRepository->find($usedCommissionId);

                if (!$usedCommission) {
                    throw new ScriptException('Commission with '.$usedCommissionId.' not exists.');
                }

                if (
                    $usedCommission->getWeekStartDate() === $monday->format('Y-m-d')
                    && $usedCommission->getWeekEndDate() === $sunday->format('Y-m-d')
                ) {
                    $usedWeeklyFreeFeeAmount = $this->math->add(
                        $usedWeeklyFreeFeeAmount,
                        $usedCommission->getFreeAmount(),
                        $decimalsCount,
                    );
                }
            }
        }

        return $usedWeeklyFreeFeeAmount;
    }
}
