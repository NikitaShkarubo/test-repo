<?php

declare(strict_types=1);

namespace App\Service\Currency;

use App\Currency\Currency;
use App\Service\Math;

class CurrencyNormalizer
{
    private Math $math;

    public function __construct(Math $math)
    {
        $this->math = $math;
    }

    /**
     * @param string $amount
     * @param Currency $currency
     * @return string
     *
     * Round amount up to currency's decimal places.
     * For example, 0.023 EUR should be rounded up to 0.03 EUR.
     * And 8611.41 JPY to 8612.
     */
    public function normalize(string $amount, Currency $currency): string
    {
        $decimalPlaces = $currency->getDecimalPlaces();

        $result = (float) $this->math->divide(
            (string) ceil((float) $this->math->multiply($amount, (string) 10 ** $decimalPlaces)),
            (string) 10 ** $decimalPlaces,
        );

        return number_format($result, $decimalPlaces, '.', '');
    }
}
