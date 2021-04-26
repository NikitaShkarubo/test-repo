<?php

declare(strict_types=1);

namespace App\Service\Reader;

use League\Csv\Reader;

class CsvReaderAdapter implements ReaderInterface
{
    private Reader $reader;

    public function __construct(Reader $csvReaderAdaptee)
    {
        $this->reader = $csvReaderAdaptee;
    }

    public function getRecords(): \Iterator
    {
        return $this->reader->getRecords();
    }
}
