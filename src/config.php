<?php

use CommissionFeeCalculation\Parsers\Facades\CsvItemAbstract;
use CommissionFeeCalculation\Parsers\Facades\TxtItemAbstract;
use CommissionFeeCalculation\UserTypeCommissions\PrivateDepositType;
use CommissionFeeCalculation\UserTypeCommissions\BusinessDepositType;
use CommissionFeeCalculation\UserTypeCommissions\PrivateWithdrawType;
use CommissionFeeCalculation\UserTypeCommissions\BusinessWithdrawType;

return [
    'accessible_types' => [
        CsvItemAbstract::class,
        TxtItemAbstract::class,
    ],
    'user_types' => [
        'private' => [
            PrivateDepositType::class,
            PrivateWithdrawType::class,
        ],
        'business' => [
            BusinessDepositType::class,
            BusinessWithdrawType::class,
        ],
    ]
];