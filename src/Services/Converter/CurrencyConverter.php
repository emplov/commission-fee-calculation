<?php

namespace CommissionFeeCalculation\Services\Converter;

use CommissionFeeCalculation\Services\Container;
use CommissionFeeCalculation\Services\Math;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class CurrencyConverter implements Convert
{
    public array $currencies_rate = [];

    private Math $math;

    public function __construct()
    {
        $this->math = Container::getInstance()->get(Math::class);
    }

    /**
     * Get rates from api
     *
     * @return void
     * @throws GuzzleException
     */
    public function fetchRates()
    {
        if (count($this->currencies_rate) == 0) {
            $guzzleClient = new Client();

            $response = $guzzleClient->request(
                'GET',
                'https://developers.paysera.com/tasks/api/currency-exchange-rates',
            );

            $data = json_decode($response->getBody()->getContents(), true);

            $this->currencies_rate = $data['rates'];
        }
    }

    public function convert(string $amount, string $currency)
    {
        return ($amount / $this->getRate($currency));
    }

    public function getRate(string $currency)
    {
        return $this->currencies_rate[$currency];
    }

    public function setRate(string $currency, float $price)
    {
        $this->currencies_rate[$currency] = $price;
    }
}
