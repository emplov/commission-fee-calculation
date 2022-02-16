<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Entities;

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
        ];
    }
}
