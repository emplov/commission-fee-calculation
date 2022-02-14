<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Bootstrap;

use CommissionFeeCalculation\Parsers\ParserContext;
use CommissionFeeCalculation\Services\Config;
use CommissionFeeCalculation\Services\Dispatcher;
use Exception;

/**
 * @class Script
 */
final class Script
{
    private string $extension;

    public function __construct(
        private string $filepath,
        private string $separator = ',',
        private string $enclosure = '"',
        private string $escape = '\\',
    ) {
        $this->extension = $this->getExtension($filepath);
    }

    /**
     * Run application.
     *
     * @throws Exception
     */
    public function run(): void
    {
        // Get parser
        $parser = $this->getParserByExtension($this->extension);

        $transactions = $parser->execute();

        // Create dispatcher
        $dispatcher = new Dispatcher(
            transactions: $transactions,
        );

        // Parse file and get result
        $calculatedCommissions = $dispatcher->dispatch();

        // Show results if everything is ok.
        foreach ($calculatedCommissions['response'] as $commission) {
            echo $commission.PHP_EOL;
        }
    }

    /**
     * Get file extension.
     */
    public function getExtension(string $filename): string
    {
        $filename = strrev($filename);

        $explodedData = explode('.', $filename);

        return strrev($explodedData[0] ?? null);
    }

    /**
     * Check is this extension accessible.
     */
    public function getParserByExtension(string $extension): ParserContext
    {
        $context = new ParserContext();

        foreach (Config::get('accessible_extensions') as $accessibleExtension => $accessibleType) {
            if ($extension === $accessibleExtension) {
                $context->setStrategy(
                    new $accessibleType(
                        $this->filepath,
                        $this->separator,
                        $this->enclosure,
                        $this->escape,
                    ),
                );

                break;
            }
        }

        return $context;
    }
}
