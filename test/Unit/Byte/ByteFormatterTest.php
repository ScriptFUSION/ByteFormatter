<?php
namespace ScriptFUSIONTest\Unit\Byte;

use PHPUnit\Framework\TestCase;
use ScriptFUSION\Byte\ByteFormatter;

final class ByteFormatterTest extends TestCase
{
    /**
     * @var ByteFormatter
     */
    private $formatter;

    protected function setUp(): void
    {
        $this->formatter = new ByteFormatter;
    }

    public function testFormat(): void
    {
        self::assertSame($format = 'foo', $this->formatter->setFormat($format)->getFormat());
    }

    public function testAutomaticPrecision(): void
    {
        self::assertTrue($this->formatter->hasAutomaticPrecision());
        self::assertFalse($this->formatter->disableAutomaticPrecision()->hasAutomaticPrecision());
        self::assertTrue($this->formatter->enableAutomaticPrecision()->hasAutomaticPrecision());
    }

    public function testFixedExponent(): void
    {
        self::assertFalse($this->formatter->hasFixedExponent());
        self::assertSame($exponent = 2, $this->formatter->setFixedExponent($exponent)->getFixedExponent());
        self::assertTrue($this->formatter->hasFixedExponent());
        self::assertFalse($this->formatter->clearFixedExponent()->hasFixedExponent());
    }
}
