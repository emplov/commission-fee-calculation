<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Repositories;

class User
{
    private array $users = [];

    public function find(int $userID): ?array
    {
        return $this->users[$userID] ?? null;
    }

    public function addUser(int $userID, string $userType): void
    {
        $this->users[$userID] = [
            'user_id' => $userID,
            'user_type' => $userType,
            'deposits_count' => '0',
            'withdraws_count' => '0',
            'withdrawals' => [],
            'deposits' => [],
        ];
    }

    public function addTransaction(int $userID, Transaction $transaction, string $type): void
    {
        $this->users[$userID][$type][] = $transaction;
    }
}
