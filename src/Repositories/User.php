<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Repositories;

class User
{
    public array $users = [];

    /**
     * Find user.
     */
    public function find(int $userID): string|int|null
    {
        foreach ($this->users as $key => $user) {
            if ($user['user_id'] === $userID) {
                return $key;
            }
        }

        return null;
    }
}
