<?php

namespace CommissionFeeCalculation\Parsers;

use CommissionFeeCalculation\Exceptions\NotAccessableExtensionException;
use CommissionFeeCalculation\Parsers\Contracts\Parser;
use Generator;

class ParserContext
{
    private ?Parser $parser = null;

    /**
     * @param Parser $parser
     * @return void
     */
    public function setStrategy(Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @return Generator|array
     * @throws NotAccessableExtensionException
     */
    public function execute(): Generator|array
    {
        if (is_null($this->parser)) {
            throw new NotAccessableExtensionException();
        }

        return $this->parser->parse();
    }
}
