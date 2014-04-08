<?php
use ScriptFUSION\Byte\Base;
use ScriptFUSION\Byte\ByteFormatter;
use ScriptFUSION\Byte\ByteUnitNameCollection;

class ByteFormatterTest extends PHPUnit_Framework_TestCase {
    private function createFormatter() {
        return (new ByteFormatter)->disableAutomaticUnitSwitching()->setUnitSymbolSuffix()->setFormat('%v%u');
    }

    public function test64BitEnabled() {
        $this->assertGreaterThanOrEqual(8, PHP_INT_SIZE);
    }

    /** @dataProvider provideBinaryIntegers */
    public function testBinaryFormat($integer, $formatted) {
        $formatter = $this->createFormatter()->setBase(Base::BINARY);

        $this->assertSame($formatted, $formatter->format($integer));
    }

    public function provideBinaryIntegers() {
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
    public function testDecimalFormat($integer, $formatted) {
        $formatter = $this->createFormatter()->setBase(Base::DECIMAL);

        $this->assertSame($formatted, $formatter->format($integer));
    }

    public function provideDecimalIntegers() {
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
    public function testPrecision($integer, $formatted) {
        $formatter = $this->createFormatter()->setPrecision(2);

        $this->assertSame($formatted, $formatter->format($integer));
    }

    public function providePrecisionIntegers() {
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
    public function testFormats($format, $formatted) {
        $formatter = $this->createFormatter()->setFormat($format);

        $this->assertSame($formatted, $formatter->format($formatter->getBase()));
    }

    public function provideFormats() {
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

    public function testAutomaticUnitSwitching() {
        $formatter = $this->createFormatter()->disableAutomaticUnitSwitching(false);

        $formatter->setBase(Base::BINARY);
        $this->assertSame('1KiB', $formatter->format($formatter->getBase()));

        $formatter->setBase(Base::DECIMAL);
        $this->assertSame('1KB', $formatter->format($formatter->getBase()));
    }

    public function testCustomUnitSequence() {
        //ByteUnitNameSequence should be mocked for unit testing or promoted to integration test.
        $formatter = (new ByteFormatter)->setUnitCollection(new ByteUnitNameCollection)->setBase(Base::BINARY);

        $this->assertSame('1 byte', $formatter->format(1));
        $this->assertSame('1 kibibyte', $formatter->format($formatter->getBase()));
    }
}