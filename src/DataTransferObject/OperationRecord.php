<?php

declare(strict_types=1);

namespace App\DataTransferObject;

use App\Currency\Currency;
use App\Enum\OperationType;
use App\Enum\UserType;
use Carbon\Carbon;

class OperationRecord
{
    private Carbon $date;
    private int $userId;
    private UserType $userType;
    private OperationType $operationType;
    private string $amount;
    private Currency $currency;

    public function __construct(
        Carbon $date,
        int $userId,
        UserType $userType,
        OperationType $operationType,
        string $amount,
        Currency $currency
    ) {

        $this->date = $date;
        $this->userId = $userId;
        $this->userType = $userType;
        $this->operationType = $operationType;
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function getDate(): Carbon
    {
        return $this->date;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getUserType(): UserType
    {
        return $this->userType;
    }

    public function getOperationType(): OperationType
    {
        return $this->operationType;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }
}
