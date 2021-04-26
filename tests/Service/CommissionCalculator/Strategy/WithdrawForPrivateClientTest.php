<?php

declare(strict_types=1);

namespace Test\Service\CommissionCalculator\Strategy;

use App\Currency\Eur;
use App\Currency\Jpy;
use App\DataTransferObject\CurrencyExchangeData;
use App\DataTransferObject\OperationRecord;
use App\Enum\OperationType;
use App\Enum\UserType;
use App\Exception\CommissionCalculator\Strategy\WrongOperationTypeException;
use App\Exception\CommissionCalculator\Strategy\WrongUserTypeException;
use App\Service\CommissionCalculator\Strategy\WithdrawForPrivateClient;
use App\Service\CommissionCalculator\UserFreeOperationLimitsStorage;
use App\Service\Currency\CurrencyExchanger;
use App\Service\Currency\CurrencyFactory;
use App\Service\Math;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class WithdrawForPrivateClientTest extends TestCase
{
    private WithdrawForPrivateClient $strategy;
    private Carbon $startOfWeek;

    protected function setUp(): void
    {
        $currencyExchanger = $this->createMock(CurrencyExchanger::class);
        $currencyExchangeData = $this->createMock(CurrencyExchangeData::class);
        $currencyExchangeData->method('getResultAmount')->willReturn('300.00');
        $currencyExchanger->method('exchangeCurrency')->willReturn($currencyExchangeData);
        $currencyFactory = $this->createMock(CurrencyFactory::class);
        $currencyFactory->method('getCurrencyModelByCode')->willReturn(new Eur());

        $this->strategy = new WithdrawForPrivateClient(
            new Math(3),
            $this->createMock(CurrencyExchanger::class),
            new UserFreeOperationLimitsStorage(),
            $currencyFactory,
            '0.5',
            3,
            '1000.00',
            'week',
            'EUR'
        );

        $this->startOfWeek = Carbon::createFromDate(2021, 02, 15); // Monday
        Carbon::setTestNow($this->startOfWeek);
    }

    /*
     * Check free operations amount limit.
     * Test three operations in same week.
     * The first and the second operation must be free of charge.
     * The last one - partially free of charge.
     */
    public function testThreeOperationsInOnePeriodMadeByOneUser()
    {
        $result = [];

        for ($i = 0; $i < 3; ++$i) {
            $result[] = $this->strategy->calculateCommission(new OperationRecord(
                $this->startOfWeek->addDay(),
                1,
                UserType::private(),
                OperationType::withdraw(),
                '400.00',
                new Eur()
            ));
        }

        $this->assertEquals(['0.000', '0.000', '1.000'], $result);
        Carbon::setTestNow();
    }

    /*
     * Check free operations number.
     * Test four operations in same week.
     * The first and the second operation must be free of charge.
     * The last one - partially free of charge.
     */
    public function testFourOperationsInOnePeriodMadeByOneUser()
    {
        $result = [];

        for ($i = 0; $i < 4; ++$i) {
            $result[] = $this->strategy->calculateCommission(new OperationRecord(
                $this->startOfWeek->addDay(),
                1,
                UserType::private(),
                OperationType::withdraw(),
                '100.00',
                new Eur()
            ));
        }

        $this->assertEquals(['0.000', '0.000', '0.000', '0.500'], $result);
        Carbon::setTestNow();
    }

    /*
     * Check free operations amount limit.
     * Test one operation which amount over the free operations amount limit.
     * Operation must be partially free of charge.
     */
    public function testOneOperationMadeByOneUserWithInExcessOfTheFreeAmountLimit()
    {
        $this->assertEquals('0.500', $this->strategy->calculateCommission(new OperationRecord(
            new Carbon(),
            1,
            UserType::private(),
            OperationType::withdraw(),
            '1100.00',
            new Eur()
        )));
    }
    
    /*
     * Test three operations in same week made by different users.
     * All operations must be free of charge.
     */
    public function testThreeOperationsInOnePeriodMadeByFewUsers()
    {
        $result = [];

        for ($i = 0; $i < 3; ++$i) {
            $result[] = $this->strategy->calculateCommission(new OperationRecord(
                $this->startOfWeek->addDay(),
                $i + 1,
                UserType::private(),
                OperationType::withdraw(),
                '1000.00',
                new Eur()
            ));
        }

        $this->assertEquals(['0.000', '0.000', '0.000'], $result);
        Carbon::setTestNow();
    }

    /*
     * Test two operations made in different weeks made by one user.
     * All operations must be free of charge.
     */
    public function testCommissionCalculation()
    {
        $result = [];

        for ($i = 0; $i < 2; ++$i) {
            $result[] = $this->strategy->calculateCommission(new OperationRecord(
                $this->startOfWeek->addWeek(),
                1,
                UserType::private(),
                OperationType::withdraw(),
                '1000.00',
                new Eur()
            ));
        }

        $this->assertEquals(['0.000', '0.000'], $result);
        Carbon::setTestNow();
    }

    public function testCommissionCalculationForOperationWithWrongType()
    {
        $this->expectException(WrongOperationTypeException::class);

        $this->strategy->calculateCommission(
            new OperationRecord(
                new Carbon(),
                1,
                UserType::private(),
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
                UserType::business(),
                OperationType::withdraw(),
                '1000.25',
                new Jpy()
            )
        );
    }
}
