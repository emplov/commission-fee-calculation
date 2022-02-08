<?php

namespace CommissionFeeCalculation\Bootstrap;

use CommissionFeeCalculation\Models\Config;
use CommissionFeeCalculation\Services\File;
use CommissionFeeCalculation\Services\Dispatcher;

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
     */
    public function run(): void
    {
        $filepath = CURRENT_PATH . '/' . $this->filename;

        if (!File::fileExists($filepath)) {
            die('File not exists' . PHP_EOL);
        }

        $parser = new Dispatcher(CURRENT_PATH . '/' . $this->filename, $this->separator, $this->enclosure, $this->escape);

        $data = $parser->parse();

        if (!$data['is_parsed']) {
            die('Haven\'t been able to parse.' . PHP_EOL);
        }

        foreach ($data['response'] as $datum) {
            echo $datum . PHP_EOL;
        }
    }
}