<?php

if (!function_exists('dd')) {
    function dd(...$data) {
        var_dump($data);die;
    }
}

if (!function_exists('config')) {
    function config(string $path) {
        return \CommissionFeeCalculation\Models\Config::get($path);
    }
}