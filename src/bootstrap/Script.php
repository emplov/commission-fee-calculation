<?php

namespace CommissionFeeCalculation\Bootstrap;

use CommissionFeeCalculation\Exceptions\FileNotFoundException;
use CommissionFeeCalculation\Exceptions\NotParsableException;
use CommissionFeeCalculation\Models\Config;
use CommissionFeeCalculation\Services\File;
use CommissionFeeCalculation\Services\Dispatcher;

use Exception;

/**
 * @class Application
 */
final class Script
{
    /**
     * Constructor of object
     *
     * @param string $filename
     * @param string $separator
     * @param string $enclosure
     * @param string $escape
     */
    public function __construct(
        private string $filename,
        private string $separator = ',',
        private string $enclosure = '"',
        private string $escape = '\\',
    ){
        Config::setConfig(require FILE_PATH . '/config.php');
    }

    /**
     * Run application
     *
     * @return void
     * @throws Exception
     */
    public function run(): void
    {
        $filepath = CURRENT_PATH . '/' . $this->filename;

        // Check for file existence
        if (!File::fileExists($filepath)) {
            throw new FileNotFoundException('File not exists');
        }

        // Create dispatcher
        $dispatcher = new Dispatcher(CURRENT_PATH . '/' . $this->filename, $this->separator, $this->enclosure, $this->escape);

        // Parse file and get result
        $data = $dispatcher->parse();

        // If not parsed
        if (!$data['is_parsed']) {
            // Throw error
            throw new NotParsableException('Haven\'t been able to parse.');
        }

        // Show results if everything is ok.
        foreach ($data['response'] as $datum) {
            echo $datum . PHP_EOL;
        }
    }
}