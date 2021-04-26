<?php

declare(strict_types=1);

namespace Test\Service\CommissionCalculator\Strategy;

use App\Currency\Eur;
use App\Currency\Jpy;
use App\Currency\Usd;
use App\DataTransferObject\OperationRecord;
use App\Enum\OperationType;
use App\Enum\UserType;
use App\Exception\CommissionCalculator\Strategy\WrongOperationTypeException;
use App\Exception\CommissionCalculator\Strategy\WrongUserTypeException;
use App\Service\CommissionCalculator\Strategy\WithdrawForBusinessClient;
use App\Service\Math;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class WithdrawForBusinessClientTest extends TestCase
{
    private WithdrawForBusinessClient $strategy;

    protected function setUp(): void
    {
        $this->strategy = new WithdrawForBusinessClient(new Math(3), '0.5');
    }

    /**
     * @dataProvider dataProvider
     *
     * @param OperationRecord $operation
     * @param string $expectedCommission
     */
    public function testCommissionCalculationForTwoOperationsInARow(
        OperationRecord $operation,
        string $expectedCommission
    ): void {
        $this->assertEquals($expectedCommission, $this->strategy->calculateCommission($operation));
    }

    public function testCommissionCalculationForOperationWithWrongType(): void
    {
        $this->expectException(WrongOperationTypeException::class);

        $this->strategy->calculateCommission(
            new OperationRecord(
                new Carbon(),
                1,
                UserType::business(),
                OperationType::deposit(),
                '1000.25',
                new Jpy()
            )
        );
    }

    public function testCommissionCalculationForUserWithWrongType()
    {
        $this->expectException(WrongUserTypeException::class);

        $this->strategy->calculateCommission(
            new OperationRecord(
                new Carbon(),
                1,
                UserType::private(),
                OperationType::withdraw(),
                '1000.25',
                new Jpy()
            )
        );
    }

    public function dataProvider(): array
    {
        $date = new Carbon();
        $userId = 1;

        return [
            [
                new OperationRecord(
                    $date, $userId, UserType::business(), OperationType::withdraw(), '1000.25', new Jpy()
                ),
                '5.001'
            ],
            [
                new OperationRecord(
                    $date, $userId, UserType::business(), OperationType::withdraw(), '10.2512', new Usd()
                ),
                '0.051'
            ],
            [
                new OperationRecord(
                    $date, $userId, UserType::business(), OperationType::withdraw(), '100', new Eur()
                ),
                '0.500'
            ],
        ];
    }
}
