<?php

namespace App\Exceptions;

use Exception;

class BlockedCardException extends Exception
{
    public function __construct(string $message = 'Card is blocked', int $code = 400)
    {
        parent::__construct($message, $code);
    }
}
