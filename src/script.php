<?php

declare(strict_types=1);

use CommissionFeeCalculation\Bootstrap\Script;
use CommissionFeeCalculation\Exceptions\ScriptException;
use CommissionFeeCalculation\Repositories\Commission;
use CommissionFeeCalculation\Repositories\User;
use CommissionFeeCalculation\Services\Config;
use CommissionFeeCalculation\Services\Container;
use CommissionFeeCalculation\Services\Converter\Convert;
use CommissionFeeCalculation\Services\Converter\CurrencyConverter;
use CommissionFeeCalculation\Services\File;
use CommissionFeeCalculation\Services\Math;

// Catch all script exceptions and type errors
set_exception_handler(function (Exception|TypeError|Error $e) {
    echo 'Exception class name: '.$e::class.PHP_EOL;
    echo 'Error: '.$e->getMessage().PHP_EOL;
    echo 'File: '.$e->getFile().PHP_EOL;
    echo 'Line: '.$e->getLine().PHP_EOL;
});

// path till src folder
$scriptPath = dirname(__FILE__);

// Connecting composer's autoloader
require_once $scriptPath.'/../vendor/autoload.php';

// Taking filename from command line
$filepath = $argv[1] ?? null;

// Check is filename empty
// If empty exit program
if (empty($filepath)) {
    throw new ScriptException(ScriptException::ERROR_FILE_NAME_NOT_SPECIFIED);
}

// Set global config
Config::setConfig(include $scriptPath.'/config.php');

// Check file for existence
if (!File::fileExists($filepath)) {
    throw new ScriptException(ScriptException::ERROR_FILE_NOT_FOUNT);
}

// Check file size
if (File::fileSize($filepath) > Config::get('max_file_size')) {
    throw new ScriptException(ScriptException::ERROR_FILE_TOO_BIG);
}

// Get container instance
$container = Container::getInstance();

// Add math
$container->add(Math::class, new Math());

// Create currency object
$converter = new CurrencyConverter();
$converter->fetchRates();

// Add converter to container list
$container->add(Convert::class, $converter);

// Add commission to container list
$container->add(User::class, new User());

// Add commission to container list
$container->add(Commission::class, new Commission());

// Create script object
$script = new Script(
    filepath: $filepath,
);

// Run script
$script->run();

// PHP used memory
// echo (memory_get_usage(true) / 1000 / 1024) . 'MB' . PHP_EOL;
