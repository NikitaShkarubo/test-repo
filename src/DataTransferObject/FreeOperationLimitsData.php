<?php

declare(strict_types=1);

namespace App\DataTransferObject;

use Carbon\Carbon;

class FreeOperationLimitsData
{
    private int $numberOfFreeOperations;
    private string $amountFreeOfCharge;
    private Carbon $operationDate;

    public function __construct(Carbon $operationDate, string $amountFreeOfCharge, int $numberOfFreeOperations)
    {
        $this->operationDate = $operationDate;
        $this->amountFreeOfCharge = $amountFreeOfCharge;
        $this->numberOfFreeOperations = $numberOfFreeOperations;
    }

    public function getNumberOfFreeOperations(): int
    {
        return $this->numberOfFreeOperations;
    }

    public function getAmountFreeOfCharge(): string
    {
        return $this->amountFreeOfCharge;
    }

    public function getLastOperationDate(): Carbon
    {
        return $this->operationDate;
    }
}
