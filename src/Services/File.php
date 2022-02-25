<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Services;

class File
{
    /**
     * Check for file existence.
     */
    public function fileExists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * @return false|resource
     */
    public function openFile(string $path)
    {
        return fopen($path, 'rb');
    }

    public function closeFile($openedFile): bool
    {
        return fclose($openedFile);
    }

    public function fileSize(string $filepath): float
    {
        $filesize = filesize($filepath);

        return $filesize / 1000 / 1024;
    }
}
