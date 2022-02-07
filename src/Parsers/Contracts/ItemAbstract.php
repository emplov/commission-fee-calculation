<?php

namespace CommissionFeeCalculation\Parsers\Contracts;

interface ItemAbstract
{
    /**
     * Get extension
     *
     * @return string
     */
    public static function extension(): string;

    /**
     * Get this extensions parser
     *
     * @param string $filename
     * @param string $separator
     * @param string $enclosure
     * @param string $escape
     * @return Parser
     */
    public static function getParser(string $filename, string $separator, string $enclosure, string $escape): Parser;
}