<?php

declare(strict_types=1);

namespace App\Service\CommissionCalculator\Strategy;

use App\DataTransferObject\OperationRecord;
use App\Enum\OperationType;
use App\Exception\CommissionCalculator\Strategy\WrongOperationTypeException;

class Deposit extends CommissionCalculationStrategy
{
    public function calculateCommission(OperationRecord $operation): string
    {
        $this->validateOperation($operation);

        return $this->calculateRawCommission($operation->getAmount());
    }

    protected function validateOperation(OperationRecord $operation): void
    {
        if (!$operation->getOperationType()->equals(OperationType::deposit())) {
            throw new WrongOperationTypeException();
        }
    }
}
