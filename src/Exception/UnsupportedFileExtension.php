<?php

declare(strict_types=1);

namespace App\Exception;

class UnsupportedFileExtension extends \Exception
{
    protected $message = 'Unsupported file extension.';
}
