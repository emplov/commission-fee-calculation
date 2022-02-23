<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Parsers\Contracts;

use CommissionFeeCalculation\Services\File;
use Generator;

interface Parser
{
    /**
     * @param ?string $enclosure
     * @param ?string $escape
     */
    public function __construct(File $fileService, string $filename, string $separator, string $enclosure = null, string $escape = null);

    /**
     * Parse file.
     */
    public function parse(): Generator;
}
