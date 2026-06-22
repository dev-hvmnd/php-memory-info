<?php

declare(strict_types=1);

namespace DevHvmnd\MemoryInfo;

readonly class MemoryInfo
{
    public function __construct(
        public int $memTotal,
        public int $memFree,
        public ?int $memAvailable,
        public int $buffers,
        public int $cached,
        public ?int $swapCached,
        public ?int $active,
        public ?int $inactive,
        public ?int $activeAnon,
        public ?int $inactiveAnon,
        public ?int $activeFile,
        public ?int $inactiveFile,
        public ?int $unevictable,
        public ?int $mlocked,
        public int $swapTotal,
        public int $swapFree,
        public ?int $dirty,
        public ?int $writeback,
        public ?int $anonPages,
        public ?int $mapped,
        public ?int $shmem,
        public ?int $kReclaimable,
        public ?int $slab,
        public ?int $sReclaimable,
        public ?int $sUnreclaim,
        public ?int $kernelStack,
        public ?int $pageTables,
        public ?int $nfsUnstable,
        public ?int $bounce,
        public ?int $writebackTmp,
        public ?int $commitLimit,
        public ?int $committedAS,
        public ?int $vmallocTotal,
        public ?int $vmallocUsed,
        public ?int $vmallocChunk
    ) {
    }
}
