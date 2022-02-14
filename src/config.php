<?php

use CommissionFeeCalculation\Parsers\Items\CsvParser;
use CommissionFeeCalculation\Parsers\Items\TxtParser;
use CommissionFeeCalculation\UserTypeCommissions\Types\Business\BusinessDepositType;
use CommissionFeeCalculation\UserTypeCommissions\Types\Business\BusinessWithdrawType;
use CommissionFeeCalculation\UserTypeCommissions\Types\Privete\PrivateDepositType;
use CommissionFeeCalculation\UserTypeCommissions\Types\Privete\PrivateWithdrawType;

return [
    'max_file_size' => 100, // in MB

    'accessible_extensions' => [
        'csv' => CsvParser::class,
        'txt' => TxtParser::class,
    ],

    'user_types' => [
        'private' => [
            'deposit' => PrivateDepositType::class,
            'withdraw' => PrivateWithdrawType::class,
        ],
        'business' => [
            'deposit' => BusinessDepositType::class,
            'withdraw' => BusinessWithdrawType::class,
        ],
    ],

    'commissions' => [
        'business' => [
            'deposit' => '0.03',
            'withdraw' => '0.5',
        ],
        'private' => [
            'deposit' => '0.03',
            'withdraw' => [
                'percent' => "0.3",
                'week_free_fee_amount' => '1000',
            ],
        ],
    ],
];
