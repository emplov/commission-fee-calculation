<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Exceptions;

class NotParsableException extends \Exception
{
    protected $message = 'Haven\'t been able to parse.';
}
