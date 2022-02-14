<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Parsers\Contracts;

use Generator;

interface Parser
{
    /**
     * @param string $filename
     * @param string $separator
     * @param ?string $enclosure
     * @param ?string $escape
     */
    public function __construct(string $filename, string $separator, string $enclosure = null, string $escape = null);

    /**
     * Parse file
     *
     * @return Generator|array
     */
    public function parse(): Generator|array;
}
