<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Repositories;

use CommissionFeeCalculation\Entities\User;
use CommissionFeeCalculation\Repositories\Persistence\Persistence;

class UserRepository
{
    public function __construct(
        private Persistence $persistence,
    ) {
    }

    public function find(int $userID): ?User
    {
        $user = $this->persistence->retrieve($userID);

        if (!$user) {
            return null;
        }

        return User::fromState($user['user_id'], $user['user_type'], $user['transactions']);
    }

    public function save(User $user): void
    {
        $this->persistence->persist([
            'user_id' => $user->getUserID(),
            'user_type' => $user->getUserType(),
            'transactions' => $user->getTransactions(),
        ]);
    }
}
