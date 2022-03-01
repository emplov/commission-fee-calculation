<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Entities;

class User
{
    public function __construct(
        private int $userID,
        private string $userType,
        private array $usedFreeFeeCommission = [],
    ) {
    }

    public function getUserID(): int
    {
        return $this->userID;
    }

    public function getTransactions(): array
    {
        return $this->usedFreeFeeCommission;
    }

    public function getUserType(): string
    {
        return $this->userType;
    }

    public function addUsedFreeFeeCommission(string $transactionType, int $id): void
    {
        $this->usedFreeFeeCommission[$transactionType][] = $id;
    }

    public function hasUsedFreeFeeCommissions(string $transactionType): bool
    {
        return isset($this->usedFreeFeeCommission[$transactionType]);
    }

    public function getUsedCommissionsByType(string $transactionType): array
    {
        return $this->usedFreeFeeCommission[$transactionType];
    }
}
