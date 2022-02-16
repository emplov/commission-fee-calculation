<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Services;

class Config
{
    private array $config;

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    public function get(string $path, mixed $default = null)
    {
        $parts = explode('.', $path);

        $configuration = $this->config;

        foreach ($parts as $part) {
            if (!isset($configuration[$part])) {
                return $default;
            }

            $configuration = $configuration[$part];
        }

        return $configuration;
    }
}
