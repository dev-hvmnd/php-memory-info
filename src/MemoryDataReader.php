<?php

declare(strict_types=1);

namespace DevHvmnd\MemoryInfo;

class MemoryDataReader
{
    public const PROC_MEMORY_INFO_FILE = '/proc/meminfo';

    public function __construct(private MemoryInfoParser $memoryInfoParser)
    {
    }

    public function getMemoryInfo(): MemoryInfo
    {
        $memoryInformation = file_get_contents(self::PROC_MEMORY_INFO_FILE);

        $memoryInformationData = $this->memoryInfoParser->parse($memoryInformation);

        extract($memoryInformationData, EXTR_OVERWRITE);

        return new MemoryInfo(
            $memTotal,
            $memFree,
            $memAvailable,
            $buffers,
            $cached,
            $swapCached,
            $active,
            $inactive,
            $activeAnon,
            $inactiveAnon,
            $activeFile,
            $inactiveFile,
            $unevictable,
            $mlocked,
            $swapTotal,
            $swapFree,
            $dirty,
            $writeback,
            $anonPages,
            $mapped,
            $shmem,
            $kReclaimable,
            $slab,
            $sReclaimable,
            $sUnreclaim,
            $kernelStack,
            $pageTables,
            $nfsUnstable,
            $bounce,
            $writebackTmp,
            $commitLimit,
            $committedAS,
            $vmallocTotal,
            $vmallocUsed,
            $vmallocChunk
        );
    }
}
