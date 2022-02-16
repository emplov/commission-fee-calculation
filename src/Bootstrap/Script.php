<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Bootstrap;

use CommissionFeeCalculation\Parsers\ParserContext;
use CommissionFeeCalculation\Services\Commission;
use CommissionFeeCalculation\Services\Config;
use CommissionFeeCalculation\Services\Container;
use CommissionFeeCalculation\Services\Dispatcher;
use Exception;

/**
 * @class Script
 */
final class Script
{
    private string $extension;

    private Config $config;

    private Commission $commission;

    public function __construct(
        private string $filepath,
        private string $separator = ',',
        private string $enclosure = '"',
        private string $escape = '\\',
    ) {
        $this->extension = $this->getExtension($filepath);
        $this->config = Container::getInstance()->get(Config::class);
        $this->commission = Container::getInstance()->get(Commission::class);
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
            commission: $this->commission,
        );

        // Parse file and get result
        $calculatedCommissions = $dispatcher->dispatch();

        // Show results if everything is ok.
        foreach ($calculatedCommissions as $commission) {
            echo $commission.PHP_EOL;
        }
    }

    /**
     * Get file extension.
     */
    public function getExtension(string $filename): string
    {
        $filename = strrev($filename);

        $explodedParts = explode('.', $filename);

        return strrev($explodedParts[0] ?? null);
    }

    /**
     * Check is this extension accessible.
     */
    public function getParserByExtension(string $extension): ParserContext
    {
        $context = new ParserContext();

        foreach ($this->config->get('accessible_extensions') as $accessibleExtension => $accessibleType) {
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
