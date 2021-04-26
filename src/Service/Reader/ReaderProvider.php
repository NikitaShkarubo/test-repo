<?php

declare(strict_types=1);

namespace App\Service\Reader;

use App\Exception\FileDoesNotExistException;
use App\Exception\UnsupportedFileExtension;
use League\Csv\Reader;

class ReaderProvider
{
    protected const SUPPORTED_EXTENSIONS = ['csv'];

    /**
     * @param string $filePath
     * @return ReaderInterface
     * @throws UnsupportedFileExtension|FileDoesNotExistException
     */
    public function getReader(string $filePath): ReaderInterface
    {
        if (!$this->fileExists($filePath)) {
            throw new FileDoesNotExistException();
        }

        if (!$this->fileIsSupported($filePath)) {
            throw new UnsupportedFileExtension();
        }

        if (pathinfo($filePath, \PATHINFO_EXTENSION) === 'csv') {
            return new CsvReaderAdapter(Reader::createFromPath($filePath));
        }
    }

    private function fileExists(string $path): bool
    {
        return file_exists($path);
    }

    private function fileIsSupported(string $filePath): bool
    {
        return in_array(pathinfo($filePath, \PATHINFO_EXTENSION), self::SUPPORTED_EXTENSIONS, true);
    }
}
