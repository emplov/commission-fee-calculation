<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Services;

class Config
{
    private static array $config;

    public static function setConfig(array $config)
    {
        self::$config = $config;
    }

    public static function get(string $path, mixed $default = null)
    {
        $parts = explode('.', $path);

        $configuration = self::$config;

        foreach ($parts as $part) {
            if (!isset($configuration[$part])) {
                return $default;
            }

            $configuration = $configuration[$part];
        }

        return $configuration;
    }
}
