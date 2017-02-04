<?php
namespace ScriptFUSIONTest\Integration;

use ScriptFUSION\Byte\Base;
use ScriptFUSION\Byte\ByteFormatter;
use ScriptFUSION\Byte\Unit\NameDecorator;
use ScriptFUSION\Byte\Unit\SymbolDecorator;

final class DocumentationTest extends \PHPUnit_Framework_TestCase
{
    public function testBasicUsage()
    {
        self::assertSame('512 KiB', (new ByteFormatter)->format(0x80000));

        self::assertSame('500 KB', (new ByteFormatter)->setBase(Base::DECIMAL)->format(500000));
    }

    public function testPrecision()
    {
        self::assertSame('513 KiB', (new ByteFormatter)->format(0x80233));

        self::assertSame('512.55 KiB', (new ByteFormatter)->setPrecision(2)->format(0x80233));

        self::assertSame('512.5 KiB', (new ByteFormatter)->setPrecision(2)->format(0x80200));

        self::assertSame(
            '512.50 KiB',
            (new ByteFormatter)->setPrecision(2)->disableAutomaticPrecision()->format(0x80200)
        );

        self::assertSame('512.5498 KiB', (new ByteFormatter)->setPrecision(2)->format(0x80233, 4));
    }

    public function testOutputFormat()
    {
        self::assertSame('512KiB', (new ByteFormatter)->setFormat('%v%u')->format(0x80000));
    }

    public function testFixedExponent()
    {
        self::assertSame('1024 KiB', (new ByteFormatter)->setFixedExponent(1)->format(1024 * 1024));
    }

    public function testSymbolDecorator()
    {
        self::assertSame(
            '512 KB',
            (new ByteFormatter(new SymbolDecorator(SymbolDecorator::SUFFIX_METRIC)))
                ->format(0x80000)
        );

        self::assertSame(
            '512 K',
            (new ByteFormatter(new SymbolDecorator(SymbolDecorator::SUFFIX_NONE)))
                ->format(0x80000)
        );

        self::assertSame(
            '512',
            (new ByteFormatter(new SymbolDecorator(SymbolDecorator::SUFFIX_NONE)))
                ->format(512)
        );

        self::assertSame(
            '512 B',
            (new ByteFormatter(
                (new SymbolDecorator(SymbolDecorator::SUFFIX_NONE))
                    ->alwaysShowUnit()
            ))
                ->format(512)
        );
    }

    public function testNameDecorator()
    {
        self::assertSame(
            '512 kibibytes',
            (new ByteFormatter(new NameDecorator))
                ->format(0x80000)
        );

        self::assertSame(
            '500 kilobytes',
            (new ByteFormatter(new NameDecorator))
                ->setBase(Base::DECIMAL)
                ->format(500000)
        );
    }
}
