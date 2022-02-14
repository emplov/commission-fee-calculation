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

        $conf = null;

        foreach ($parts as $part) {
            if (is_null($conf)) {
                $conf = self::set(self::$config, $part, $default);
            } else {
                $conf = self::set($conf, $part, $default);
            }
        }

        return $conf;
    }

    private static function set($conf, $part, $default)
    {
        if (!isset($conf[$part])) {
            $conf = $default;
        } else {
            $conf = $conf[$part];
        }

        return $conf;
    }
}
