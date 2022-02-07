<?php

namespace CommissionFeeCalculation\Parsers\Facades;

use CommissionFeeCalculation\Parsers\Items\CsvParser;
use CommissionFeeCalculation\Parsers\Contracts\ItemAbstract;
use CommissionFeeCalculation\Parsers\Contracts\Parser;

class CsvItemAbstract implements ItemAbstract
{
    /**
     * @inheritDoc
     */
    public static function extension(): string
    {
        return 'csv';
    }

    /**
     * @inheritDoc
     */
    public static function getParser(string $filename, string $separator, string $enclosure, string $escape): Parser
    {
        return (new CsvParser($filename, $separator, $enclosure, $escape));
    }
}