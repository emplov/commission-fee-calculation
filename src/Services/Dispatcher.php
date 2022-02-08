<?php

namespace CommissionFeeCalculation\Services;

use CommissionFeeCalculation\Models\Commission;
use CommissionFeeCalculation\Models\Currencies;
use CommissionFeeCalculation\Parsers\Contracts\Parser;

class Dispatcher
{
    private string $filename;

    private string $separator;

    private string $enclosure;

    private string $escape;

    private string $extension;

    /**
     * @param string $filename
     * @param string $separator
     * @param string $enclosure
     * @param string $escape
     */
    public function __construct(string $filename, string $separator, string $enclosure, string $escape)
    {
        $this->filename = $filename;
        $this->separator = $separator;
        $this->enclosure = $enclosure;
        $this->escape = $escape;

        $this->extension = $this->getExtension($filename);
    }

    /**
     * @return array
     */
    public function parse(): array
    {
        $parser = $this->checkExtensionTypeAndGetParser($this->extension);

        Currencies::fetchRates();

        if (empty($parser)) {
            die('Not accessible type.' . PHP_EOL);
        }

        $isParsed = false;
        $errorMessage = null;

        try {
            $isParsed = $parser->parse();

            $resData = Commission::getResult();
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $isParsed = false;
        } finally {
            $data = [
                'is_parsed' => $isParsed,
                'error_message' => $errorMessage,
                'response' => $resData ?? null,
            ];
        }

        return $data;
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
                    $this->filename,
                    $this->separator,
                    $this->enclosure,
                    $this->escape,
                );
            }
        }

        return null;
    }
}