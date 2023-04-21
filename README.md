PHP Memory Info
=============================

A PHP library for reading the current memory status on Linux systems.

This package provides the ability to read RAM usage and other memory information from the file `/proc/meminfo`.

The file `/proc/meminfo` within the pseudo file system `/proc` provides a report on the memory usage on the system.


## Coding standard

To ensure that the code remains clean and consistent, [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) is used. The standard PSR12 is used

```shell
./vendor/bin/phpcs --standard="PSR12" /app/src
```
