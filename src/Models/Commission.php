<?php

namespace CommissionFeeCalculation\Models;

class Commission
{
    public static array $data = [];

    private static array $result = [];

    /**
     * @param string $date
     * @param int $userID
     * @param string $userType
     * @param string $operationType
     * @param float $operationAmount
     * @param string $operationCurrency
     * @return void
     */
    public static function addData(
        string $date,
        int $userID,
        string $userType,
        string $operationType,
        float $operationAmount,
        string $operationCurrency,
    ): void
    {
        // Get user if exists
        $userKey = self::find($userID);

        // Create user if not exists
        if (is_null($userKey)) {
            self::$data[] = [
                'user_id' => $userID,
                'user_type' => $userType,
                'deposits_count' => 0,
                'withdraws_count' => 0,
                'last_withdraw_date' => null,
                'last_deposit_date' => null,
                'withdrawals' => [],
                'deposits' => [],
            ];

            $userKey = self::find($userID);
        }

        // Save operation
        if ($operationType === 'withdraw') {
            self::addWithdrawal($userKey, $date, $operationAmount, $operationCurrency, $userType);
        } elseif ($operationType === 'deposit') {
            self::addDeposit($userKey, $date, $operationAmount, $operationCurrency, $userType);
        }
    }

    /**
     * @param int $userKey
     * @param string $date
     * @param float $operationAmount
     * @param string $operationCurrency
     * @param string $userType
     * @return void
     */
    public static function addWithdrawal(int $userKey, string $date, float $operationAmount, string $operationCurrency, string $userType)
    {
        // Save withdrawal
        self::$data[$userKey]['withdrawals'][] = [
            'date' => $date,
            'type' => 'withdrawal',
            'amount' => $operationAmount,
            'currency' => $operationCurrency,
        ];

//         Withdrawal check statements
        foreach (config('user_types')[$userType] as $type) {
            if (mb_strtolower($type::type()) == mb_strtolower($userType . '_withdraw')) {
                $type::handle($userKey, $operationAmount, $operationCurrency, [
                    'date' => $date,
                ]);
                break;
            }
        }

        // Save last withdraw date
        self::$data[$userKey]['last_withdraw_date'] = $date;
    }

    /**
     * @param int $userKey
     * @param string $date
     * @param float $operationAmount
     * @param string $operationCurrency
     * @param string $userType
     * @return void
     */
    public static function addDeposit(int $userKey, string $date, float $operationAmount, string $operationCurrency, string $userType)
    {
        // Save deposit
        self::$data[$userKey]['deposits'][] = [
            'date' => $date,
            'type' => 'deposit',
            'amount' => $operationAmount,
            'currency' => $operationCurrency,
        ];

        // Deposit check statements
        foreach (config('user_types')[$userType] as $type) {
            if (mb_strtolower($type::type()) == mb_strtolower($userType . '_deposit')) {
                $type::handle($userKey, $operationAmount, $operationCurrency, [
                    'date' => $date,
                ]);
                break;
            }
        }

        // Save last deposit date
        self::$data[$userKey]['last_deposit_date'] = $date;
    }

    /**
     * Get result
     *
     * @return array
     */
    public static function getResult(): array
    {
        return self::$result;
    }

    /**
     * Get result
     *
     * @param mixed $res
     * @return mixed
     */
    public static function addResult(mixed $res): mixed
    {
        return self::$result[] = $res;
    }

    /**
     * Find user
     *
     * @param int $userID
     * @return string|int|null
     */
    public static function find(int $userID): string|int|null
    {
        foreach (self::$data as $key => $user) {
            if ($user['user_id'] === $userID) {
                return $key;
            }
        }

        return null;
    }
}