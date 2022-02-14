<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\DTO\Objects;

use CommissionFeeCalculation\DTO\Contracts\DTOAbstract;

class CommissionDataDTO extends DTOAbstract
{
    public int $userKey;

    public string $date;

    public string $operationAmount;

    public string $operationCurrency;

    public string $userType;

    public int $decimalsCount;
}
