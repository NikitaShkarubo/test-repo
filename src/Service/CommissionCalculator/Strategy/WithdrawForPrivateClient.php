<?php

declare(strict_types=1);

namespace App\Service\CommissionCalculator\Strategy;

use App\Currency\Currency;
use App\DataTransferObject\FreeOperationLimitsData;
use App\DataTransferObject\OperationRecord;
use App\Enum\OperationType;
use App\Enum\UserType;
use App\Exception\CommissionCalculator\Strategy\WrongOperationTypeException;
use App\Exception\CommissionCalculator\Strategy\WrongUserTypeException;
use App\Service\Currency\CurrencyFactory;
use App\Service\Math;
use App\Service\CommissionCalculator\UserFreeOperationLimitsStorage;
use App\Service\Currency\CurrencyExchanger;

class WithdrawForPrivateClient extends CommissionCalculationStrategy
{
    private CurrencyExchanger $currencyExchanger;
    private UserFreeOperationLimitsStorage $userFreeOperationLimitsStorage;
    private int $numberOfFreeOperationsLimit;
    private string $timePeriodOfFreeOperationsLimit;
    private string $amountOfFreeOperationsLimit;
    private Currency $baseCurrency;

    public function __construct(
        Math $mathService,
        CurrencyExchanger $currencyExchanger,
        UserFreeOperationLimitsStorage $userFreeOperationsManager,
        CurrencyFactory $currencyFactory,
        string $commissionCoefficient,
        int $numberOfFreeOperationsLimit,
        string $amountOfFreeOperationsLimit,
        string $timePeriodOfFreeOperationsLimit,
        string $baseCurrencyCode
    ) {
        parent::__construct($mathService, $commissionCoefficient);

        $this->currencyExchanger = $currencyExchanger;
        $this->userFreeOperationLimitsStorage = $userFreeOperationsManager;
        $this->numberOfFreeOperationsLimit = $numberOfFreeOperationsLimit;
        $this->amountOfFreeOperationsLimit = $amountOfFreeOperationsLimit;
        $this->timePeriodOfFreeOperationsLimit = $timePeriodOfFreeOperationsLimit;
        $this->baseCurrency = $currencyFactory->getCurrencyModelByCode($baseCurrencyCode);
    }

    public function calculateCommission(OperationRecord $operation): string
    {
        $this->validateOperation($operation);

        $currency = $operation->getCurrency();

        if (!$currency instanceof $this->baseCurrency) {
            $exchangeInfo = $this->currencyExchanger->exchangeCurrency(
                $operation->getAmount(),
                $currency,
                $this->baseCurrency
            );
            $amount = $exchangeInfo->getResultAmount();
        } else {
            $amount = $operation->getAmount();
        }

        $amount = $this->math->subtract($amount, $this->getAmountFreeOfCharge($operation, $amount));
        $commission = $this->calculateRawCommission($amount);

        if (!$currency instanceof $this->baseCurrency) {
            $exchangeInfo = $this->currencyExchanger->exchangeCurrency(
                $commission,
                $this->baseCurrency,
                $currency,
                $this->math->divide('1', $exchangeInfo->getRate())
            );
            $commission = $exchangeInfo->getResultAmount();
        }

        return $commission;
    }

    private function getAmountFreeOfCharge(OperationRecord $operation, string $euroAmount): string
    {
        $userId = $operation->getUserId();
        $userLimits = $this->userFreeOperationLimitsStorage->getDataByUserId($userId);
        $dateComparisonMethod = $this->dateComparisonMethodName();
        $currentOperationDate = $operation->getDate();
        $freeAmountRemainder = '0';
        $numberOfFreeOperations = 0;

        if (empty($userLimits) || !$currentOperationDate->$dateComparisonMethod($userLimits->getLastOperationDate())) {
            // If this is the first withdraw operation this period it will be free of charge
            if ($euroAmount > $this->amountOfFreeOperationsLimit) {
                // Partially free of charge
                $result = $this->amountOfFreeOperationsLimit;
            } else {
                // Or completely free of charge
                $freeAmountRemainder = $this->math->subtract($this->amountOfFreeOperationsLimit, $euroAmount);
                $numberOfFreeOperations = $this->numberOfFreeOperationsLimit - 1;
                $result = $euroAmount;
            }
        } else {
            // If withdraw operation have already been made this period
            $amountFreeOfChargeLimit = $userLimits->getAmountFreeOfCharge();
            $numberOfFreeOperationsLimit = $userLimits->getNumberOfFreeOperations();

            if ($amountFreeOfChargeLimit === 0 || $numberOfFreeOperationsLimit === 0) {
                // And we have exceeded the limits on the amount or number of free operations
                // we do not make any discounts
                $result = '0';
            } elseif ($amountFreeOfChargeLimit < $euroAmount) {
                // If the limits have not been reached, withdraw operation will be free of charge
                // Partially free of charge
                $result = $amountFreeOfChargeLimit;
                $numberOfFreeOperations = $numberOfFreeOperationsLimit - 1;
            } else {
                // Or completely free of charge
                $result = $euroAmount;
                $freeAmountRemainder = $this->math->subtract($amountFreeOfChargeLimit, $euroAmount);
                $numberOfFreeOperations = $numberOfFreeOperationsLimit - 1;
            }
        }

        $this->userFreeOperationLimitsStorage->addDataForUserId(
            $userId,
            new FreeOperationLimitsData($currentOperationDate, $freeAmountRemainder, $numberOfFreeOperations)
        );

        return $result;
    }

    private function dateComparisonMethodName(): string
    {
        $term = ucfirst(strtolower($this->timePeriodOfFreeOperationsLimit));

        return "isSame{$term}";
    }

    protected function validateOperation(OperationRecord $operation): void
    {
        if (!$operation->getOperationType()->equals(OperationType::withdraw())) {
            throw new WrongOperationTypeException();
        }

        if (!$operation->getUserType()->equals(UserType::private())) {
            throw new WrongUserTypeException();
        }
    }
}
