<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Entities;

class User
{
    public function __construct(
        private int $userID,
        private string $userType,
        private array $transactions = [],
    ) {
    }

    public function getUserID(): int
    {
        return $this->userID;
    }

    public function getTransactions(): array
    {
        return $this->transactions;
    }

    public function getUserType(): string
    {
        return $this->userType;
    }

    public function addTransaction(string $transactionType, int $id): void
    {
        $this->transactions[$transactionType][] = $id;
    }

    public function hasTransactions(string $transactionType): bool
    {
        return isset($this->transactions[$transactionType]);
    }

    public function getTransactionsByType(string $transactionType): array
    {
        return $this->transactions[$transactionType];
    }
}
