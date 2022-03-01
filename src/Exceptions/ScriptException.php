<?php

declare(strict_types=1);

namespace CommissionFeeCalculation\Exceptions;

class ScriptException extends \Exception
{
    public const ERROR_FILE_NOT_FOUNT = 'file_not_found';

    public const ERROR_FILE_NAME_NOT_SPECIFIED = 'file_name_not_specified';

    public const ERROR_FILE_TOO_BIG = 'file_too_big';

    public const ERROR_NOT_ACCESSIBLE_TYPE = 'Not accessible type.';

    public const ERROR_USER_TYPE_HANDLER_NOT_FOUND = 'User type not acceptable.';
}
