<?php
namespace ScriptFUSIONTest\Integration;

use ScriptFUSION\Byte\Base;
use ScriptFUSION\Byte\ByteFormatter;
use ScriptFUSION\Byte\Unit\SymbolDecorator;
use ScriptFUSION\Byte\Unit\UnitDecorator;

final class ByteFormatterTest extends \PHPUnit_Framework_TestCase
{
    /** @var ByteFormatter */
    private $formatter;

    protected function setUp()
    {
        $this->formatter = (new ByteFormatter(new SymbolDecorator(SymbolDecorator::SUFFIX_NONE)))->setFormat('%v%u');
    }

    /** @dataProvider provideBinaryIntegers */
    public function testBinaryFormat($integer, $formatted)
    {
        $this->assertSame($formatted, $this->formatter->setBase(Base::BINARY)->format($integer));
    }

    public function provideBinaryIntegers()
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
            [0x80200, '513K'],
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
    public function testDecimalFormat($integer, $formatted)
    {
        $this->assertSame($formatted, $this->formatter->setBase(Base::DECIMAL)->format($integer));
    }

    public function provideDecimalIntegers()
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
        ];
    }

    /** @dataProvider providePrecisionIntegers */
    public function testPrecision($integer, $formatted)
    {
        $this->assertSame($formatted, $this->formatter->setPrecision(2)->format($integer));
        $this->assertSame($formatted, $this->formatter->setPrecision(5)->format($integer, 2));
    }

    public function providePrecisionIntegers()
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
    public function testFormats($format, $formatted)
    {
        $this->assertSame($formatted, $this->formatter->setFormat($format)->format($this->formatter->getBase()));
    }

    public function provideFormats()
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

    public function testCustomUnitSequence()
    {
        $formatter = (new ByteFormatter)->setUnitDecorator(
            \Mockery::mock(UnitDecorator::class)
                ->shouldReceive('decorate')
                ->andReturn('foo')
                ->getMock()
        );

        $this->assertSame('1 foo', $formatter->format(1));
    }
}
