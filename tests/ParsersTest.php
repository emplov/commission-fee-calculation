<?php

namespace Tests;

use CommissionFeeCalculation\Services\Dispatcher;

class ParsersTest extends BaseTest
{
    public function testCheckCsv(): void
    {
        $parser = new Dispatcher('input.csv', ',', '"', '\\');

        $data = $parser->parse();

        $this->assertSame(13, count($data['response']), 'Data return count not same.');
        $this->assertSame([
            '0.60',
            '3.00',
            '0.00',
            '0.06',
            '1.50',
            '0.00',
            '0.69',
            '0.30',
            '0.00',
            '3.00',
            '0.00',
            '0.00',
            '8607.39',
        ], $data['response'],  'Returned data not same.');
    }

    public function testCheckTxt()
    {
        $parser = new Dispatcher('input.txt', ',', '"', '\\');

        $data = $parser->parse();

        $this->assertSame(13, count($data['response']), 'Data return count not same.');
        $this->assertSame([
            '0.60',
            '3.00',
            '0.00',
            '0.06',
            '1.50',
            '0.00',
            '0.69',
            '0.30',
            '0.30',
            '3.00',
            '0.00',
            '0.00',
            '8607.39',
        ], $data['response'],  'Returned data not same.');
    }
}