<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Services;

class File
{
    /**
     * Check for file existence.
     */
    public static function fileExists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * @return false|resource
     */
    public static function openFile(string $path)
    {
        return fopen($path, 'r');
    }

    /**
     * @param $openedFile
     */
    public static function closeFile($openedFile): bool
    {
        return fclose($openedFile);
    }

    public static function fileSize(string $filepath): float
    {
        $filesize = filesize($filepath);

        return $filesize / 1000 / 1024;
    }
}
