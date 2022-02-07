<?php

declare(strict_types=1);

use CommissionFeeCalculation\Bootstrap\Script;

// Путь пользователя с которого запускается сам скрипт
define("CURRENT_PATH", getcwd());

// Root путь папки от программы
define("FILE_ROOT_PATH", dirname(__FILE__, 2));

// Путь до файла
define("FILE_PATH", dirname(__FILE__));

// Подключаем composer
require FILE_ROOT_PATH . '/vendor/autoload.php';

// Берем название файла с терминала
$filename = $argv[1] ?? null;

// Проверяем пустой или нет
if (empty($filename)) {
    die('Please specify the file name.' . PHP_EOL);
}

// Если нет, то создаём объект скрипта.
$script = new Script(
    filename: $filename,
);

// И запускаем его.
$script->run();
