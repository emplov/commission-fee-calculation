<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Parsers\Items;

use CommissionFeeCalculation\Parsers\Contracts\Parser;
use CommissionFeeCalculation\Services\File;
use Generator;

class CsvParser implements Parser
{
    /**
     * {@inheritDoc}
     */
    public function __construct(
        private File $fileService,
        private string $filepath,
        private string $separator,
        private ?string $enclosure = null,
        private ?string $escape = null,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function parse(): Generator
    {
        $fileResource = $this->fileService->openFile($this->filepath);

        while (
            (
                $userTransaction = fgetcsv(
                    $fileResource,
                    1000,
                    $this->separator,
                    $this->enclosure,
                    $this->escape,
                )
            ) !== false
        ) {
            yield $userTransaction;
        }

        $this->fileService->closeFile($fileResource);
    }
}
