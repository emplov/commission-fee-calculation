<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Repositories;

class User
{
    public array $users = [];

    public function find(int $userID): ?array
    {
        return $this->users[$userID] ?? null;
    }

    public function addUser(int $userID, string $userType): void
    {
        $this->users[$userID] = [
            'user_id' => $userID,
            'user_type' => $userType,
            'deposits_count' => 0,
            'withdraws_count' => 0,
            'last_withdraw_date' => null,
            'last_deposit_date' => null,
            'withdrawals' => [],
            'deposits' => [],
        ];
    }
}
