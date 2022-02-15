<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\UserTypeCommissions\Contracts;

use CommissionFeeCalculation\Services\Container;
use CommissionFeeCalculation\Services\Math;

abstract class TypeAbstract
{
    /**
     * Get user's type handler.
     */
    abstract public static function type(): string;

    /**
     * Handler.
     */
    abstract public function handle(int $userKey, string $amount, string $currency, string $date, int $decimalsCount): void;

    public function roundNumber(string $amount, int $decimalsCount = 2): string|float
    {
        /** @var Math $math */
        $math = Container::getInstance()->get(Math::class);

        if ($decimalsCount === 0) {
            $amount = $math->bcceil($amount);
        } else {
            $amount = $math->bcround(
                $amount,
                $decimalsCount,
            );
        }

        return $amount;
    }

    public function castToStandartFormat(string $amount, int $decimalsCount = 2): string
    {
        return number_format(
            (float) $this->roundNumber($amount, $decimalsCount),
            $decimalsCount,
            '.',
            '',
        );
    }
}
