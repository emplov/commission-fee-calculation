<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Entities;

class UsedCommission
{
    public function __construct(
        private string $date,
        private string $weekStartDate,
        private string $weekEndDate,
        private string $freeAmount,
    ) {
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
}
