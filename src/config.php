<?php

declare(strict_types=1);

use CommissionFeeCalculation\Parsers\Items\CsvParser;
use CommissionFeeCalculation\UserTypeCommissions\Types\Business\BusinessDepositType;
use CommissionFeeCalculation\UserTypeCommissions\Types\Business\BusinessWithdrawType;
use CommissionFeeCalculation\UserTypeCommissions\Types\Privete\PrivateDepositType;
use CommissionFeeCalculation\UserTypeCommissions\Types\Privete\PrivateWithdrawType;

return [
    'max_file_size' => 100, // in MB

    'accessible_extensions' => [
        'csv' => CsvParser::class,
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
                'percent' => '0.3',
                'week_free_fee_amount' => '1000',
            ],
        ],
    ],

    'currency_decimal_part' => [
        'USD' => 2,
        'EUR' => 2,
        'JPY' => 0,
    ],
];
