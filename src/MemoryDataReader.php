<?php

declare(strict_types=1);

namespace DevHvmnd\MemoryInfo;

readonly class MemoryDataReader
{
    public const PROC_MEMORY_INFO_FILE = '/proc/meminfo';

    private MemoryInfoParser $memoryInfoParser;

    public function __construct(
        ?MemoryInfoParser $memoryInfoParser = null,
        private string $memoryInformationFile = self::PROC_MEMORY_INFO_FILE
    ) {
        $this->memoryInfoParser = $memoryInfoParser ?? new MemoryInfoParser();
    }

    public function getMemoryInfo(): MemoryInfo
    {
        $memoryInformation = $this->readMemoryInformation();

        return $this->memoryInfoParser->parse($memoryInformation);
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
        } catch (MemoryInfoReadException $memoryInfoReadException) {
            throw new MemoryInfoReadException(
                sprintf(
                    'Unable to read memory information from "%s": %s',
                    $this->memoryInformationFile,
                    $memoryInfoReadException->getMessage()
                ),
                0,
                $memoryInfoReadException
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
}
