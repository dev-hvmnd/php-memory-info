<?php

declare(strict_types=1);

namespace DevHvmnd\MemoryInfo;

class MemoryDataReader
{
    public const PROC_MEMORY_INFO_FILE = '/proc/meminfo';

    /**
     * @var list<string>
     */
    private const REQUIRED_FIELDS = [
        'memTotal',
        'memFree',
        'memAvailable',
        'buffers',
        'cached',
        'swapCached',
        'active',
        'inactive',
        'activeAnon',
        'inactiveAnon',
        'activeFile',
        'inactiveFile',
        'unevictable',
        'mlocked',
        'swapTotal',
        'swapFree',
        'dirty',
        'writeback',
        'anonPages',
        'mapped',
        'shmem',
        'kReclaimable',
        'slab',
        'sReclaimable',
        'sUnreclaim',
        'kernelStack',
        'pageTables',
        'nfsUnstable',
        'bounce',
        'writebackTmp',
        'commitLimit',
        'committedAS',
        'vmallocTotal',
        'vmallocUsed',
        'vmallocChunk',
    ];

    public function __construct(
        private MemoryInfoParser $memoryInfoParser,
        private string $memoryInformationFile = self::PROC_MEMORY_INFO_FILE
    ) {
    }

    public function getMemoryInfo(): MemoryInfo
    {
        $memoryInformation = $this->readMemoryInformation();
        $memoryInformationData = $this->memoryInfoParser->parse($memoryInformation);

        $this->assertRequiredFieldsExist($memoryInformationData);

        return new MemoryInfo(
            $memoryInformationData['memTotal'],
            $memoryInformationData['memFree'],
            $memoryInformationData['memAvailable'],
            $memoryInformationData['buffers'],
            $memoryInformationData['cached'],
            $memoryInformationData['swapCached'],
            $memoryInformationData['active'],
            $memoryInformationData['inactive'],
            $memoryInformationData['activeAnon'],
            $memoryInformationData['inactiveAnon'],
            $memoryInformationData['activeFile'],
            $memoryInformationData['inactiveFile'],
            $memoryInformationData['unevictable'],
            $memoryInformationData['mlocked'],
            $memoryInformationData['swapTotal'],
            $memoryInformationData['swapFree'],
            $memoryInformationData['dirty'],
            $memoryInformationData['writeback'],
            $memoryInformationData['anonPages'],
            $memoryInformationData['mapped'],
            $memoryInformationData['shmem'],
            $memoryInformationData['kReclaimable'],
            $memoryInformationData['slab'],
            $memoryInformationData['sReclaimable'],
            $memoryInformationData['sUnreclaim'],
            $memoryInformationData['kernelStack'],
            $memoryInformationData['pageTables'],
            $memoryInformationData['nfsUnstable'],
            $memoryInformationData['bounce'],
            $memoryInformationData['writebackTmp'],
            $memoryInformationData['commitLimit'],
            $memoryInformationData['committedAS'],
            $memoryInformationData['vmallocTotal'],
            $memoryInformationData['vmallocUsed'],
            $memoryInformationData['vmallocChunk']
        );
    }

    private function readMemoryInformation(): string
    {
        if (!is_readable($this->memoryInformationFile)) {
            throw new MemoryInfoReadException(
                sprintf('Memory information file "%s" is not readable.', $this->memoryInformationFile)
            );
        }

        set_error_handler(static function (int $severity, string $message): void {
            throw new MemoryInfoReadException($message);
        });

        try {
            $memoryInformation = file_get_contents($this->memoryInformationFile);
        } catch (MemoryInfoReadException $exception) {
            throw new MemoryInfoReadException(
                sprintf(
                    'Unable to read memory information from "%s": %s',
                    $this->memoryInformationFile,
                    $exception->getMessage()
                ),
                0,
                $exception
            );
        } finally {
            restore_error_handler();
        }

        if ($memoryInformation === false) {
            throw new MemoryInfoReadException(
                sprintf('Unable to read memory information from "%s".', $this->memoryInformationFile)
            );
        }

        return $memoryInformation;
    }

    /**
     * @param array<string, int> $memoryInformationData
     */
    private function assertRequiredFieldsExist(array $memoryInformationData): void
    {
        foreach (self::REQUIRED_FIELDS as $requiredField) {
            if (!array_key_exists($requiredField, $memoryInformationData)) {
                throw new MemoryInfoReadException(
                    sprintf(
                        'Missing memory information field "%s" in "%s".',
                        $requiredField,
                        $this->memoryInformationFile
                    )
                );
            }
        }
    }
}
