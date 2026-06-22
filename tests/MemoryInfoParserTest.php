<?php

declare(strict_types=1);

namespace DevHvmnd\MemoryInfo\Tests;

use DevHvmnd\MemoryInfo\MemoryInfoParser;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class MemoryInfoParserTest extends TestCase
{
    public function testParsesRealisticMemoryInformation(): void
    {
        $parser = new MemoryInfoParser();

        $memoryInformationData = $parser->parse($this->memoryInformationFixture());

        self::assertSame(16_384_256 * 1024, $memoryInformationData['memTotal']);
        self::assertSame(111_111 * 1024, $memoryInformationData['activeAnon']);
        self::assertSame(444_444 * 1024, $memoryInformationData['inactiveFile']);
        self::assertSame(5 * 1024, $memoryInformationData['nfsUnstable']);
        self::assertSame(9_999_999 * 1024, $memoryInformationData['committedAS']);
        self::assertSame(0, $memoryInformationData['hugePagesTotal']);
        self::assertSame(12_345 * 1024, $memoryInformationData['directMap4k']);
    }

    public function testRejectsMalformedNonEmptyLine(): void
    {
        $parser = new MemoryInfoParser();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid memory information line 2');

        $parser->parse("MemTotal: 1 kB\nnot valid\n");
    }

    public function testRejectsValueWithoutNumber(): void
    {
        $parser = new MemoryInfoParser();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid memory information value on line 1');

        $parser->parse("MemTotal: no value kB\n");
    }

    private function memoryInformationFixture(): string
    {
        return <<<'MEMORY_INFORMATION'
MemTotal:       16384256 kB
MemFree:         123456 kB
MemAvailable:   789012 kB
Buffers:          34567 kB
Cached:         2345678 kB
SwapCached:          12 kB
Active:         3456789 kB
Inactive:       4567890 kB
Active(anon):    111111 kB
Inactive(anon):  222222 kB
Active(file):    333333 kB
Inactive(file):  444444 kB
Unevictable:         55 kB
Mlocked:             44 kB
SwapTotal:      2097148 kB
SwapFree:       1048576 kB
Dirty:             1234 kB
Writeback:          234 kB
AnonPages:       555555 kB
Mapped:           66666 kB
Shmem:            77777 kB
KReclaimable:     88888 kB
Slab:             99999 kB
SReclaimable:     11111 kB
SUnreclaim:       22222 kB
KernelStack:       3333 kB
PageTables:        4444 kB
NFS_Unstable:         5 kB
Bounce:               6 kB
WritebackTmp:         7 kB
CommitLimit:    8888888 kB
Committed_AS:   9999999 kB
VmallocTotal:   1234567 kB
VmallocUsed:        123 kB
VmallocChunk:         0 kB
HugePages_Total:      0
DirectMap4k:      12345 kB

MEMORY_INFORMATION;
    }
}
