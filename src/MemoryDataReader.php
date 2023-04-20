<?php

declare(strict_types=1);

class MemoryDataReader
{
    public const PROC_MEMINFO_FILE = '/proc/meminfo';

    public function getMemoryInfo(): MemoryInfo
    {
        $memoryInformation = file(self::PROC_MEMINFO_FILE);
        $memoryInformationData = [];

        foreach ($memoryInformation as $line) {
            [$key, $value] = explode(":", $line);

            $key = trim($key);
            $key = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $key))));
            $value = (int)trim(string: preg_replace(pattern: '/^\D/', replacement: '', subject: $value)) * 1024;

            $memoryInformationData[$key] = $value;
        }

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
