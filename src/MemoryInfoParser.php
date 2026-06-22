<?php

declare(strict_types=1);

namespace DevHvmnd\MemoryInfo;

use InvalidArgumentException;

class MemoryInfoParser
{
    /**
     * @return array<string, int>
     */
    public function parse(string $memoryInformation): array
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
