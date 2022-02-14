<?php

namespace CommissionFeeCalculation\Exceptions;

use Throwable;

class ScriptException extends \Exception
{
    public const ERROR_FILE_NOT_FOUNT = 'file_not_found';
    public const ERROR_FILE_NAME_NOT_SPECIFIED = 'file_name_not_specified';
    public const ERROR_FILE_TOO_BIG = 'file_too_big';
}
