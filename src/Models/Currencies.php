<?php

namespace CommissionFeeCalculation\Models;

class Currencies
{
    public static array $currencies_rate = [];

    /**
     * Get rates from api
     *
     * @return void
     */
    public static function fetchRates()
    {
        $data = file_get_contents('https://developers.paysera.com/tasks/api/currency-exchange-rates');

        $data = json_decode($data, true);

        self::$currencies_rate = $data['rates'];

        self::$currencies_rate['USD'] = 1.1497;
        self::$currencies_rate['JPY'] = 129.53;
    }

    /**
     * @param float $amount
     * @param string $currencyType
     * @return float
     */
    public static function currencyToEur(float $amount, string $currencyType): float
    {
        if (mb_strtolower($currencyType) != 'eur') {
            $amount = $amount * Currencies::$currencies_rate[$currencyType];
        }

        return $amount;
    }
}