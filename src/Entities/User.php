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

    public static function fromState(int $userID, string $userType, array $transactions = []): self
    {
        return new self(
            $userID,
            $userType,
            $transactions,
        );
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

    public function addTransaction(string $transactionType, UsedCommission $transaction): void
    {
        $this->transactions[$transactionType][] = $transaction;
    }

    public function hasTransactions(string $transactionType): bool
    {
        if (isset($this->transactions[$transactionType])) {
            return true;
        }

        return false;
    }

    public function getTransactionsByType(string $transactionType): array
    {
        return $this->transactions[$transactionType];
    }
}
