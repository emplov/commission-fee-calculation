<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Services;

class File
{
    /**
     * Check for file existence
     *
     * @param string $path
     * @return bool
     */
    public static function fileExists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * @param string $path
     * @return false|resource
     */
    public static function openFile(string $path)
    {
        return fopen($path, 'r');
    }

    /**
     * @param $openedFile
     * @return bool
     */
    public static function closeFile($openedFile): bool
    {
        return fclose($openedFile);
    }

    /**
     * @param string $filepath
     * @return float
     */
    public static function fileSize(string $filepath): float
    {
        $filesize = filesize($filepath);

        return ($filesize / 1000 / 1024);
    }
}
