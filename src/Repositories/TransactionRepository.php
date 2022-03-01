<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Repositories;

use CommissionFeeCalculation\Entities\Transaction;
use CommissionFeeCalculation\Repositories\Persistence\Persistence;

class TransactionRepository
{
    public function __construct(
        private Persistence $persistence,
    ) {
    }

    public function find(int $transactionID): ?Transaction
    {
        $usedCommission = $this->persistence->retrieve($transactionID);

        if (!$usedCommission) {
            return null;
        }

        return new Transaction(
            $usedCommission['user_id'],
            $usedCommission['type'],
            $usedCommission['date'],
            $usedCommission['week_start_date'],
            $usedCommission['week_end_date'],
            $usedCommission['free_amount'],
            $usedCommission['amount'],
        );
    }

    public function save(Transaction $transaction): mixed
    {
        return $this->persistence->persist([
            'user_id' => $transaction->getUserID(),
            'type' => $transaction->getType(),
            'date' => $transaction->getDate(),
            'week_start_date' => $transaction->getWeekStartDate(),
            'week_end_date' => $transaction->getWeekEndDate(),
            'free_amount' => $transaction->getFreeAmount(),
            'amount' => $transaction->getAmount(),
        ]);
    }
}
