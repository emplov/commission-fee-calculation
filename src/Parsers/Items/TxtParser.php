<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Parsers\Items;

use CommissionFeeCalculation\Parsers\Contracts\Parser;
use CommissionFeeCalculation\Services\File;
use Generator;

class TxtParser implements Parser
{
    private string $filepath;

    private string $separator;

    /**
     * {@inheritDoc}
     */
    public function __construct(string $filepath, string $separator, string $enclosure = null, string $escape = null)
    {
        $this->filepath = $filepath;
        $this->separator = $separator;
    }

    /**
     * {@inheritDoc}
     */
    public function parse(): Generator|array
    {
        $fileResource = File::openFile($this->filepath);

        while (!feof($fileResource)) {
            // Get new line
            $line = fgets($fileResource);

            // Check for emptiness
            if (empty($line) || is_bool($line)) {
                continue;
            }

            $line = trim($line);

            // Explode by separator
            $userTransaction = explode($this->separator, $line);

            yield $userTransaction;
        }

        File::closeFile($fileResource);
    }
}
