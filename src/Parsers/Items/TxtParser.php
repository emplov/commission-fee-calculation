<?php

namespace CommissionFeeCalculation\Parsers\Items;

use CommissionFeeCalculation\Models\Commission;
use CommissionFeeCalculation\Parsers\Contracts\Parser;
use CommissionFeeCalculation\Services\File;

class TxtParser implements Parser
{
    private string $filepath;

    private string $separator;

    /**
     * @inheritDoc
     */
    public function __construct(string $filepath, string $separator, string $enclosure = null, string $escape = null)
    {
        $this->filepath = $filepath;
        $this->separator = $separator;
    }

    public function parse(): bool
    {
        $fileResource = File::openFile($this->filepath);

        while (!feof($fileResource)) {
            // Get new line
            $line = trim(fgets($fileResource));

            // Check for not emptiness
            if (empty($line)) {
                continue;
            }

            // Explode by separator
            $data = explode($this->separator, $line);

            // Add data to model
            Commission::addData($data[0], (int) $data[1], $data[2], $data[3], $data[4], $data[5]);
        }

        return true;
    }
}