<?php

namespace Tests;

use CommissionFeeCalculation\Models\Config;
use CommissionFeeCalculation\Models\Currencies;

use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Config::setConfig(require './src/config.php');

        Currencies::fetchRates();
        Currencies::setRate('JPY', 130.869977);
        Currencies::setRate('USD', 1.129031);
    }
}