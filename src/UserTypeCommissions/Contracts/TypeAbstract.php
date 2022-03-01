<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\UserTypeCommissions\Contracts;

interface TypeAbstract
{
    /**
     * Get user's type handler.
     */
    public function type(): string;

    /**
     * Handler.
     */
    public function handle(int $userKey, string $amount, string $currency, string $date, int $decimalsCount): string;
}
