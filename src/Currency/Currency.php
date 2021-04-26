<?php

declare(strict_types=1);

namespace App\Currency;

abstract class Currency
{
    public function getDecimalPlaces(): int
    {
        return $this->decimalPlaces;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
