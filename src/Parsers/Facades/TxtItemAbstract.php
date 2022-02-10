<?php

namespace CommissionFeeCalculation\Parsers\Facades;

use CommissionFeeCalculation\Parsers\Items\TxtParser;
use CommissionFeeCalculation\Parsers\Contracts\Parser;
use CommissionFeeCalculation\Parsers\Contracts\ItemAbstract;

class TxtItemAbstract implements ItemAbstract
{
    /**
     * @inheritDoc
     */
    public static function extension(): string
    {
        return 'txt';
    }

    /**
     * @inheritDoc
     */
    public static function getParser(string $filename, string $separator, string $enclosure, string $escape): Parser
    {
        return (new TxtParser($filename, $separator, $enclosure, $escape));
    }
}