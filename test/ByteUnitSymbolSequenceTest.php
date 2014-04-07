<?php
use ScriptFUSION\Byte\ByteUnitSymbolSequence;

class ByteUnitSymbolSequenceTest extends PHPUnit_Framework_TestCase {
    public function testDefaultSequence() {
        $seq = new ByteUnitSymbolSequence;

        $this->assertSame(['', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y'], $seq->getSequenceArray());
    }

    public function testAlwaysShowUnit() {
        $seq = (new ByteUnitSymbolSequence)->alwaysShowUnit();

        $this->assertSame(['B', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y'], $seq->getSequenceArray());
    }

    public function testMetricSuffix() {
        $seq = new ByteUnitSymbolSequence(ByteUnitSymbolSequence::SUFFIX_METRIC);

        $this->assertSame(['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'], $seq->getSequenceArray());
    }

    public function testIecSuffix() {
        $seq = new ByteUnitSymbolSequence(ByteUnitSymbolSequence::SUFFIX_IEC);

        $this->assertSame(['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'], $seq->getSequenceArray());
    }
}
