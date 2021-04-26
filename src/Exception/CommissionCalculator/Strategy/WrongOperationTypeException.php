<?php

declare(strict_types=1);

namespace App\Exception\CommissionCalculator\Strategy;

class WrongOperationTypeException extends \Exception
{
    protected $message = 'Wrong operation type.';
}
