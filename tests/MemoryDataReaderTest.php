<?php

declare(strict_types=1);

namespace DevHvmnd\MemoryInfo\Tests;

use DevHvmnd\MemoryInfo\MemoryDataReader;
use DevHvmnd\MemoryInfo\MemoryInfoParser;
use DevHvmnd\MemoryInfo\MemoryInfoReadException;
use PHPUnit\Framework\TestCase;

final class MemoryDataReaderTest extends TestCase
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
        $memoryDataReader = new MemoryDataReader(new MemoryInfoParser(), $memoryInfoFile);

        $memoryInfo = $memoryDataReader->getMemoryInfo();

        $this->assertSame(1024, $memoryInfo->memTotal);
        $this->assertSame(9 * 1024, $memoryInfo->activeAnon);
        $this->assertSame(28 * 1024, $memoryInfo->nfsUnstable);
        $this->assertSame(32 * 1024, $memoryInfo->committedAS);
        $this->assertSame(35 * 1024, $memoryInfo->vmallocChunk);
    }

    public function testBuildsMemoryInfoWithDefaultParserFromConfiguredFile(): void
    {
        $memoryInfoFile = $this->createTemporaryMemoryInfoFile($this->memoryInformationFixture());
        $memoryDataReader = new MemoryDataReader(memoryInformationFile: $memoryInfoFile);

        $memoryInfo = $memoryDataReader->getMemoryInfo();

        $this->assertSame(1024, $memoryInfo->memTotal);
        $this->assertSame(2 * 1024, $memoryInfo->memFree);
    }

    public function testThrowsExceptionForUnreadableMemoryInfoFile(): void
    {
        $missingMemoryInfoFile = sys_get_temp_dir()
            . DIRECTORY_SEPARATOR
            . 'missing-meminfo-'
            . str_replace('.', '', uniqid('', more_entropy: true));

        $memoryDataReader = new MemoryDataReader(new MemoryInfoParser(), $missingMemoryInfoFile);

        $this->expectException(MemoryInfoReadException::class);
        $this->expectExceptionMessage('is not readable');

        $memoryDataReader->getMemoryInfo();
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

    private function memoryInformationFixture(): string
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

        $lines = [];

        foreach ($fields as $field => $value) {
            $lines[] = sprintf('%s: %d kB', $field, $value);
        }

        return implode("\n", $lines) . "\n";
    }
}
