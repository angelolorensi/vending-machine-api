<?php

namespace App\Exceptions;

use Exception;

class DailyLimitExceededException extends Exception
{
    public function __construct(string $message = 'Daily limit exceeded', int $code = 400)
    {
        parent::__construct($message, $code);
    }
}
