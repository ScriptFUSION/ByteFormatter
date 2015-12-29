<?php
namespace ScriptFUSION\Byte\Unit;

/**
 * Decorates byte values with units.
 */
interface UnitDecorator
{
    /**
     * Decorates the specified scaled byte value of the specified exponent and
     * base with units.
     *
     * @param int $exponent Exponent.
     * @param int $base Base.
     * @param float $value Scaled byte value.
     *
     * @return string Units.
     */
    public function decorate($exponent, $base, $value);
}
