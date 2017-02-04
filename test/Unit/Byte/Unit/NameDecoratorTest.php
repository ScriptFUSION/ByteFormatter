<?php
namespace ScriptFUSIONTest\Unit\Byte\Unit;

use ScriptFUSION\Byte\Base;
use ScriptFUSION\Byte\Unit\NameDecorator;

final class NameDecoratorTest extends \PHPUnit_Framework_TestCase
{
    public function testBinarySequence()
    {
        $decorator = new NameDecorator;

        foreach (
            [
                'bytes',
                'kibibytes',
                'mebibytes',
                'gibibytes',
                'tebibytes',
                'pebibytes',
                'exbibytes',
                'zebibytes',
                'yobibytes',
            ] as $exponent => $name
        ) {
            self::assertSame($name, $decorator->decorate($exponent, Base::BINARY, 0));
        }
    }

    public function testDecimalSequence()
    {
        $decorator = new NameDecorator;

        foreach (
            [
                'bytes',
                'kilobytes',
                'megabytes',
                'gigabytes',
                'terabytes',
                'petabytes',
                'exabytes',
                'zettabytes',
                'yottabytes',
            ] as $exponent => $name
        ) {
            self::assertSame($name, $decorator->decorate($exponent, Base::DECIMAL, 0));
        }
    }

    public function testSingular()
    {
        $decorator = new NameDecorator;

        foreach (
            [
                'byte',
                'kibibyte',
                'mebibyte',
                'gibibyte',
                'tebibyte',
                'pebibyte',
                'exbibyte',
                'zebibyte',
                'yobibyte',
            ] as $exponent => $name
        ) {
            self::assertSame($name, $decorator->decorate($exponent, Base::BINARY, 1));
        }
    }
}
