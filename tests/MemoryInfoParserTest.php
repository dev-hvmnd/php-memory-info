<?php

declare(strict_types=1);

namespace DevHvmnd\MemoryInfo\Tests;

use DevHvmnd\MemoryInfo\MemoryInfoParser;
use DevHvmnd\MemoryInfo\MemoryInfoReadException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class MemoryInfoParserTest extends TestCase
{
    public function testParsesRealisticMemoryInformation(): void
    {
        $memoryInfoParser = new MemoryInfoParser();

        $memoryInfo = $memoryInfoParser->parse($this->memoryInformationFixture());

        $this->assertSame(16_384_256 * 1024, $memoryInfo->memTotal);
        $this->assertSame(111_111 * 1024, $memoryInfo->activeAnon);
        $this->assertSame(444_444 * 1024, $memoryInfo->inactiveFile);
        $this->assertSame(5 * 1024, $memoryInfo->nfsUnstable);
        $this->assertSame(9_999_999 * 1024, $memoryInfo->committedAS);
        $this->assertSame(0, $memoryInfo->vmallocChunk);
    }

    public function testParsesLinuxProcMemoryInformationFixture(): void
    {
        $memoryInfoParser = new MemoryInfoParser();

        $memoryInfo = $memoryInfoParser->parse($this->linuxProcMemoryInformationFixture());

        $this->assertSame(32_776_548 * 1024, $memoryInfo->memTotal);
        $this->assertSame(24_567_890 * 1024, $memoryInfo->memAvailable);
        $this->assertSame(2_734_568 * 1024, $memoryInfo->activeFile);
        $this->assertSame(0, $memoryInfo->nfsUnstable);
        $this->assertSame(12_458_700 * 1024, $memoryInfo->committedAS);
        $this->assertSame(0, $memoryInfo->vmallocChunk);
    }

    public function testUsesNullWhenOptionalFieldIsMissing(): void
    {
        $memoryInfoParser = new MemoryInfoParser();

        $memoryInfo = $memoryInfoParser->parse($this->minimalMemoryInformationFixture());

        $this->assertSame(16_384_256 * 1024, $memoryInfo->memTotal);
        $this->assertSame(2_345_678 * 1024, $memoryInfo->cached);
        $this->assertSame(2_097_148 * 1024, $memoryInfo->swapTotal);
        $this->assertNull($memoryInfo->memAvailable);
        $this->assertNull($memoryInfo->activeAnon);
        $this->assertNull($memoryInfo->kReclaimable);
        $this->assertNull($memoryInfo->committedAS);
    }

    public function testThrowsExceptionWhenRequiredFieldIsMissing(): void
    {
        $memoryInfoParser = new MemoryInfoParser();

        $this->expectException(MemoryInfoReadException::class);
        $this->expectExceptionMessage('Missing memory information field "memTotal"');

        $memoryInfoParser->parse($this->memoryInformationFixture(['MemTotal' => null]));
    }

    public function testRejectsMalformedNonEmptyLine(): void
    {
        $memoryInfoParser = new MemoryInfoParser();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid memory information line 2');

        $memoryInfoParser->parse("MemTotal: 1 kB\nnot valid\n");
    }

    public function testRejectsValueWithoutNumber(): void
    {
        $memoryInfoParser = new MemoryInfoParser();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid memory information value on line 1');

        $memoryInfoParser->parse("MemTotal: no value kB\n");
    }

    /**
     * @param array<string, int|null> $overrides
     */
    private function memoryInformationFixture(array $overrides = []): string
    {
        $fields = [
            'MemTotal' => 16_384_256,
            'MemFree' => 123_456,
            'MemAvailable' => 789_012,
            'Buffers' => 34_567,
            'Cached' => 2_345_678,
            'SwapCached' => 12,
            'Active' => 3_456_789,
            'Inactive' => 4_567_890,
            'Active(anon)' => 111_111,
            'Inactive(anon)' => 222_222,
            'Active(file)' => 333_333,
            'Inactive(file)' => 444_444,
            'Unevictable' => 55,
            'Mlocked' => 44,
            'SwapTotal' => 2_097_148,
            'SwapFree' => 1_048_576,
            'Dirty' => 1_234,
            'Writeback' => 234,
            'AnonPages' => 555_555,
            'Mapped' => 66_666,
            'Shmem' => 77_777,
            'KReclaimable' => 88_888,
            'Slab' => 99_999,
            'SReclaimable' => 11_111,
            'SUnreclaim' => 22_222,
            'KernelStack' => 3_333,
            'PageTables' => 4_444,
            'NFS_Unstable' => 5,
            'Bounce' => 6,
            'WritebackTmp' => 7,
            'CommitLimit' => 8_888_888,
            'Committed_AS' => 9_999_999,
            'VmallocTotal' => 1_234_567,
            'VmallocUsed' => 123,
            'VmallocChunk' => 0,
            'HugePages_Total' => 0,
            'DirectMap4k' => 12_345,
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

    private function minimalMemoryInformationFixture(): string
    {
        return implode("\n", [
            'MemTotal: 16384256 kB',
            'MemFree: 123456 kB',
            'Buffers: 34567 kB',
            'Cached: 2345678 kB',
            'SwapTotal: 2097148 kB',
            'SwapFree: 1048576 kB',
        ]) . "\n";
    }

    private function linuxProcMemoryInformationFixture(): string
    {
        $fixture = file_get_contents(__DIR__ . '/Fixtures/proc-meminfo-linux-6.x.txt');

        if ($fixture === false) {
            self::fail('Unable to read Linux /proc/meminfo fixture.');
        }

        return $fixture;
    }
}
