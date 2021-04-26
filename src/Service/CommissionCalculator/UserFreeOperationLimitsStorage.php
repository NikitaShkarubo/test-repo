<?php

declare(strict_types=1);

namespace App\Service\CommissionCalculator;

use App\DataTransferObject\FreeOperationLimitsData;

class UserFreeOperationLimitsStorage
{
    private array $freeOperationsData = [];

    public function getDataByUserId(int $userId): ?FreeOperationLimitsData
    {
        if (isset($this->freeOperationsData[$userId])) {
            return unserialize($this->freeOperationsData[$userId]);
        }

        return null;
    }

    public function addDataForUserId(int $userId, FreeOperationLimitsData $data): void
    {
        $this->freeOperationsData[$userId] = serialize($data);
    }
}
