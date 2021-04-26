<?php

declare(strict_types=1);

namespace App\Command;

use App\Enum\OperationType;
use App\Enum\UserType;
use App\Service\CommissionCalculator\CommissionCalculator;
use App\Service\Currency\CurrencyFactory;
use App\DataTransferObject\OperationRecord;
use App\Service\Reader\ReaderProvider;
use Carbon\Carbon;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CalculateCommissionFeeCommand extends Command
{
    public const ERROR_MSG = 'Error occurred while command execution.';
    private const PATH_ARG = 'path';

    protected static $defaultName = 'calculate-commission-fee';
    private ReaderProvider $readerProvider;
    private CommissionCalculator $commissionCalculator;
    private CurrencyFactory $currencyFactory;

    public function __construct(
        ReaderProvider $readerProvider,
        CommissionCalculator $commissionCalculator,
        CurrencyFactory $currencyFactory
    ) {
        parent::__construct();
        $this->readerProvider = $readerProvider;
        $this->commissionCalculator = $commissionCalculator;
        $this->currencyFactory = $currencyFactory;
    }

    protected function configure()
    {
        $this->addArgument(self::PATH_ARG, InputArgument::REQUIRED, 'File path');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument(self::PATH_ARG);

        try {
            $reader = $this->readerProvider->getReader($filePath);
            foreach ($reader->getRecords() as $record) {
                $output->writeln($this->commissionCalculator->calculate($this->parseRecord($record)));
            }
        } catch (\Exception $e) {
            $output->writeln(self::ERROR_MSG . "\n{$e->getMessage()}");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function parseRecord(array $recordData): OperationRecord
    {
        return new OperationRecord(
            Carbon::createFromFormat('Y-m-d', $recordData[0]),
            (int) $recordData[1],
            new UserType($recordData[2]),
            new OperationType($recordData[3]),
            (string) $recordData[4],
            $this->currencyFactory->getCurrencyModelByCode($recordData[5])
        );
    }
}
