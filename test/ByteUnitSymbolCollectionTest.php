<?php
use ScriptFUSION\Byte\ByteUnitSymbolCollection;

class ByteUnitSymbolSequenceTest extends PHPUnit_Framework_TestCase {
    public function testDefaultSequence() {
        $collection = new ByteUnitSymbolCollection;

        $this->assertSame(
            ['', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y'],
            iterator_to_array($collection)
        );
    }

    public function testAlwaysShowUnit() {
        $collection = (new ByteUnitSymbolCollection)->alwaysShowUnit();

        $this->assertSame(
            ['B', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y'],
            iterator_to_array($collection)
        );
    }

    public function testMetricSuffix() {
        $collection = new ByteUnitSymbolCollection(ByteUnitSymbolCollection::SUFFIX_METRIC);

        $this->assertSame(
            ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
            iterator_to_array($collection)
        );
    }

    public function testIecSuffix() {
        $collection = new ByteUnitSymbolCollection(ByteUnitSymbolCollection::SUFFIX_IEC);

        $this->assertSame(
            ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'],
            iterator_to_array($collection)
        );
    }
}
