<?php

declare(strict_types=1);

namespace App\Service\CommissionCalculator;

use App\DataTransferObject\OperationRecord;
use App\Service\CommissionCalculator\Strategy\CalculationStrategyProvider;
use App\Service\Currency\CurrencyNormalizer;

class CommissionCalculator
{
    private CalculationStrategyProvider $calculationStrategyProvider;
    private CurrencyNormalizer $currencyNormalizer;

    public function __construct(
        CalculationStrategyProvider $calculationStrategyProvider,
        CurrencyNormalizer $currencyNormalizer
    ) {
        $this->calculationStrategyProvider = $calculationStrategyProvider;
        $this->currencyNormalizer = $currencyNormalizer;
    }

    public function calculate(OperationRecord $operation): string
    {
        $commission = $this->calculationStrategyProvider
            ->getStrategyForOperation($operation)
            ->calculateCommission($operation);

        return $this->currencyNormalizer->normalize($commission, $operation->getCurrency());
    }
}
