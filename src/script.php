<?php

declare(strict_types=1);

use CommissionFeeCalculation\Bootstrap\Script;
use CommissionFeeCalculation\Exceptions\ScriptException;
use CommissionFeeCalculation\Services\Commission;
use CommissionFeeCalculation\Services\Config;
use CommissionFeeCalculation\Services\Container;
use CommissionFeeCalculation\Services\File;

// Connecting composer's autoloader
require_once __DIR__.'/../vendor/autoload.php';

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

$fileService = $container->get(File::class);

// Check file for existence
if (!$fileService->fileExists($filepath)) {
    throw new ScriptException(ScriptException::ERROR_FILE_NOT_FOUNT);
}

// Check file size
if ($fileService->fileSize($filepath) > $container->get(Config::class)->get('max_file_size')) {
    throw new ScriptException(ScriptException::ERROR_FILE_TOO_BIG);
}

// Create script object
$script = new Script(
    fileService: $fileService,
    config: $container->get(Config::class),
    commission: $container->get(Commission::class),
    filepath: $filepath,
);

// Run script
$script->run();
