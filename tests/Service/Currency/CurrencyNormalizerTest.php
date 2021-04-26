<?php

declare(strict_types=1);

namespace Test\Service\Currency;

use App\Currency\Currency;
use App\Currency\Eur;
use App\Currency\Jpy;
use App\Currency\Usd;
use App\Service\Currency\CurrencyNormalizer;
use App\Service\Math;
use PHPUnit\Framework\TestCase;

class CurrencyNormalizerTest extends TestCase
{
    private CurrencyNormalizer $normalizer;

    public function setUp(): void
    {
        parent::setUp();

        $this->normalizer = new CurrencyNormalizer(new Math(3));
    }

    /**
     * @dataProvider normalizeData
     *
     * @param string $amount
     * @param Currency $currency
     * @param string $expected
     */
    public function testNormalize(string $amount, Currency $currency, string $expected): void {
        $this->assertEquals($expected, $this->normalizer->normalize($amount, $currency));
    }

    public function normalizeData(): array
    {
        return [
            'normalize amount in JPY' => ['123.001', new Jpy(), '124'],
            'normalize amount in EUR' => ['123.001', new Eur(), '123.01'],
            'normalize amount in USD'=> ['123.0001', new Usd(), '123.01'],
        ];
    }
}
