<?php

declare(strict_types=1);

namespace Test\Service\CommissionCalculator;

use App\DataTransferObject\FreeOperationLimitsData;
use App\Service\CommissionCalculator\UserFreeOperationLimitsStorage;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class UserFreeOperationLimitsStorageTest extends TestCase
{
    public function testSavingDataForUserId(): void
    {
        $testUserId = 1;
        $testData = new FreeOperationLimitsData(new Carbon(), '123.12', 5);
        $storage = new UserFreeOperationLimitsStorage();
        $storage->addDataForUserId($testUserId, $testData);
        $fetchedData = $storage->getDataByUserId($testUserId);

        $this->assertInstanceOf(FreeOperationLimitsData::class, $fetchedData);
        $this->assertEquals($testData->getAmountFreeOfCharge(), $fetchedData->getAmountFreeOfCharge());
        $this->assertEquals($testData->getLastOperationDate(), $fetchedData->getLastOperationDate());
        $this->assertEquals($testData->getNumberOfFreeOperations(), $fetchedData->getNumberOfFreeOperations());
        $this->assertNull($storage->getDataByUserId($testUserId + 1));
    }
}
