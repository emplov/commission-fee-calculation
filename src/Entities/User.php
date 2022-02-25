<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Entities;

class User
{
    public function __construct(
        private int $userID,
        private string $userType,
        private array $used_free_fee_commission = [],
    ) {
    }

    public function getUserID(): int
    {
        return $this->userID;
    }

    public function getTransactions(): array
    {
        return $this->used_free_fee_commission;
    }

    public function getUserType(): string
    {
        return $this->userType;
    }

    public function addUsedFreeFeeCommission(string $transactionType, int $id): void
    {
        $this->used_free_fee_commission[$transactionType][] = $id;
    }

    public function hasUsedFreeFeeCommissions(string $transactionType): bool
    {
        return isset($this->used_free_fee_commission[$transactionType]);
    }

    public function getUsedCommissionsByType(string $transactionType): array
    {
        return $this->used_free_fee_commission[$transactionType];
    }
}
