<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\DTO;

class CommissionDTO
{
    public function __construct(
        public int $userKey,
        public string $date,
        public string $operationAmount,
        public string $operationCurrency,
        public string $userType,
        public int $decimalsCount,
    ) {
    }
}
