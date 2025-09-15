<?php
namespace ScriptFUSIONTest\Integration\Byte;

use PHPUnit\Framework\TestCase;
use ScriptFUSION\Byte\Base;
use ScriptFUSION\Byte\ByteFormatter;
use ScriptFUSION\Byte\Unit\SymbolDecorator;
use ScriptFUSION\Byte\Unit\UnitDecorator;

final class ByteFormatterTest extends TestCase
{
    private ByteFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = (new ByteFormatter(new SymbolDecorator(SymbolDecorator::SUFFIX_NONE)))->setFormat('%v%u');
    }

    /** @dataProvider provideBinaryIntegers */
    public function testBinaryFormat($integer, $formatted): void
    {
        self::assertSame($formatted, $this->formatter->setBase(Base::BINARY)->format($integer));
    }

    public function provideBinaryIntegers(): array
    {
        return [
            [0, '0'],
            [1, '1'],
            [1023, '1023'],
            [1024, '1K'],
            [0x7FFFF, '512K'],
            [0x80000, '512K'],
            [0x80001, '512K'],
            [0x801FF, '512K'],
            // Rounding error fixed in 8.4.1.
            // See: https://github.com/php/php-src/blob/9ee607823eae02996f4d2f17d778041b76ec3e19/UPGRADING#L728-L734
            [0x80200, version_compare(PHP_VERSION, '8.4.0') > 0 ? '512K' : '513K'],
            [0x80201, '513K'],
            [0x80233, '513K'],
            [0x803FF, '513K'],
            [0x80400, '513K'],
            [0x100000, '1M'],
            [0x40000000, '1G'],
            //64-bit beyond this point.
            [0x10000000000, '1T'],
            [0x4000000000000, '1P'],
            [0x1000000000000000, '1E'],
        ];
    }

    /** @dataProvider provideDecimalIntegers */
    public function testDecimalFormat($integer, $formatted): void
    {
        self::assertSame($formatted, $this->formatter->setBase(Base::DECIMAL)->format($integer));
    }

    public function provideDecimalIntegers(): array
    {
        return [
            [0, '0'],
            [1, '1'],
            [999, '999'],
            [1000, '1K'],
            [499999, '500K'],
            [500000, '500K'],
            [500001, '500K'],
            [500999, '501K'],
            [501000, '501K'],
            [1000000, '1M'],
            [1000000000, '1G'],
            //64-bit beyond this point.
            [1000000000000, '1T'],
            [1000000000000000, '1P'],
            [1000000000000000000, '1E'],
            [1000000000000000000000, '1Z'],
            [1000000000000000000000000, '1Y'],
        ];
    }

    /** @dataProvider providePrecisionIntegers */
    public function testPrecision($integer, $formatted): void
    {
        self::assertSame($formatted, $this->formatter->setPrecision(2)->format($integer));
        self::assertSame($formatted, $this->formatter->setPrecision(5)->format($integer, 2));
    }

    public function providePrecisionIntegers(): array
    {
        return [
            [0, '0'],
            [1, '1'],
            [1023, '1023'],
            [1024, '1K'],
            [0x7FFFF, '512K'],
            [0x80000, '512K'],
            [0x80001, '512K'],
            [0x801FF, '512.5K'],
            [0x80200, '512.5K'],
            [0x80201, '512.5K'],
            [0x80233, '512.55K'],
            [0x803FF, '513K'],
            [0x80400, '513K'],
        ];
    }

    /** @dataProvider provideFormats */
    public function testFormats($format, $formatted): void
    {
        self::assertSame($formatted, $this->formatter->setFormat($format)->format($this->formatter->getBase()));
    }

    public function provideFormats(): array
    {
        return [
            ['%v%u', '1K'],
            ['%u%v', 'K1'],
            ['%v', '1'],
            ['%u', 'K'],
            ['%v%v', '11'],
            ['%u%u', 'KK'],
            ['%v%u %u%v', '1K K1'],
        ];
    }

    /** @dataProvider provideFixedExponents */
    public function testFixedExponent($exponent, $bytes, $formatted): void
    {
        $this->formatter->setPrecision(8);

        $this->formatter->setFixedExponent($exponent);
        self::assertSame($formatted, $this->formatter->format($bytes));
    }

    public function provideFixedExponents(): array
    {
        return [
            // TODO: Investigate rounding errors in following two cases.
            [0, 0x8000000000, '549755813888.00134277'],
            [1, 0x8000000000, '536870912.00000131K'],
            [2, 0x8000000000, '524288M'],
            [3, 0x8000000000, '512G'],
            [4, 0x8000000000, '0.5T'],
            [5, 0x8000000000, '0.00048828P'],
            [6, 0x8000000000, '0.00000048E'],

            [1, 0, '0K'],
            [1, Base::BINARY ** 0, '0.00097656K'],
            [1, Base::BINARY ** 1, '1K'],
            [1, Base::BINARY ** 2, '1024K'],
            [1, Base::BINARY ** 3, '1048576K'],
            [1, Base::BINARY ** 4, '1073741824K'],
            [1, Base::BINARY ** 5, '1099511627776K'],
            [1, Base::BINARY ** 6, '1125899906842624K'],
            [1, Base::BINARY ** 7, '1152921504606846976K'],
            [1, Base::BINARY ** 8, '1180591620717411303424K'],
            [1, Base::BINARY ** 9, '1208925819614629174706176K'],
            [1, Base::BINARY ** 10, '1237940039285380274899124224K'],
        ];
    }

    public function testDisableAutomaticPrecision(): void
    {
        $this->formatter->disableAutomaticPrecision();

        self::assertSame('512.50K', $this->formatter->format(0x80200, 2));
    }

    public function testCustomUnitSequence(): void
    {
        $formatter = (new ByteFormatter)->setUnitDecorator(
            \Mockery::mock(UnitDecorator::class)
                ->shouldReceive('decorate')
                ->andReturn('foo')
                ->getMock()
        );

        self::assertSame('1 foo', $formatter->format(1));
    }

    /** @dataProvider provideSignificantFigures */
    public function testSignificantFigures(int $significantFigures, int $in, string $out): void
    {
        $formatter = $this->formatter->setBase(Base::DECIMAL)->setSignificantFigures($significantFigures);

        self::assertSame($out, $formatter->format($in));
    }

    public static function provideSignificantFigures(): iterable
    {
        return [
            // Small numbers.
            [1, 1, '1'],
            [2, 1, '1'],
            [1, 12, '10'],
            [2, 12, '12'],
            [3, 12, '12'],
            [1, 123, '100'],
            [2, 123, '120'],
            [3, 123, '123'],

            // Thousands.
            [1, 1_234, '1K'],
            [2, 1_234, '1.2K'],
            [3, 1_234, '1.23K'],
            [4, 1_234, '1.234K'],

            [1, 12_345, '10K'],
            [2, 12_345, '12K'],
            [3, 12_345, '12.3K'],
            [4, 12_345, '12.35K'],
            [5, 12_345, '12.345K'],

            // Millions.
            [1, 1_234_567, '1M'],
            [2, 1_234_567, '1.2M'],
            [3, 1_234_567, '1.23M'],
            [4, 1_234_567, '1.235M'],
            [7, 1_234_567, '1.234567M'],

            // Billions.
            [1, 12_345_678_901, '10G'],
            [2, 12_345_678_901, '12G'],
            [3, 12_345_678_901, '12.3G'],
            [4, 12_345_678_901, '12.35G'],

            // Trillions.
            [1, 1_234_567_890_123, '1T'],
            [2, 1_234_567_890_123, '1.2T'],
            [3, 1_234_567_890_123, '1.23T'],
            [4, 1_234_567_890_123, '1.235T'],

            // Quadrillions.
            [1, 1_234_567_890_123_456, '1P'],
            [2, 1_234_567_890_123_456, '1.2P'],
            [3, 1_234_567_890_123_456, '1.23P'],
            [4, 1_234_567_890_123_456, '1.235P'],

            // PHP_INT_MAX boundary case.
            [1, 9_223_372_036_854_775_807, '9E'],
            [2, 9_223_372_036_854_775_807, '9.2E'],
            [3, 9_223_372_036_854_775_807, '9.22E'],
            [4, 9_223_372_036_854_775_807, '9.223E'],
        ];
    }

    public function testClearingSigFigFallsBackToPrecision(): void
    {
        $formatter = $this->formatter->setBase(Base::DECIMAL)->setSignificantFigures(3)->setPrecision(2);

        self::assertSame('908.61K', $formatter->format(908_614));
    }
}
