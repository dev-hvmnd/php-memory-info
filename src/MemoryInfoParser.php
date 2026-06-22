<?php

declare(strict_types=1);

namespace DevHvmnd\MemoryInfo;

use InvalidArgumentException;

class MemoryInfoParser
{
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

    public function parse(string $memoryInformation): MemoryInfo
    {
        $memoryInformationData = $this->parseMemoryInformationData($memoryInformation);

        $this->assertRequiredFieldsExist($memoryInformationData);

        return $this->createMemoryInfo($memoryInformationData);
    }

    /**
     * @return array<string, int>
     */
    private function parseMemoryInformationData(string $memoryInformation): array
    {
        $memoryInformationData = [];

        foreach (explode("\n", $memoryInformation) as $lineNumber => $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            if (!str_contains($line, ':')) {
                throw new InvalidArgumentException(
                    sprintf('Invalid memory information line %d: "%s".', $lineNumber + 1, $line)
                );
            }

            [$key, $value] = explode(":", $line, 2);

            $key = $this->canonicalizeValueKey($key);
            $value = $this->parseValueInBytes($value, $lineNumber + 1);

            $memoryInformationData[$key] = $value;
        }

        return $memoryInformationData;
    }

    /**
     * @param array<string, int> $memoryInformationData
     */
    private function createMemoryInfo(array $memoryInformationData): MemoryInfo
    {
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

    /**
     * @param array<string, int> $memoryInformationData
     */
    private function assertRequiredFieldsExist(array $memoryInformationData): void
    {
        foreach (self::REQUIRED_FIELDS as $requiredField) {
            if (!array_key_exists($requiredField, $memoryInformationData)) {
                throw new MemoryInfoReadException(
                    sprintf('Missing memory information field "%s".', $requiredField)
                );
            }
        }
    }

    private function canonicalizeValueKey(string $key): string
    {
        $keyParts = preg_split(
            pattern: '/\s+/',
            subject: str_replace(['_', '(', ')'], ' ', trim($key)),
            flags: PREG_SPLIT_NO_EMPTY
        );

        if ($keyParts === false || $keyParts === []) {
            throw new InvalidArgumentException(sprintf('Invalid memory information key "%s".', $key));
        }

        $normalizedKeyParts = [];

        foreach ($keyParts as $index => $keyPart) {
            $normalizedKeyParts[] = $index === 0
                ? $this->normalizeFirstKeyPart($keyPart)
                : $this->normalizeFollowingKeyPart($keyPart);
        }

        return implode('', $normalizedKeyParts);
    }

    private function normalizeFirstKeyPart(string $keyPart): string
    {
        if (strtoupper($keyPart) === $keyPart) {
            return strtolower($keyPart);
        }

        return lcfirst($keyPart);
    }

    private function normalizeFollowingKeyPart(string $keyPart): string
    {
        if ($keyPart === 'AS') {
            return $keyPart;
        }

        if (strtoupper($keyPart) === $keyPart) {
            return ucfirst(strtolower($keyPart));
        }

        return ucfirst($keyPart);
    }

    private function parseValueInBytes(string $value, int $lineNumber): int
    {
        if (!preg_match('/^\s*(\d+)\s*(?:kB)?\s*$/i', $value, $matches)) {
            throw new InvalidArgumentException(
                sprintf('Invalid memory information value on line %d: "%s".', $lineNumber, trim($value))
            );
        }

        return (int)$matches[1] * 1024;
    }
}
