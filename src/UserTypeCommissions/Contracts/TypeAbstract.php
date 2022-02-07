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
     * @return string
     */
    protected static function castToStandartFormat(float|int $amount): string
    {
        return number_format(round($amount, 2), 2, '.', '');
    }
}