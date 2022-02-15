<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Services;

class Math
{
    public function add(string $leftOperand, string $rightOperand, int $scale = 5): string
    {
        return bcadd($leftOperand, $rightOperand, $scale);
    }

    public function divide(string $num1, string $num2, int $scale = 5): string
    {
        return bcdiv($num1, $num2, $scale);
    }

    public function pow(string $num, string $exponent, int $scale = 5): string
    {
        return bcpow($num, $exponent, $scale);
    }

    public function sub(string $num1, string $num2, int $scale = 5): string
    {
        return bcsub($num1, $num2, $scale);
    }

    public function multiply(string $num1, string $num2, int $scale = 5): string
    {
        return bcmul($num1, $num2, $scale);
    }

    public function convertFloat($floatAsString): string
    {
        $norm = (string)(float)$floatAsString;

        if (($e = strrchr($norm, 'E')) === false) {
            return $norm;
        }

        return number_format((float)$norm, -(int) substr($e, 1));
    }

    public function bcceil($number): mixed
    {
        if (str_contains($number, '.')) {
            if (preg_match("~\.[0]+$~", $number)) {
                return $this->bcround($number, 0);
            }
            if ($number[0] !== '-') {
                return bcadd($number, '1', 0);
            }

            return bcsub($number, '0', 0);
        }

        return $number;
    }

    public function bcfloor($number): mixed
    {
        if (str_contains($number, '.')) {
            if (preg_match("~\.[0]+$~", $number)) {
                return $this->bcround($number, 0);
            }
            if ($number[0] !== '-') {
                return bcadd($number, '0', 0);
            }

            return bcsub($number, '1', 0);
        }

        return $number;
    }

    public function bcround($number, $precision = 0): mixed
    {
        if (str_contains($number, '.')) {
            if ($number[0] !== '-') {
                return bcadd($number, '0.'.str_repeat('0', $precision).'5', $precision);
            }

            return bcsub($number, '0.'.str_repeat('0', $precision).'5', $precision);
        }

        return $number;
    }
}
