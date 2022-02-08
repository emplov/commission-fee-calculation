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
    }

    /**
     * @param float $amount
     * @param string $currencyType
     * @return float
     */
    public static function currencyToEur(float $amount, string $currencyType): float
    {
        if (mb_strtolower($currencyType) != 'eur') {
            $amount /= Currencies::$currencies_rate[$currencyType];
        }

        return $amount;
    }

    /**
     * @param string $currency
     * @param float $price
     * @return void
     */
    public static function setRate(string $currency, float $price)
    {
        self::$currencies_rate[$currency] = $price;
    }
}