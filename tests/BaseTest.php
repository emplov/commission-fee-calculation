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

        Currencies::setRate('EUR', 1);
        Currencies::setRate('USD', 1.1497);
        Currencies::setRate('JPY', 129.53);
    }
}