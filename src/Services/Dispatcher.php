<?php

namespace CommissionFeeCalculation\Services;

use CommissionFeeCalculation\Exceptions\NotAccessableExtensionException;
use CommissionFeeCalculation\Models\Commission;
use CommissionFeeCalculation\Models\Currencies;
use CommissionFeeCalculation\Parsers\Contracts\Parser;

use Exception;

class Dispatcher
{
    private string $filepath;

    private string $separator;

    private string $enclosure;

    private string $escape;

    private string $extension;

    /**
     * @param string $filepath
     * @param string $separator
     * @param string $enclosure
     * @param string $escape
     */
    public function __construct(string $filepath, string $separator, string $enclosure, string $escape)
    {
        $this->filepath = $filepath;
        $this->separator = $separator;
        $this->enclosure = $enclosure;
        $this->escape = $escape;

        $this->extension = $this->getExtension($filepath);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function parse(): array
    {
        $parser = $this->checkExtensionTypeAndGetParser($this->extension);

        Currencies::fetchRates();

        if (empty($parser)) {
            throw new NotAccessableExtensionException('Not accessible type.' . PHP_EOL);
        }

        $errorMessage = null;

        $isParsed = $parser->parse();
        $resData = Commission::getResult();

        return [
            'is_parsed' => $isParsed,
            'error_message' => $errorMessage,
            'response' => $resData ?? null,
        ];
    }

    /**
     * Get file extension
     *
     * @param string $filename
     * @return string
     */
    public function getExtension(string $filename): string
    {
        $filename = strrev($filename);

        $explodedData = explode('.', $filename);

        return strrev($explodedData[0] ?? null);
    }

    /**
     * Check if available in program.
     *
     * @param string $extension
     * @return Parser|null
     */
    public function checkExtensionTypeAndGetParser(string $extension): ?Parser
    {
        foreach (config('accessible_types') as $accessibleType) {
            if ($extension === $accessibleType::extension()) {
                return $accessibleType::getParser(
                    $this->filepath,
                    $this->separator,
                    $this->enclosure,
                    $this->escape,
                );
            }
        }

        return null;
    }
}