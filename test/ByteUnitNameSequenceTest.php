<?php
use ScriptFUSION\Byte\Base;
use ScriptFUSION\Byte\ByteUnitNameSequence;

class ByteUnitNameSequenceTest extends PHPUnit_Framework_TestCase {
    public function testDecimalSequence() {
        $sequence = new ByteUnitNameSequence(Base::DECIMAL);

        $this->assertInstanceOf('Generator', $sequence->getSequence());
        $this->assertCount(9, $sequence->getSequenceArray());
        $this->assertSame('bytes', $sequence->getSequenceIndex(0));
        $this->assertSame('yottabytes', $sequence->getSequenceIndex(8));
    }

    public function testBinarySequence() {
        $sequence = new ByteUnitNameSequence(Base::BINARY);

        $this->assertInstanceOf('Generator', $sequence->getSequence());
        $this->assertCount(9, $sequence->getSequenceArray());
        $this->assertSame('bytes', $sequence->getSequenceIndex(0));
        $this->assertSame('yobibytes', $sequence->getSequenceIndex(8));
    }
}
