ByteFormatter
=============

ByteFormatter is a PHP library that formats byte values as human-readable strings. An appropriate scale is calculated automatically such that the value never exceeds the base. For example, in base 1024, `format(1023)` gives *1023 B* but `format(1024)` gives *1 KiB* instead of *1024 B*.

Requirements
------------
- PHP 5.5 for generator support.
- 64-bit support is required if you want to work with terabytes or run the unit tests.

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

Increasing the precision with `setPrecision($precision)` allows the specified amount of digits after the decimal point.
```php
(new ByteFormatter)->setPrecision(2)->format(0x80233);
```
> 512.55 KiB

Increasing the precision will increase the maximum digits allowed but the formatter will only display as many as needed.

```php
(new ByteFormatter)->setPrecision(2)->format(0x80200);
```
> 512.5 KiB

Output format
-------------
The format can be changed by calling the `setFormat($format)` function which takes a string format parameter. The default format is `'%v %u'`. Occurrences of `%v` and `%u` in the format string will be replaced with the calculated *value* and *units* respectively.

```php
(new ByteFormatter)->setFormat('%v%u')->format(0x80000);
```
> 512KiB

Customizing units
-----------------
Units are provided by collections that extend `ByteUnitCollection`. The current unit collection instance can be retrieved from `ByteFormatter::getUnitCollection()`.

Unit collections are notified of base changes in the formatter so that different units can be returned for different bases. For example, the default collection outputs `KiB` in base 1024 for *2<sup>10</sup> < bytes < 2<sup>20</sup>* but outputs `KB` in base 1000 for *1000 < bytes < 1000000*. This behaviour can be suppressed by calling `disableAutomaticUnitSwitching(true)` to prevent units changing when the base is changed.

### Symbol collection

`ByteUnitSymbolCollection` is the default unit collection and returns units like *B*, *KB*, *MB*, etc. The symbol's suffix can be changed using one of the class constants from the following table.

| Constant      | B |  K  |  M  |  G  |  T  |  P  |  E  |  Z  |  Y  |
|---------------|:-:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| SUFFIX_NONE   |   |  K  |  M  |  G  |  T  |  P  |  E  |  Z  |  Y  |
| SUFFIX_METRIC | B |  KB |  MB |  GB |  TB |  PB |  EB |  ZB |  YB |
| SUFFIX_IEC    | B | KiB | MiB | GiB | TiB | PiB | EiB | ZiB | YiB |

The following example uses base 1024 but displays the metric suffix, like Windows Explorer.

```php
(new ByteFormatter)
    ->setBase(Base::BINARY)
    ->setUnitSymbolSuffix(ByteUnitSymbolCollection::SUFFIX_METRIC)
    ->format(0x80000);
```
> 512 KB

If you prefer terse notation the suffix may be removed with `SUFFIX_NONE`.

```php
(new ByteFormatter)
    ->setUnitSymbolSuffix(ByteUnitSymbolCollection::SUFFIX_NONE)
    ->format(0x80000);
```
> 512 K

Note that no unit is displayed for bytes when the suffix is disabled. If this is undesired, byte units can be forced with `ByteUnitSymbolCollection::alwaysShowUnit()`.

```php
$formatter = (new ByteFormatter)
    ->setUnitSymbolSuffix(ByteUnitSymbolCollection::SUFFIX_NONE)
    ->format(512);
```
> 512

```php
$formatter->getUnitCollection()->alwaysShowUnit();
$formatter->format(512);
```
> 512 B

### Name collection
`ByteUnitNameCollection` can be used to replace the default collection and returns units like *byte*, *kilobyte*, *megabyte*, etc.

```php
(new ByteFormatter)
    ->setUnitCollection(new ByteUnitNameCollection)
    ->format(0x80000);
```
> 512 kibibytes

Using decimal base:

```php
(new ByteFormatter)
    ->setUnitCollection(new ByteUnitNameCollection)
    ->setBase(Base::DECIMAL)
    ->format(500000);
```
> 500 kilobytes

Testing
-------
This library is fully unit tested. Run the tests with `vendor/bin/phpunit test/` from the command line.
