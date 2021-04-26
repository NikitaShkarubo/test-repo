<?php

declare(strict_types=1);

namespace App\Exception\Currency;

class GettingRatesException extends \Exception
{
    protected $message = 'Error occurred while fetching rates data from remote resource.';
}
