# PHP Memory Info

PHP Memory Info is a small PHP library for reading the current memory status on Linux systems.

It reads `/proc/meminfo`, normalizes the kernel field names, and returns a typed `MemoryInfo` object with
integer values in bytes.

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

    echo $memoryInfo->getMemTotal();
    echo $memoryInfo->getMemAvailable();
} catch (MemoryInfoReadException $exception) {
    // Handle missing /proc/meminfo, unreadable data, or unsupported field sets.
}
```

`MemoryInfo` exposes typed getters for the supported `/proc/meminfo` fields, for example `getMemTotal()`,
`getMemAvailable()`, `getSwapTotal()`, and `getCommittedAS()`.

If you already have `/proc/meminfo` contents, parse them directly:

```php
$memoryInfo = (new MemoryInfoParser())->parse($contents);
```

## Behavior

- Values from `/proc/meminfo` are converted from `kB` to bytes.
- Additional kernel fields are accepted by the parser but ignored by `MemoryInfo`.
- Missing fields required by `MemoryInfo` raise `MemoryInfoReadException`.
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
