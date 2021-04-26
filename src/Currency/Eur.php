<?php

declare(strict_types=1);

namespace App\Currency;

class Eur extends Currency
{
    protected int $decimalPlaces = 2;
    protected string $code = 'EUR';
}
