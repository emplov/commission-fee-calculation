<?php

use CommissionFeeCalculation\Models\Config;

if (!function_exists('dd')) {
    /**
     * @param ...$data
     * @return void
     */
    function dd(...$data) {
        var_dump($data);die;
    }
}

if (!function_exists('config')) {
    function config(string $path, mixed $default = null) {
        return Config::get($path, $default);
    }
}