<?php

namespace CommissionFeeCalculation\Models;

class Config
{
    private static array $config;

    public static function setConfig(array $config)
    {
        self::$config = $config;
    }

    public static function getConfig(): array
    {
        return self::$config;
    }

    public static function get(string $path, mixed $default = null)
    {
        $parts = explode('.', $path);

        $conf = null;

        foreach ($parts as $part) {
            if (is_null($conf)) {
                if (!isset(self::$config[$part])) {
                    $conf = $default;
                    break;
                } else {
                    $conf = self::$config[$part];
                }
            } else {
                if (!isset($conf[$part])) {
                    $conf = $default;
                    break;
                } else {
                    $conf = $conf[$part];
                }
            }
        }

        return $conf;
    }
}