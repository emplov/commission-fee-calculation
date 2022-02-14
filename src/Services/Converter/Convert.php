<?php

namespace CommissionFeeCalculation\Services\Converter;

interface Convert
{
    public function convert(string $amount, string $currency);

    public function fetchRates();

    public function getRate(string $currency);

    public function setRate(string $currency, float $price);
}
