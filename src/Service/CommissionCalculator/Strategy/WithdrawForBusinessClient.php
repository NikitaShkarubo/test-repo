<?php

declare(strict_types=1);

namespace App\Service\CommissionCalculator\Strategy;

use App\DataTransferObject\OperationRecord;
use App\Enum\OperationType;
use App\Enum\UserType;
use App\Exception\CommissionCalculator\Strategy\WrongOperationTypeException;
use App\Exception\CommissionCalculator\Strategy\WrongUserTypeException;

class WithdrawForBusinessClient extends CommissionCalculationStrategy
{
    public function calculateCommission(OperationRecord $operation): string
    {
        $this->validateOperation($operation);

        return $this->calculateRawCommission($operation->getAmount());
    }

    protected function validateOperation(OperationRecord $operation): void
    {
        if (!$operation->getOperationType()->equals(OperationType::withdraw())) {
            throw new WrongOperationTypeException();
        }

        if (!$operation->getUserType()->equals(UserType::business())) {
            throw new WrongUserTypeException();
        }
    }
}
