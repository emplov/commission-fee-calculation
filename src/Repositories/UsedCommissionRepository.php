<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Repositories;

use CommissionFeeCalculation\Entities\UsedFreeCommission;
use CommissionFeeCalculation\Repositories\Persistence\Persistence;

class UsedCommissionRepository
{
    public function __construct(
        private Persistence $persistence,
    ) {
    }

    public function find(int $usedCommissionID): ?UsedFreeCommission
    {
        $usedCommission = $this->persistence->retrieve($usedCommissionID);

        if (!$usedCommission) {
            return null;
        }

        return new UsedFreeCommission(
            $usedCommission['user_id'],
            $usedCommission['type'],
            $usedCommission['date'],
            $usedCommission['week_start_date'],
            $usedCommission['week_end_date'],
            $usedCommission['free_amount'],
        );
    }

    public function save(UsedFreeCommission $usedCommission): mixed
    {
        return $this->persistence->persist([
            'user_id' => $usedCommission->getUserID(),
            'type' => $usedCommission->getType(),
            'date' => $usedCommission->getDate(),
            'week_start_date' => $usedCommission->getWeekStartDate(),
            'week_end_date' => $usedCommission->getWeekEndDate(),
            'free_amount' => $usedCommission->getFreeAmount(),
        ]);
    }
}
