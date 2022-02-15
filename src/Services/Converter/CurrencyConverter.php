<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Services\Converter;

use CommissionFeeCalculation\Services\Container;
use CommissionFeeCalculation\Services\Math;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class CurrencyConverter implements Convert
{
    public array $currenciesRate = [];

    private Math $math;

    public function __construct()
    {
        $this->math = Container::getInstance()->get(Math::class);
    }

    /**
     * Get rates from api.
     *
     * @return void
     *
     * @throws GuzzleException
     */
    public function fetchRates()
    {
        if (count($this->currenciesRate) === 0) {
            $guzzleClient = new Client();

            $response = $guzzleClient->request(
                'GET',
                'https://developers.paysera.com/tasks/api/currency-exchange-rates',
            );

            $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            $this->currenciesRate = $data['rates'];
        }
    }

    public function convert(string $amount, string $currency): string
    {
        return $this->math->sub($amount, (string) $this->getRate($currency));
    }

    public function getRate(string $currency)
    {
        return $this->currenciesRate[$currency];
    }

    public function setRate(string $currency, float $price)
    {
        $this->currenciesRate[$currency] = $price;
    }
}
