<?php

declare(strict_types=1);

use CommissionFeeCalculation\Bootstrap\Script;
use CommissionFeeCalculation\Exceptions\ScriptException;
use CommissionFeeCalculation\Services\Config;
use CommissionFeeCalculation\Services\Container;
use CommissionFeeCalculation\Services\File;

// path till src folder
$scriptPath = __DIR__;

// Connecting composer's autoloader
require_once $scriptPath.'/../vendor/autoload.php';

// Taking filename from command line
$filepath = $argv[1] ?? null;

// Check is filename empty
// If empty exit program
if (empty($filepath)) {
    throw new ScriptException(ScriptException::ERROR_FILE_NAME_NOT_SPECIFIED);
}

// Create container instance with definitions
$container = Container::getInstance();
$container->addDefinitions(include 'definitions.php');

// Check file for existence
if (!File::fileExists($filepath)) {
    throw new ScriptException(ScriptException::ERROR_FILE_NOT_FOUNT);
}

// Check file size
if (File::fileSize($filepath) > $container->get(Config::class)->get('max_file_size')) {
    throw new ScriptException(ScriptException::ERROR_FILE_TOO_BIG);
}

// Create script object
$script = new Script(
    filepath: $filepath,
);

// Run script
$script->run();
