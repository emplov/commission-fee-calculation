<?php

if (!function_exists('testasd')) {
    function testasd() {
        var_dump('test');die;
    }
}

if (!function_exists('config')) {
    function config(string $path) {
        return \CommissionFeeCalculation\Models\Config::get($path);
    }
}