<?php

namespace App\Exceptions;

use Exception;

class InsufficientPointsException extends Exception
{
    public function __construct(string $message = 'Insufficient points', int $code = 400)
    {
        parent::__construct($message, $code);
    }
}
