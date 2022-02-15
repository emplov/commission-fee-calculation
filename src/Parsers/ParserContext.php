<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Parsers;

use CommissionFeeCalculation\Exceptions\ScriptException;
use CommissionFeeCalculation\Parsers\Contracts\Parser;
use Generator;

class ParserContext
{
    private ?Parser $parser = null;

    public function setStrategy(Parser $parser): void
    {
        $this->parser = $parser;
    }

    public function execute(): Generator
    {
        if (is_null($this->parser)) {
            throw new ScriptException(ScriptException::ERROR_NOT_ACCESSIBLE_TYPE);
        }

        return $this->parser->parse();
    }
}
