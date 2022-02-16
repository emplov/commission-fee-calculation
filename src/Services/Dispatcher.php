<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Services;

use Exception;
use Generator;

class Dispatcher
{
    private Commission $commission;

    private Generator|array $transactions;

    /**
     * Object's constructor.
     */
    public function __construct(
        array|Generator $transactions,
        Commission $commission,
    ) {
        $this->commission = $commission;
        $this->transactions = $transactions;
    }

    /**
     * @throws Exception
     */
    public function dispatch(): array
    {
        $calculatedCommissions = [];

        foreach ($this->transactions as $transaction) {
            $calculatedCommissions[] = $this->commission->addTransaction(
                $transaction[0],
                (int) $transaction[1],
                $transaction[2],
                $transaction[3],
                $transaction[4],
                $transaction[5],
            );
        }

        return $calculatedCommissions;
    }
}
