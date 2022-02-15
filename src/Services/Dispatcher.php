<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Services;

use CommissionFeeCalculation\Repositories\Commission;
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
        array|Generator $transactions
    ) {
        $this->commission = Container::getInstance()->get(Commission::class);
        $this->transactions = $transactions;
    }

    /**
     * @throws Exception
     */
    public function dispatch(): array
    {
        foreach ($this->transactions as $transaction) {
            $this->commission->addData(
                $transaction[0],
                (int) $transaction[1],
                $transaction[2],
                $transaction[3],
                $transaction[4],
                $transaction[5],
            );
        }

        return $this->commission->getResult();
    }
}
