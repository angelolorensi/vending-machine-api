<?php

namespace App\Contracts;

interface ActionContract
{
    public function execute(mixed ...$params): mixed;
}
