<?php
namespace ScriptFUSIONTest\Unit\Byte\Unit;

use PHPUnit\Framework\TestCase;
use ScriptFUSION\Byte\Base;
use ScriptFUSION\Byte\Unit\SymbolDecorator;

final class SymbolDecoratorTest extends TestCase
{
    public function testNoSuffix(): void
    {
        $decorator = new SymbolDecorator(SymbolDecorator::SUFFIX_NONE);

        foreach (['', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y'] as $exponent => $symbol) {
            self::assertSame($symbol, $decorator->decorate($exponent, 0, 0));
        }
    }

    public function testAlwaysShowUnit(): void
    {
        $decorator = (new SymbolDecorator(SymbolDecorator::SUFFIX_NONE))->alwaysShowUnit();

        foreach (['B', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y'] as $exponent => $symbol) {
            self::assertSame($symbol, $decorator->decorate($exponent, 0, 0));
        }
    }

    public function testMetricSuffix(): void
    {
        $decorator = new SymbolDecorator(SymbolDecorator::SUFFIX_METRIC);

        foreach (['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'] as $exponent => $symbol) {
            self::assertSame($symbol, $decorator->decorate($exponent, 0, 0));
        }
    }

    public function testIecSuffix(): void
    {
        $decorator = new SymbolDecorator(SymbolDecorator::SUFFIX_IEC);

        foreach (['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'] as $exponent => $symbol) {
            self::assertSame($symbol, $decorator->decorate($exponent, 0, 0));
        }
    }

    public function testAutomaticUnitSwitching(): void
    {
        $decorator = new SymbolDecorator;

        self::assertSame('KiB', $decorator->decorate(1, Base::BINARY, 0));
        self::assertSame('KB', $decorator->decorate(1, Base::DECIMAL, 0));
    }

    public function testSuffix(): void
    {
        self::assertSame($suffix = 'foo', (new SymbolDecorator)->setSuffix($suffix)->getSuffix());
    }

    public function testCustomPrefixes(): void
    {
        $decorator = (new SymbolDecorator())->setPrefixes('XYZ');

        foreach (['B', 'X', 'Y', 'Z', 'Z', 'Z'] as $exponent => $symbol) {
            self::assertSame($symbol, $decorator->decorate($exponent, 0, 0));
        }
    }
}
