<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Entities;

class Transaction
{
    public function __construct(
        private int $userId,
        private string $type,
        private string $date,
        private string $weekStartDate,
        private string $weekEndDate,
        private string $freeAmount,
        private string $amount,
    ) {
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getFreeAmount(): string
    {
        return $this->freeAmount;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getWeekStartDate(): string
    {
        return $this->weekStartDate;
    }

    public function getWeekEndDate(): string
    {
        return $this->weekEndDate;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
