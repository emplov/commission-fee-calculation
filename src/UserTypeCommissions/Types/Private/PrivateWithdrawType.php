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

        // Remove this week used free fee from amount
        [$amountToCharge, $freeFee] = $this->removeFreeAmountFee($amount, $currency, $usedWeeklyFreeFeeAmount);

        // Save used free fee
        $usedCommission = new UsedFreeCommission(
            $user->getUserID(),
            self::type(),
            $date,
            $monday->format('Y-m-d'),
            $sunday->format('Y-m-d'),
            $this->numberFormat->roundNumber((string) $freeFee, $decimalsCount),
        );
        $usedCommissionId = $this->usedCommissionRepository->save($usedCommission);

        // Add to users used_commissions_list
        $user->addUsedFreeFeeCommission(self::type(), $usedCommissionId);

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

    private function removeFreeAmountFee(string $amount, string $currency, string $usedWeeklyFreeFeeAmount): array
    {
        // Get weekly free fee amount
        $weeklyFreeFeeAmount = $this->config->get('commissions.private.withdraw.weekly_free_fee_amount');

        // Amount that is going to be charged
        $amountToCharge = $amount;

        // If used weekly free amount was exceeded
        // then charge commission from whole amount
        if ($usedWeeklyFreeFeeAmount >= $weeklyFreeFeeAmount) {
            return [
                $amountToCharge,
                '0',
            ];
        }

        // If not exceeded

        // convert amount to base currency
        $convertedAmount = $this->convert->convert($amountToCharge, $currency);

        // Get not used weekly free fee
        $notUsedWeeklyFreeFee = $this->math->sub($weeklyFreeFeeAmount, $usedWeeklyFreeFeeAmount);

        // If amount less or equal to not used weekly free fee
        if ((float) $convertedAmount <= (float) $notUsedWeeklyFreeFee) {
            // Then not charge commission from amount
            $amountToCharge = '0';
            $feeToCharge = $convertedAmount;
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
            $feeToCharge = $notUsedWeeklyFreeFee;
        }

        // Return amount to charge which is used in commission calculating
        // And used fee from weekly free fee, to save it
        return [
            $amountToCharge,
            $feeToCharge,
        ];
    }

    private function getUsedWeeklyFreeAmount(User $user, Carbon $monday, Carbon $sunday, int $decimalsCount): string
    {
        $usedWeeklyFreeFeeAmount = '0';

        if ($user->hasUsedFreeFeeCommissions(self::type())) {
            /* @var UsedFreeCommission $usedCommission */
            foreach ($user->getUsedCommissionsByType(self::type()) as $usedCommissionId) {
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