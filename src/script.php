<?php

declare(strict_types=1);

use CommissionFeeCalculation\Bootstrap\Script;

// Current user's path
define("CURRENT_PATH", getcwd());

// Path to command's root path
define("FILE_ROOT_PATH", dirname(__FILE__, 2));

// Script file path
define("FILE_PATH", dirname(__FILE__));

// Connecting composer's autoloader
require FILE_ROOT_PATH . '/vendor/autoload.php';

// Taking filename from command line
$filename = $argv[1] ?? null;

// Check is filename empty
// If empty exit program
if (empty($filename)) {
    exit('Please specify the file name.' . PHP_EOL);
}

// Create script object
$script = new Script(
    filename: $filename,
);

// Run script
$script->run();
