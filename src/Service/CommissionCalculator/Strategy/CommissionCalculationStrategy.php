<?php

declare(strict_types=1);

namespace App\Service\CommissionCalculator\Strategy;

use App\DataTransferObject\OperationRecord;
use App\Service\Math;

abstract class CommissionCalculationStrategy
{
    protected Math $math;
    protected string $commissionCoefficient;

    public function __construct(Math $math, string $commissionCoefficient)
    {
        $this->math = $math;
        $this->commissionCoefficient = $commissionCoefficient;
    }

    /**
     * @param string $amount
     * @return string
     *
     * Calculate commission by common formula
     */
    protected function calculateRawCommission(string $amount): string
    {
        return $this->math->divide(
            $this->math->multiply($amount, $this->commissionCoefficient),
            '100',
        );
    }

    /**
     * @param OperationRecord $operation
     * @return string
     *
     * Encapsulate complex calculation strategy logic
     */
    abstract public function calculateCommission(OperationRecord $operation): string;

    abstract protected function validateOperation(OperationRecord $operation): void;
}
