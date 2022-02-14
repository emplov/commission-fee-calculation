<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\UserTypeCommissions\Contracts;

use CommissionFeeCalculation\Services\Container;
use CommissionFeeCalculation\Services\Math;

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
     * @param string $amount
     * @param string $currency
     * @param array $extra
     * @return void
     */
    abstract public function handle(int $userKey, string $amount, string $currency, array $extra = []): void;

    /**
     * @param string $amount
     * @param int $decimalsCount
     * @return string|float
     */
    public function roundNumber(string $amount, int $decimalsCount = 2): string|float
    {
        /** @var Math $math */
        $math = Container::getInstance()->get(Math::class);

        if ($decimalsCount == 0) {
            $amount = $math->bcceil($amount);
        } else {
            $amount = $math->bcround(
                $amount,
                $decimalsCount,
            );
        }

        return $amount;
    }

    /**
     * @param string $amount
     * @param int $decimalsCount
     * @return string
     */
    public function castToStandartFormat(string $amount, int $decimalsCount = 2): string
    {
        return number_format(
            floatval(self::roundNumber($amount, $decimalsCount)),
            $decimalsCount,
            '.',
            '',
        );
    }
}
