<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Parsers;

use CommissionFeeCalculation\Exceptions\NotAccessableExtensionException;
use CommissionFeeCalculation\Parsers\Contracts\Parser;
use Generator;

class ParserContext
{
    private ?Parser $parser = null;

    public function setStrategy(Parser $parser): void
    {
        $this->parser = $parser;
    }

    /**
     * @throws NotAccessableExtensionException
     */
    public function execute(): Generator
    {
        if (is_null($this->parser)) {
            throw new NotAccessableExtensionException();
        }

        return $this->parser->parse();
    }
}
