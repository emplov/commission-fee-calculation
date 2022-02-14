<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Exceptions;

class NotAccessableExtensionException extends \Exception
{
    protected $message = 'Not accessible type.'.PHP_EOL;
}
