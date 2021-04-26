<?php

declare(strict_types=1);

namespace Test\Command;

use App\Command\CalculateCommissionFeeCommand;
use App\Exception\FileDoesNotExistException;
use App\Service\CommissionCalculator\CommissionCalculator;
use App\Service\Currency\CurrencyFactory;
use App\Service\Reader\ReaderProvider;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use PHPUnit\Framework\TestCase;

class CalculateCommissionFeeCommandTest extends TestCase
{
    public function testExecutionWithThrowingException(): void
    {
        $commandTester = $this->prepareCommandTester(true);

        $commandTester->execute(['path' => 'random.csv']);
        $this->assertEquals(COMMAND::FAILURE, $commandTester->getStatusCode());
        $this->assertStringContainsString(
            CalculateCommissionFeeCommand::ERROR_MSG,
            $commandTester->getDisplay()
        );
    }

    public function testExecutionWithoutThrowingException(): void
    {
        $commandTester = $this->prepareCommandTester();

        $commandTester->execute(['path' => 'random.csv']);
        $this->assertEquals(COMMAND::SUCCESS, $commandTester->getStatusCode());
    }

    private function prepareCommandTester(bool $needToThrowException = false): CommandTester
    {
        $application = new Application();
        $readerProviderMock = $this->createMock(ReaderProvider::class);
        $commissionCalculatorMock = $this->createStub(CommissionCalculator::class);
        $currencyFactory = $this->createStub(CurrencyFactory::class);

        if ($needToThrowException) {
            $readerProviderMock->method('getReader')->willThrowException(new FileDoesNotExistException());
        }

        $application->add(
            new CalculateCommissionFeeCommand(
                $readerProviderMock,
                $commissionCalculatorMock,
                $currencyFactory
            )
        );
        $command = $application->find('calculate-commission-fee');

        return new CommandTester($command);
    }
}
