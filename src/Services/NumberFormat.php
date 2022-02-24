<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Services;

class NumberFormat
{
    public function __construct(
        private Math $math,
    ) {
    }

    /**
     * Round number
     *
     * @param string $amount
     * @param int $decimalsCount
     * @return string
     */
    public function roundNumber(string $amount, int $decimalsCount = 2): string
    {
        if ($decimalsCount === 0) {
            $amount = $this->math->bcceil($amount);
        } else {
            $amount = $this->math->bcround(
                $amount,
                $decimalsCount,
            );
        }

        return $amount;
    }

    /**
     * Cast to commission output style
     *
     * @param string $amount
     * @param int $decimalsCount
     * @return string
     */
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
