ByteFormatter
=============

[![Version][Version image]][Releases]
[![Build status][Build image]][Build]

ByteFormatter is a [PSR-2](http://www.php-fig.org/psr/psr-2/) compliant PHP library that formats byte values as
human-readable strings. An appropriate exponent is calculated automatically such that the value never exceeds the base.
For example, in base 1024, `format(1023)` gives *1023 B* but `format(1024)` gives *1 KiB* instead of *1024 B*.

Requirements
------------

- PHP 5.5 and Composer.
- Nothing else and no production dependencies!

Usage
-----

By default bytes are divided using `Base::BINARY` into multiples of 1024.

```php
(new ByteFormatter)->format(0x80000);
```
> 512 KiB

Bytes can be divided into multiples of 1000 by specifying `Base::DECIMAL` as the base.

```php
(new ByteFormatter)->setBase(Base::DECIMAL)->format(500000);
```
> 500 KB

Precision
---------

By default all values are rounded to the nearest integer.

```php
(new ByteFormatter)->format(0x80233);
```
> 513 KiB

Increasing the default precision with `setPrecision()` allows the specified number of digits after the decimal point.

```php
(new ByteFormatter)->setPrecision(2)->format(0x80233);
```
> 512.55 KiB

Increasing the precision will increase the maximum digits allowed but the formatter will only display as many as
needed.

```php
(new ByteFormatter)->setPrecision(2)->format(0x80200);
```
> 512.5 KiB

The default precision can be overridden by passing the second argument to `format()`.

```php
(new ByteFormatter)->setPrecision(2)->format(0x80233, 4);
```
> 512.5498 KiB

Output format
-------------

The format can be changed by calling the `setFormat($format)` function which takes a string format parameter.
The default format is `'%v %u'`. Occurrences of `%v` and `%u` in the format string will be replaced with the calculated
*value* and *units* respectively.

```php
(new ByteFormatter)->setFormat('%v%u')->format(0x80000);
```
> 512KiB

Unit customization
------------------

Units are provided by decorators extending `UnitDecorator`. Two implementations are provided: the default
`SymbolDecorator` and an optional `NameDecorator`.

Unit decorators receive the base of the formatter when asked to decorate a value so that different units can be
returned for different bases. For example, the default decorator outputs `KiB` in base 1024 for
*2<sup>10</sup> < bytes < 2<sup>20</sup>* but outputs `KB` in base 1000 for *1000 < bytes < 1000000*. This behaviour
can be suppressed by calling`SymbolDecorator::setSuffix()` with the desired `SymbolDecorator` suffix constant to
prevent units changing when the base is changed. Decorators also receive the exponent and scaled byte value.

### Symbol decorator

`SymbolDecorator` is the default unit decorator and returns units like *B*, *KB*, *MB*, etc. The symbol's suffix can be
changed using one of the class constants from the following table.

| Constant      | B |  K  |  M  |  G  |  T  |  P  |  E  |  Z  |  Y  |
|---------------|:-:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| SUFFIX_NONE   |   |  K  |  M  |  G  |  T  |  P  |  E  |  Z  |  Y  |
| SUFFIX_METRIC | B |  KB |  MB |  GB |  TB |  PB |  EB |  ZB |  YB |
| SUFFIX_IEC    | B | KiB | MiB | GiB | TiB | PiB | EiB | ZiB | YiB |

The following example uses base 1024 but displays the metric suffix, like Windows Explorer.

```php
(new ByteFormatter(new SymbolDecorator(SymbolDecorator::SUFFIX_METRIC)))
    ->format(0x80000)
```
> 512 KB

If you prefer terse notation the suffix may be removed with `SUFFIX_NONE`.

```php
(new ByteFormatter(new SymbolDecorator(SymbolDecorator::SUFFIX_NONE)))
    ->format(0x80000)
```
> 512 K

Note that no unit is displayed for bytes when the suffix is disabled. If this is undesired, byte units can be forced
with `SymbolDecorator::alwaysShowUnit()`.

```php
(new ByteFormatter(new SymbolDecorator(SymbolDecorator::SUFFIX_NONE)))
    ->format(512)
```
> 512

```php
(new ByteFormatter(
    (new SymbolDecorator(SymbolDecorator::SUFFIX_NONE))
        ->alwaysShowUnit()
))
    ->format(512)
```
> 512 B

### Name decorator

`NameDecorator` can be used to replace the default decorator and returns units like *byte*, *kilobyte*, *megabyte*,
etc.

```php
(new ByteFormatter(new NameDecorator))
    ->format(0x80000)
```
> 512 kibibytes

Using decimal base:

```php
(new ByteFormatter(new NameDecorator))
    ->setBase(Base::DECIMAL)
    ->format(500000)
```
> 500 kilobytes

Testing
-------

This library is fully unit tested. Run the tests with `vendor/bin/phpunit test` from the command line. All examples
in this document can be found in `DocumentationTest`.

  [Releases]: https://github.com/ScriptFUSION/ByteFormatter/releases
  [Version image]: http://img.shields.io/github/tag/ScriptFUSION/ByteFormatter.svg "Latest version"
  [Build]: http://travis-ci.org/ScriptFUSION/ByteFormatter
  [Build image]: http://img.shields.io/travis/ScriptFUSION/ByteFormatter.svg "Build status"
