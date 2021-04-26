<?php

declare(strict_types=1);

namespace App\Currency;

class Jpy extends Currency
{
    protected int $decimalPlaces = 0;
    protected string $code = 'JPY';
}
