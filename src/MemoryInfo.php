<?php

declare(strict_types=1);

namespace DevHvmnd\MemoryInfo;

class MemoryInfo
{
    public function __construct(
        private int $memTotal,
        private int $memFree,
        private int $memAvailable,
        private int $buffers,
        private int $cached,
        private int $swapCached,
        private int $active,
        private int $inactive,
        private int $activeAnon,
        private int $inactiveAnon,
        private int $activeFile,
        private int $inactiveFile,
        private int $unevictable,
        private int $mlocked,
        private int $swapTotal,
        private int $swapFree,
        private int $dirty,
        private int $writeback,
        private int $anonPages,
        private int $mapped,
        private int $shmem,
        private int $kReclaimable,
        private int $slab,
        private int $sReclaimable,
        private int $sUnreclaim,
        private int $kernelStack,
        private int $pageTables,
        private int $nfsUnstable,
        private int $bounce,
        private int $writebackTmp,
        private int $commitLimit,
        private int $committedAS,
        private int $vmallocTotal,
        private int $vmallocUsed,
        private int $vmallocChunk
    ) {
    }

    /**
     * Returns the total amount of physical memory on the system.
     */
    public function getMemTotal(): int
    {
        return $this->memTotal;
    }

    /**
     * Returns the amount of physical memory currently unused on the system.
     */
    public function getMemFree(): int
    {
        return $this->memFree;
    }

    /**
     * Returns an estimate of how much memory is available for starting new applications, without swapping.
     */
    public function getMemAvailable(): int
    {
        return $this->memAvailable;
    }

    /**
     * Returns the amount of physical memory used for file buffers.
     */
    public function getBuffers(): int
    {
        return $this->buffers;
    }

    /**
     * Returns the amount of physical memory used for caching data read from disk.
     */
    public function getCached(): int
    {
        return $this->cached;
    }

    /**
     * Returns the amount of swap space currently used for caching data from the main memory.
     */
    public function getSwapCached(): int
    {
        return $this->swapCached;
    }

    /**
     * Returns the amount of active memory in use by the kernel and other processes.
     */
    public function getActive(): int
    {
        return $this->active;
    }

    /**
     * Returns the amount of inactive memory in the system.
     */
    public function getInactive(): int
    {
        return $this->inactive;
    }

    /**
     * Returns the amount of active anonymous memory (not associated with a file) in use by the kernel and other
     * processes.
     */
    public function getActiveAnon(): int
    {
        return $this->activeAnon;
    }

    /**
     * Returns the amount of inactive anonymous memory (not associated with a file) in the system.
     */
    public function getInactiveAnon(): int
    {
        return $this->inactiveAnon;
    }

    /**
     * Returns the amount of active file-backed memory in use by the kernel and other processes.
     */
    public function getActiveFile(): int
    {
        return $this->activeFile;
    }

    /**
     * Returns the amount of inactive file-backed memory in the system.
     */
    public function getInactiveFile(): int
    {
        return $this->inactiveFile;
    }

    /**
     * Returns the amount of unevictable memory (memory that cannot be swapped out) in the system.
     */
    public function getUnevictable(): int
    {
        return $this->unevictable;
    }

    /**
     * Returns the amount of mlocked memory (memory that cannot be paged out to disk) in the system.
     */
    public function getMlocked(): int
    {
        return $this->mlocked;
    }

    /**
     * Returns the total amount of swap space available on the system.
     */
    public function getSwapTotal(): int
    {
        return $this->swapTotal;
    }

    /**
     * Returns the amount of swap space currently unused on the system.
     */
    public function getSwapFree(): int
    {
        return $this->swapFree;
    }

    /**
     * Returns the amount of dirty memory (memory that needs to be written to disk) in the system.
     */
    public function getDirty(): int
    {
        return $this->dirty;
    }

    /**
     * Returns the amount of memory that is marked to be written back to disk.
     */
    public function getWriteback(): int
    {
        return $this->writeback;
    }

    /**
     * Returns the amount of anonymous memory (not associated with a file) in the system.
     */
    public function getAnonPages(): int
    {
        return $this->anonPages;
    }

    /**
     * Returns the amount of memory mapped into userspace processes.
     */
    public function getMapped(): int
    {
        return $this->mapped;
    }

    /**
     * Returns the amount of shared memory (memory that can be shared between processes) in the system.
     */
    public function getShmem(): int
    {
        return $this->shmem;
    }

    /**
     * Returns the amount of kernel reclaimable memory.
     */
    public function getKReclaimable(): int
    {
        return $this->kReclaimable;
    }

    /**
     * Returns the amount of memory used by the kernel to cache data structures for its own use.
     */
    public function getSlab(): int
    {
        return $this->slab;
    }

    /**
     * Returns the amount of kernel reclaimable memory that is currently being used for caches.
     */
    public function getSReclaimable(): int
    {
        return $this->sReclaimable;
    }

    /**
     * Returns the amount of kernel reclaimable memory that is not being used for caches.
     */
    public function getSUnreclaim(): int
    {
        return $this->sUnreclaim;
    }

    /**
     * Returns the amount of memory used by the kernel for stack space for each process.
     */
    public function getKernelStack(): int
    {
        return $this->kernelStack;
    }

    /**
     * Returns the amount of memory used by the kernel for page tables.
     */
    public function getPageTables(): int
    {
        return $this->pageTables;
    }

    /**
     * Returns the amount of NFS pages waiting to be written to the server.
     */
    public function getNfsUnstable(): int
    {
        return $this->nfsUnstable;
    }

    /**
     * Returns the amount of memory used for "bounce buffers" - temporary buffers used for data transfer between
     * devices.
     */
    public function getBounce(): int
    {
        return $this->bounce;
    }

    /**
     * Returns the amount of memory used for temporarily storing data being written back to disk.
     */
    public function getWritebackTmp(): int
    {
        return $this->writebackTmp;
    }

    /**
     * Returns the total amount of memory that can be used (including both physical memory and swap space).
     */
    public function getCommitLimit(): int
    {
        return $this->commitLimit;
    }

    /**
     * Returns the total amount of memory currently committed to the system
     * (including both physical memory and swap space).
     */
    public function getCommittedAS(): int
    {
        return $this->committedAS;
    }

    /**
     * Returns the total amount of virtual memory available on the system.
     */
    public function getVmallocTotal(): int
    {
        return $this->vmallocTotal;
    }

    /**
     * Returns the amount of virtual memory currently in use on the system.
     */
    public function getVmallocUsed(): int
    {
        return $this->vmallocUsed;
    }

    /**
     * Returns the largest contiguous block of virtual memory available on the system.
     */
    public function getVmallocChunk(): int
    {
        return $this->vmallocChunk;
    }
}
