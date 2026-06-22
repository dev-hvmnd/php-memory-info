<?php

declare(strict_types=1);

namespace DevHvmnd\MemoryInfo\Tests;

use DevHvmnd\MemoryInfo\MemoryDataReader;
use DevHvmnd\MemoryInfo\MemoryInfoParser;
use DevHvmnd\MemoryInfo\MemoryInfoReadException;
use PHPUnit\Framework\TestCase;

class MemoryDataReaderTest extends TestCase
{
    /**
     * @var list<string>
     */
    private array $temporaryFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->temporaryFiles as $temporaryFile) {
            if (is_file($temporaryFile)) {
                unlink($temporaryFile);
            }
        }

        $this->temporaryFiles = [];
    }

    public function testBuildsMemoryInfoFromConfiguredFile(): void
    {
        $memoryInfoFile = $this->createTemporaryMemoryInfoFile($this->memoryInformationFixture());
        $reader = new MemoryDataReader(new MemoryInfoParser(), $memoryInfoFile);

        $memoryInfo = $reader->getMemoryInfo();

        self::assertSame(1 * 1024, $memoryInfo->getMemTotal());
        self::assertSame(9 * 1024, $memoryInfo->getActiveAnon());
        self::assertSame(28 * 1024, $memoryInfo->getNfsUnstable());
        self::assertSame(32 * 1024, $memoryInfo->getCommittedAS());
        self::assertSame(35 * 1024, $memoryInfo->getVmallocChunk());
    }

    public function testThrowsExceptionForUnreadableMemoryInfoFile(): void
    {
        $missingMemoryInfoFile = sys_get_temp_dir()
            . DIRECTORY_SEPARATOR
            . 'missing-meminfo-'
            . str_replace('.', '', uniqid('', true));

        $reader = new MemoryDataReader(new MemoryInfoParser(), $missingMemoryInfoFile);

        $this->expectException(MemoryInfoReadException::class);
        $this->expectExceptionMessage('is not readable');

        $reader->getMemoryInfo();
    }

    public function testThrowsExceptionWhenRequiredFieldIsMissing(): void
    {
        $memoryInfoFile = $this->createTemporaryMemoryInfoFile(
            $this->memoryInformationFixture(['MemAvailable' => null])
        );
        $reader = new MemoryDataReader(new MemoryInfoParser(), $memoryInfoFile);

        $this->expectException(MemoryInfoReadException::class);
        $this->expectExceptionMessage('Missing memory information field "memAvailable"');

        $reader->getMemoryInfo();
    }

    private function createTemporaryMemoryInfoFile(string $contents): string
    {
        $temporaryFile = tempnam(sys_get_temp_dir(), 'meminfo_');

        if ($temporaryFile === false) {
            self::fail('Unable to create temporary memory information file.');
        }

        $bytesWritten = file_put_contents($temporaryFile, $contents);

        if ($bytesWritten === false) {
            self::fail(sprintf('Unable to write temporary memory information file "%s".', $temporaryFile));
        }

        $this->temporaryFiles[] = $temporaryFile;

        return $temporaryFile;
    }

    /**
     * @param array<string, int|null> $overrides
     */
    private function memoryInformationFixture(array $overrides = []): string
    {
        $fields = [
            'MemTotal' => 1,
            'MemFree' => 2,
            'MemAvailable' => 3,
            'Buffers' => 4,
            'Cached' => 5,
            'SwapCached' => 6,
            'Active' => 7,
            'Inactive' => 8,
            'Active(anon)' => 9,
            'Inactive(anon)' => 10,
            'Active(file)' => 11,
            'Inactive(file)' => 12,
            'Unevictable' => 13,
            'Mlocked' => 14,
            'SwapTotal' => 15,
            'SwapFree' => 16,
            'Dirty' => 17,
            'Writeback' => 18,
            'AnonPages' => 19,
            'Mapped' => 20,
            'Shmem' => 21,
            'KReclaimable' => 22,
            'Slab' => 23,
            'SReclaimable' => 24,
            'SUnreclaim' => 25,
            'KernelStack' => 26,
            'PageTables' => 27,
            'NFS_Unstable' => 28,
            'Bounce' => 29,
            'WritebackTmp' => 30,
            'CommitLimit' => 31,
            'Committed_AS' => 32,
            'VmallocTotal' => 33,
            'VmallocUsed' => 34,
            'VmallocChunk' => 35,
        ];

        foreach ($overrides as $field => $value) {
            if ($value === null) {
                unset($fields[$field]);
                continue;
            }

            $fields[$field] = $value;
        }

        $lines = [];

        foreach ($fields as $field => $value) {
            $lines[] = sprintf('%s: %d kB', $field, $value);
        }

        return implode("\n", $lines) . "\n";
    }
}
