<?php

declare(strict_types=1);

namespace Test\Service\Reader;

use App\Exception\FileDoesNotExistException;
use App\Exception\UnsupportedFileExtension;
use App\Service\Reader\ReaderInterface;
use App\Service\Reader\ReaderProvider;
use PHPUnit\Framework\TestCase;

class ReaderProviderTest extends TestCase
{
    private ReaderProvider $provider;

    protected function setUp(): void
    {
        $this->provider = new ReaderProvider();
    }

    public function testProvidingReaderForNonexistentFile(): void
    {
        $this->expectException(FileDoesNotExistException::class);

        $this->provider->getReader('nonexistent.csv');
    }

    public function testProvidingReaderForFileWithUnsupportedExtension(): void
    {
        $this->expectException(UnsupportedFileExtension::class);

        $this->provider->getReader(__DIR__ . '/test.wrong');
    }

    public function testProvidingReaderForValidFile()
    {
        $reader = $this->provider->getReader(__DIR__ . '/test.csv');

        $this->assertInstanceOf(ReaderInterface::class, $reader);
    }
}
