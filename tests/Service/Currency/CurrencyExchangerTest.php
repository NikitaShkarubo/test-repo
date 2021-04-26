<?php

declare(strict_types=1);

namespace Test\Service\Currency;

use App\Currency\Eur;
use App\Currency\Jpy;
use App\Service\Currency\CurrencyExchanger;
use App\Service\Math;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CurrencyExchangerTest extends TestCase
{
    private CurrencyExchanger $exchanger;

    public function setUp(): void
    {
        parent::setUp();

        $this->exchanger = new CurrencyExchanger(
            new Math(3),
            'http://random-url.com',
            $this->createMock(HttpClientInterface::class)
        );
    }

    public function testExchangeFormJpyToEuro(): void
    {
        $initialAmount = '12000';
        $exchangeInfo = $this->exchanger->exchangeCurrency($initialAmount, new Jpy(), new Eur(), '126.12');

        $this->assertEquals($exchangeInfo->getInitialAmount(), $initialAmount);
        $this->assertEquals('95.147', $exchangeInfo->getResultAmount());
        $this->assertInstanceOf(Jpy::class, $exchangeInfo->getInitialCurrency());
        $this->assertInstanceOf(Eur::class, $exchangeInfo->getResultCurrency());
    }

    public function testExchangeFromEuroToJpy(): void
    {
        $euroAmount = '12';
        $exchangeInfo = $this->exchanger->exchangeCurrency($euroAmount, new Eur(), new Jpy(), '0.007928');

        $this->assertEquals($exchangeInfo->getInitialAmount(), $euroAmount);
        $this->assertEquals('1513.622', $exchangeInfo->getResultAmount());
        $this->assertInstanceOf(Jpy::class, $exchangeInfo->getResultCurrency());
        $this->assertInstanceOf(Eur::class, $exchangeInfo->getInitialCurrency());
    }
}
