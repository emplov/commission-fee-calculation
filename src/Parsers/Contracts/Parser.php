<?php

namespace CommissionFeeCalculation\Parsers\Contracts;

interface Parser
{
    /**
     * @param string $filename
     * @param string $separator
     * @param string|null $enclosure
     * @param string|null $escape
     */
    public function __construct(string $filename, string $separator, string $enclosure = null, string $escape = null);

    /**
     * Parse file
     *
     * @return bool
     */
    public function parse(): bool;
}