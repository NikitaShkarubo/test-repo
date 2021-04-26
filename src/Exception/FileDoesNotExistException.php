<?php

declare(strict_types=1);

namespace App\Exception;

class FileDoesNotExistException extends \Exception
{
    protected $message = 'File does not exist.';
}
