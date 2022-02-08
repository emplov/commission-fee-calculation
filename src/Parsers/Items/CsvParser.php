<?php

namespace CommissionFeeCalculation\Parsers\Items;

use CommissionFeeCalculation\Services\File;
use CommissionFeeCalculation\Models\Commission;
use CommissionFeeCalculation\Parsers\Contracts\Parser;

class CsvParser implements Parser
{
    private string $filepath;

    private string $separator;

    private string $enclosure;

    private string $escape;

    /**
     * @inheritDoc
     */
    public function __construct(string $filepath, string $separator, string $enclosure = null, string $escape = null)
    {
        $this->filepath = $filepath;
        $this->separator = $separator;
        $this->enclosure = $enclosure;
        $this->escape = $escape;
    }

    /**
     * @inheritDoc
     */
    public function parse(): bool
    {
        $fileResource = File::openFile($this->filepath);

        while (($data = fgetcsv($fileResource, 1000, $this->separator, $this->enclosure, $this->escape)) !== false) {
            Commission::addData($data[0], $data[1], $data[2], $data[3], $data[4], $data[5]);
        }

        File::closeFile($fileResource);

        return true;
    }
}