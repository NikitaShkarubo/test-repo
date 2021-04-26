<?php

declare(strict_types=1);

namespace App\Service\Currency;

use App\Currency\Currency;
use App\Exception\Currency\UnsupportedCurrencyException;

class CurrencyFactory
{
    private string $currencyClassNamespace = 'App\\Currency\\';

    /**
     * @param string $code
     * @return Currency
     * @throws UnsupportedCurrencyException
     */
    public function getCurrencyModelByCode(string $code): Currency
    {
        $className = $this->currencyClassNamespace . ucfirst(strtolower($code));

        if (class_exists($className)) {
            return new $className();
        }

        throw new UnsupportedCurrencyException(sprintf('Currency "%s" is not supported', $code));
    }
}
