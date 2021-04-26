<?php

declare(strict_types=1);

namespace Test\Service\CommissionCalculator\Strategy;

use App\Currency\Eur;
use App\DataTransferObject\OperationRecord;
use App\Enum\OperationType;
use App\Enum\UserType;
use App\Exception\CommissionCalculator\UnsupportedOperationTypeException;
use App\Exception\CommissionCalculator\UnsupportedUserTypeException;
use App\Service\CommissionCalculator\Strategy\CalculationStrategyProvider;
use App\Service\CommissionCalculator\Strategy\Deposit;
use App\Service\CommissionCalculator\Strategy\WithdrawForBusinessClient;
use App\Service\CommissionCalculator\Strategy\WithdrawForPrivateClient;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class CalculationStrategyProviderTest extends TestCase
{
    private CalculationStrategyProvider $provider;

    protected function setUp(): void
    {
        $this->provider = new CalculationStrategyProvider(
            $this->createStub(WithdrawForBusinessClient::class),
            $this->createStub(WithdrawForPrivateClient::class),
            $this->createStub(Deposit::class),
        );
    }

    /**
     * @dataProvider operationsData
     * @param UserType $userType
     * @param OperationType $operationType
     * @param string $strategyClass
     * @throws UnsupportedOperationTypeException
     * @throws UnsupportedUserTypeException
     */
    public function testProvidingValidCalculationStrategy(
        UserType $userType,
        OperationType $operationType,
        string $strategyClass
    ): void {
        $operationRecord = new OperationRecord(
            new Carbon,
            1,
            $userType,
            $operationType,
            '100',
            new Eur()
        );
        $strategy = $this->provider->getStrategyForOperation($operationRecord);

        $this->assertInstanceOf($strategyClass, $strategy);
    }

    public function operationsData(): array
    {
        return [
            'Providing withdraw strategy for business client' => [
                UserType::business(), OperationType::withdraw(), WithdrawForBusinessClient::class
            ],
            'Providing withdraw strategy for private client' => [
                UserType::private(), OperationType::withdraw(), WithdrawForPrivateClient::class
            ],
            'Providing deposit strategy for business client' => [
                UserType::business(), OperationType::deposit(), Deposit::class
            ],
            'Providing deposit strategy for private client' => [
                UserType::private(), OperationType::deposit(), Deposit::class
            ],
        ];
    }
}
