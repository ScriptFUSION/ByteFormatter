<?php
namespace ScriptFUSION\Byte\Unit;

use ScriptFUSION\Byte\Base;

/**
 * Decorates byte values with unit names.
 */
class NameDecorator implements UnitDecorator
{
    protected static array $sequences = [
        Base::BINARY => ['kibi', 'mebi', 'gibi', 'tebi', 'pebi', 'exbi', 'zebi', 'yobi'],
        Base::DECIMAL => ['kilo', 'mega', 'giga', 'tera', 'peta', 'exa', 'zetta', 'yotta'],
    ];

    public function decorate(int $exponent, int $base, float $value): string
    {
        $suffix = $value === 1. ? 'byte' : 'bytes';

        if (!$exponent) {
            return $suffix;
        }

        return static::$sequences[$base][--$exponent] . $suffix;
    }
}
