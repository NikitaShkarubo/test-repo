<?php

declare(strict_types=1);

namespace App\Exception\CommissionCalculator\Strategy;

class WrongUserTypeException extends \Exception
{
    protected $message = 'Wrong user type.';
}
