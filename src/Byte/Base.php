<?php
namespace ScriptFUSION\Byte;

/**
 * Specifies an exponentiation base.
 */
final class Base
{
    const
        DECIMAL = 1000,
        BINARY  = 1024
    ;

    /**
     * Prevents instantiation of this class.
     */
    private function __construct()
    {
        // Intentionally empty.
    }
}
