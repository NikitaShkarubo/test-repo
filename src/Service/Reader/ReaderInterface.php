<?php

declare(strict_types=1);

namespace App\Service\Reader;

interface ReaderInterface
{
    public function getRecords(): \Iterator;
}