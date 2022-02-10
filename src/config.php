<?php

use CommissionFeeCalculation\Parsers\Facades\CsvItemAbstract;
use CommissionFeeCalculation\Parsers\Facades\TxtItemAbstract;
use CommissionFeeCalculation\UserTypeCommissions\PrivateDepositType;
use CommissionFeeCalculation\UserTypeCommissions\BusinessDepositType;
use CommissionFeeCalculation\UserTypeCommissions\PrivateWithdrawType;
use CommissionFeeCalculation\UserTypeCommissions\BusinessWithdrawType;

return [
    'max_file_size' => 100, // in MB

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
    ],

    'week_free_fee_amount' => 1000,

    'business_deposit_percent' => 0.03,
    'private_deposit_percent' => 0.03,
    'business_withdraw_percent' => 0.5,
    'private_withdraw_percent' => 0.3,
];