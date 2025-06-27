<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case SUCCESS = 'success';
    case FAILURE = 'failure';
}
