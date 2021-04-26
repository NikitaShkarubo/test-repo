<?php

declare(strict_types=1);

namespace App\Service\CommissionCalculator\Strategy;

use App\DataTransferObject\OperationRecord;
use App\Enum\OperationType;
use App\Enum\UserType;
use App\Exception\CommissionCalculator\UnsupportedOperationTypeException;
use App\Exception\CommissionCalculator\UnsupportedUserTypeException;

class CalculationStrategyProvider
{
    private WithdrawForBusinessClient $withdrawForBusinessClient;
    private WithdrawForPrivateClient $withdrawForPrivateClient;
    private Deposit $deposit;

    public function __construct(
        WithdrawForBusinessClient $withdrawForBusinessClient,
        WithdrawForPrivateClient $withdrawForPrivateClient,
        Deposit $deposit
    ) {
        $this->withdrawForBusinessClient = $withdrawForBusinessClient;
        $this->withdrawForPrivateClient = $withdrawForPrivateClient;
        $this->deposit = $deposit;
    }

    /**
     * @param OperationRecord $operation
     * @return CommissionCalculationStrategy
     * @throws UnsupportedUserTypeException|UnsupportedOperationTypeException
     */
    public function getStrategyForOperation(OperationRecord $operation): CommissionCalculationStrategy
    {
        $operationType = $operation->getOperationType();

        if ($operationType->equals(OperationType::deposit())) {
            return $this->deposit;
        } elseif ($operationType->equals(OperationType::withdraw())) {
            $userType = $operation->getUserType();

            if ($userType->equals(UserType::private())) {
                return $this->withdrawForPrivateClient;
            } elseif ($userType->equals(UserType::business())) {
                return $this->withdrawForBusinessClient;
            } else {
                throw new UnsupportedUserTypeException(
                    sprintf('Commission can not be calculated for user type "%s": ', $operation->getUserType())
                );
            }
        } else {
            throw new UnsupportedOperationTypeException(
                sprintf('Commission can not be calculated for operation type "%s": ', $operationType)
            );
        }
    }
}
