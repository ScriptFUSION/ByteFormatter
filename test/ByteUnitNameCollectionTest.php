<?php
use ScriptFUSION\Byte\Base;
use ScriptFUSION\Byte\ByteUnitNameCollection;

class ByteUnitNameSequenceTest extends PHPUnit_Framework_TestCase {
    public function testBinarySequence() {
        $collection = new ByteUnitNameCollection(Base::BINARY);

        $this->assertSame(
            [
                'bytes',
                'kibibytes',
                'mebibytes',
                'gibibytes',
                'tebibytes',
                'pebibytes',
                'exbibytes',
                'zebibytes',
                'yobibytes'
            ],
            iterator_to_array($collection)
        );
    }

    public function testDecimalSequence() {
        $collection = new ByteUnitNameCollection(Base::DECIMAL);

        $this->assertSame(
            [
                'bytes',
                'kilobytes',
                'megabytes',
                'gigabytes',
                'terabytes',
                'petabytes',
                'exabytes',
                'zettabytes',
                'yottabytes'
            ],
            iterator_to_array($collection)
        );
    }

    public function testSingular() {
        $collection = (new ByteUnitNameCollection)->setValue(1);

        $this->assertSame(
            [
                'byte',
                'kibibyte',
                'mebibyte',
                'gibibyte',
                'tebibyte',
                'pebibyte',
                'exbibyte',
                'zebibyte',
                'yobibyte'
            ],
            iterator_to_array($collection)
        );
    }
}