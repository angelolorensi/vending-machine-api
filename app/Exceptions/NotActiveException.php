<?php

namespace App\Exceptions;

use Exception;

class NotActiveException extends Exception
{
    public function __construct(string $message = 'Resource is not active', int $code = 400)
    {
        parent::__construct($message, $code);
    }
}
