<?php
namespace ScriptFUSIONTest\Unit\Byte;

use ScriptFUSION\Byte\ByteFormatter;

final class ByteFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ByteFormatter
     */
    private $formatter;

    protected function setUp()
    {
        $this->formatter = new ByteFormatter;
    }

    public function testFormat()
    {
        self::assertSame($format = 'foo', $this->formatter->setFormat($format)->getFormat());
    }

    public function testAutomaticPrecision()
    {
        self::assertTrue($this->formatter->hasAutomaticPrecision());
        self::assertFalse($this->formatter->disableAutomaticPrecision()->hasAutomaticPrecision());
        self::assertTrue($this->formatter->enableAutomaticPrecision()->hasAutomaticPrecision());
    }

    public function testFixedExponent()
    {
        self::assertFalse($this->formatter->hasFixedExponent());
        self::assertSame($exponent = 2, $this->formatter->setFixedExponent($exponent)->getFixedExponent());
        self::assertTrue($this->formatter->hasFixedExponent());
        self::assertFalse($this->formatter->clearFixedExponent()->hasFixedExponent());
    }
}
