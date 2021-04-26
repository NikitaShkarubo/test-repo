<?php

declare(strict_types=1);

namespace App\Service\Currency;

use App\Currency\Currency;
use App\DataTransferObject\CurrencyExchangeData;
use App\Exception\Currency\GettingCurrencyRateException;
use App\Exception\Currency\GettingRatesException;
use App\Service\Math;
use Carbon\Carbon;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CurrencyExchanger
{
    private HttpClientInterface $httpClient;
    private string $ratesSource;
    private array $rates;
    private Math $math;

    public function __construct(Math $math, string $ratesSource, HttpClientInterface $httpClient = null)
    {
        $this->ratesSource = $ratesSource;
        $this->httpClient = $httpClient ?? HttpClient::create();
        $this->math = $math;
    }

    public function exchangeCurrency(
        string $amount,
        Currency $fromCurrency,
        Currency $toCurrency,
        string $rate = ''
    ): CurrencyExchangeData {
        $rate = empty($rate) ? $this->getExchangeRate($fromCurrency, $toCurrency) : $rate;
        $resultAmount = $this->math->divide($amount, $rate);

        return new CurrencyExchangeData($fromCurrency, $toCurrency, $amount, $resultAmount, $rate);
    }

    /**
     * @param Currency $fromCurrency
     * @param Currency $toCurrency
     * @return string
     * @throws GettingCurrencyRateException
     * @throws GettingRatesException
     */
    private function getExchangeRate(Currency $fromCurrency, Currency $toCurrency): string
    {
        $fromCurrencyCode = $fromCurrency->getCode();
        $toCurrencyCode = $toCurrency->getCode();

        if ($fromCurrencyCode === $toCurrencyCode) {
            return '1';
        }

        if (empty($this->rates) || ($this->rates['date'] !== Carbon::now()->toDateString())) {
            $this->refreshRates();
        }

        $baseCurrencyCode = $this->rates['base'];
        $this->validateIfCurrencyCodePresentedInRates($fromCurrencyCode);
        $this->validateIfCurrencyCodePresentedInRates($toCurrencyCode);

        if ($fromCurrencyCode === $baseCurrencyCode) {
            return $this->math->divide('1', (string) $this->rates['rates'][$toCurrencyCode]);
        }

        if ($toCurrencyCode === $baseCurrencyCode) {
            return (string) $this->rates['rates'][$fromCurrencyCode];
        }

        return $this->math->divide(
            (string) $this->rates['rates'][$fromCurrencyCode],
            (string) $this->rates['rates'][$toCurrencyCode]
        );
    }

    private function refreshRates(): void
    {
        try {
            $response = $this->httpClient->request('GET', $this->ratesSource);
            $this->rates = $response->toArray();
        } catch (\Throwable $e) {
            throw new GettingRatesException();
        }
    }

    /**
     * @param string $currencyCode
     * @throws GettingCurrencyRateException
     */
    private function validateIfCurrencyCodePresentedInRates(string $currencyCode): void
    {
        if ($this->rates['base'] === $currencyCode || isset($this->rates['rates'][$currencyCode])) {
            return;
        }

        throw new GettingCurrencyRateException(
            sprintf('Error occurred while getting rate for "%s" currency', $currencyCode)
        );
    }
}
