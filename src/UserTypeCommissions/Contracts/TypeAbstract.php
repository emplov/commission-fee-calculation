<?php

namespace CommissionFeeCalculation\UserTypeCommissions\Contracts;

abstract class TypeAbstract
{
    /**
     * Get user's type handler
     *
     * @return string
     */
    abstract public static function type(): string;

    /**
     * Handler
     *
     * @param int $userKey
     * @param float $amount
     * @param string $currency
     * @param array $extra
     * @return void
     */
    abstract public static function handle(int $userKey, float $amount, string $currency, array $extra = []): void;

    /**
     * @param float|int $amount
     * @param int $decimalsCount
     * @return string
     */
    public static function roundNumber(float|int $amount, int $decimalsCount = 2): string
    {
        if ($decimalsCount == 0) {
            $amount = ceil($amount);
        } else {
            $amount = round(
                $amount,
                $decimalsCount,
            );
        }

        return $amount;
    }

    /**
     * @param float|int $amount
     * @param int $decimalsCount
     * @return string
     */
    public static function castToStandartFormat(float|int $amount, int $decimalsCount = 2): string
    {
        return number_format(
            self::roundNumber($amount, $decimalsCount),
            $decimalsCount,
            '.',
            '',
        );
    }
}