<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Services\Converter;

interface Converter
{
    public function convert(string $amount, string $currency);

    public function fetchRates();

    public function getRate(string $currency);

    public function setRate(string $currency, float $price);
}
