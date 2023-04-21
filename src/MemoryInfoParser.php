<?php

declare(strict_types=1);

namespace DevHvmnd\MemoryInfo;

class MemoryInfoParser
{
    public function parse(string $memoryInformation): array
    {
        $memoryInformationData = [];

        foreach (explode("\n", $memoryInformation) as $line) {
            [$key, $value] = explode(":", $line);

            $key = $this->canonicalizeValueKey($key);
            $value = $this->parseValueInBytes($value);

            $memoryInformationData[$key] = $value;
        }

        return $memoryInformationData;
    }

    private function canonicalizeValueKey(string $key) : string
    {
        $key = trim($key);
        return lcfirst(
            str_replace(
                ' ',
                '',
                ucwords(
                    str_replace('_', ' ', $key)
                )
            )
        );
    }

    private function parseValueInBytes(string $value): int
    {
        return (int)trim(string: preg_replace(pattern: '/^\D/', replacement: '', subject: $value)) * 1024;
    }
}
