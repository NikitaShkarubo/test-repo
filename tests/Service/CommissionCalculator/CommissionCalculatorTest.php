<?php

declare(strict_types=1);

namespace Test\Service\CommissionCalculator;

use App\DataTransferObject\OperationRecord;
use App\Service\CommissionCalculator\Strategy\CalculationStrategyProvider;
use App\Service\CommissionCalculator\CommissionCalculator;
use App\Service\Currency\CurrencyNormalizer;
use PHPUnit\Framework\TestCase;

class CommissionCalculatorTest extends TestCase
{
    public function testCommissionCalculation()
    {
        $testCommissionFee = '100.12';
        $strategyProviderMock = $this->createMock(CalculationStrategyProvider::class);
        $currencyNormalizerMock = $this->createMock(CurrencyNormalizer::class);
        $currencyNormalizerMock->method('normalize')->willReturn($testCommissionFee);
        $operationRecord = $this->createStub(OperationRecord::class);
        $calculator = new CommissionCalculator($strategyProviderMock, $currencyNormalizerMock);

        $this->assertEquals($testCommissionFee, $calculator->calculate($operationRecord));
    }
}
