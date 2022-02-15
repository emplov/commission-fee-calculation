<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Parsers;

use CommissionFeeCalculation\Exceptions\NotAccessableExtensionException;
use CommissionFeeCalculation\Parsers\Contracts\Parser;
use Generator;

class ParserContext
{
    private ?Parser $parser = null;

    /**
     * @return void
     */
    public function setStrategy(Parser $parser)
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
