<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Repositories;

class Transaction
{
    public const TYPE_WITHDRAWAL = 'withdrawal';

    public const TYPE_DEPOSIT = 'deposit';

    public function __construct(
        public string $date,
        public string $type,
        public string $amount,
        public string $currency,
    ) {
    }
}
