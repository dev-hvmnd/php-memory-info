# PHP Memory Info

PHP Memory Info is a small PHP library for reading the current memory status on Linux systems.

It reads `/proc/meminfo`, normalizes the kernel field names, and returns a typed `MemoryInfo` object. Values are
integers in bytes when the Linux kernel exposes the field; optional fields are `null` when absent.

## Requirements

- Linux with a readable `/proc/meminfo`
- PHP 8.2 or newer
- Composer

## Installation

```shell
composer require dev-hvmnd/memory-info
```

## Quick Start

```php
<?php

use DevHvmnd\MemoryInfo\MemoryDataReader;
use DevHvmnd\MemoryInfo\MemoryInfoParser;
use DevHvmnd\MemoryInfo\MemoryInfoReadException;

$reader = new MemoryDataReader(new MemoryInfoParser());

try {
    $memoryInfo = $reader->getMemoryInfo();

    echo $memoryInfo->memTotal;
    echo $memoryInfo->memAvailable;
} catch (MemoryInfoReadException $exception) {
    // Handle missing /proc/meminfo, unreadable data, or unsupported field sets.
}
```

`MemoryInfo` exposes public readonly properties for the supported `/proc/meminfo` fields, for example `memTotal`,
`memAvailable`, `swapTotal`, and `committedAS`.

If you already have `/proc/meminfo` contents, parse them directly:

```php
$memoryInfo = (new MemoryInfoParser())->parse($contents);
```

## Properties

The descriptions below are short summaries of the Linux `/proc/meminfo` fields. Kernel details can vary between Linux
versions. Optional properties are `null` when the current kernel does not expose the matching field.

| Property | `/proc/meminfo` key | Availability | Description |
| --- | --- | --- | --- |
| `memTotal` | `MemTotal` | Required | Total usable physical RAM. |
| `memFree` | `MemFree` | Required | Physical RAM that is currently unused. |
| `memAvailable` | `MemAvailable` | Optional | Estimate of memory available for new applications without swapping. |
| `buffers` | `Buffers` | Required | Memory used for temporary block device buffers. |
| `cached` | `Cached` | Required | Memory used for the page cache, excluding swap cache. |
| `swapCached` | `SwapCached` | Optional | Swap-backed memory that is also still cached in RAM. |
| `active` | `Active` | Optional | Recently used memory that is less likely to be reclaimed. |
| `inactive` | `Inactive` | Optional | Less recently used memory that is more likely to be reclaimed. |
| `activeAnon` | `Active(anon)` | Optional | Active anonymous memory, not backed by files. |
| `inactiveAnon` | `Inactive(anon)` | Optional | Inactive anonymous memory, not backed by files. |
| `activeFile` | `Active(file)` | Optional | Active file-backed memory. |
| `inactiveFile` | `Inactive(file)` | Optional | Inactive file-backed memory. |
| `unevictable` | `Unevictable` | Optional | Memory that cannot be reclaimed by the kernel. |
| `mlocked` | `Mlocked` | Optional | Memory locked into RAM with mlock. |
| `swapTotal` | `SwapTotal` | Required | Total configured swap space. |
| `swapFree` | `SwapFree` | Required | Swap space that is currently unused. |
| `dirty` | `Dirty` | Optional | Memory waiting to be written back to disk. |
| `writeback` | `Writeback` | Optional | Memory currently being written back to disk. |
| `anonPages` | `AnonPages` | Optional | Non-file-backed pages mapped into user space. |
| `mapped` | `Mapped` | Optional | Files mapped into memory by processes. |
| `shmem` | `Shmem` | Optional | Shared memory, including tmpfs memory. |
| `kReclaimable` | `KReclaimable` | Optional | Kernel allocations that the kernel can reclaim. |
| `slab` | `Slab` | Optional | Kernel memory used for internal data structure caches. |
| `sReclaimable` | `SReclaimable` | Optional | Reclaimable part of slab memory. |
| `sUnreclaim` | `SUnreclaim` | Optional | Non-reclaimable part of slab memory. |
| `kernelStack` | `KernelStack` | Optional | Memory used for kernel stacks. |
| `pageTables` | `PageTables` | Optional | Memory used for page tables. |
| `nfsUnstable` | `NFS_Unstable` | Optional | NFS pages sent to the server but not yet committed. |
| `bounce` | `Bounce` | Optional | Memory used for block device bounce buffers. |
| `writebackTmp` | `WritebackTmp` | Optional | Temporary memory used for FUSE writeback buffers. |
| `commitLimit` | `CommitLimit` | Optional | Total memory that can be committed under the current overcommit policy. |
| `committedAS` | `Committed_AS` | Optional | Total memory currently committed to processes. |
| `vmallocTotal` | `VmallocTotal` | Optional | Total virtual address space available for vmalloc. |
| `vmallocUsed` | `VmallocUsed` | Optional | Virtual address space currently used by vmalloc. |
| `vmallocChunk` | `VmallocChunk` | Optional | Largest contiguous vmalloc area, often reported as `0` on newer kernels. |

## Behavior

- Values from `/proc/meminfo` are converted from `kB` to bytes.
- Additional kernel fields are accepted by the parser but ignored by `MemoryInfo`.
- Missing required fields raise `MemoryInfoReadException`.
- Missing optional fields are exposed as `null`.
- Invalid non-empty lines or values without numbers raise `InvalidArgumentException` from the parser.
- For tests or fixtures, pass a custom file path as the second `MemoryDataReader` constructor argument.

## Development

Install dependencies:

```shell
composer install
```

Run the full check suite:

```shell
composer check
```

Run checks individually:

```shell
composer test
composer cs
```

If PHP is not installed locally, use the official PHP Docker image:

```shell
docker run --rm -v "$PWD":/app -w /app php:8.2-cli sh -lc '
  export DEBIAN_FRONTEND=noninteractive &&
  apt-get update &&
  apt-get install -y unzip &&
  php -r "copy(\"https://getcomposer.org/installer\", \"/tmp/composer-setup.php\");" &&
  php /tmp/composer-setup.php --install-dir=/tmp --filename=composer &&
  /tmp/composer install &&
  /tmp/composer validate --strict &&
  /tmp/composer check
'
```

The repository keeps `composer.lock` committed so development tooling is reproducible.

## License

MIT
