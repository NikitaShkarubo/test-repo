<?php

declare(strict_types=1);

namespace App\DataTransferObject;

use App\Currency\Currency;

class CurrencyExchangeData
{
    private Currency $initialCurrency;
    private Currency $resultCurrency;
    private string $initialAmount;
    private string $resultAmount;
    private string $rate;

    public function __construct(
        Currency $initialCurrency,
        Currency $resultCurrency,
        string $initialAmount,
        string $resultAmount,
        string $rate
    ) {
        $this->initialCurrency = $initialCurrency;
        $this->resultCurrency = $resultCurrency;
        $this->initialAmount = $initialAmount;
        $this->resultAmount = $resultAmount;
        $this->rate = $rate;
    }

    public function getInitialCurrency(): Currency
    {
        return $this->initialCurrency;
    }

    public function getResultCurrency(): Currency
    {
        return $this->resultCurrency;
    }

    public function getRate(): string
    {
        return $this->rate;
    }

    public function getInitialAmount(): string
    {
        return $this->initialAmount;
    }

    public function getResultAmount(): string
    {
        return $this->resultAmount;
    }
}
