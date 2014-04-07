<?php
namespace ScriptFUSION\Byte;

use ScriptFUSION\Sequence\ArraySequence;

/**
 * Provides a sequence of byte unit names from least to most significant.
 */
class ByteUnitNameSequence extends ArraySequence implements BaseAware {
    protected static $sequences = [
        Base::BINARY => [
            'bytes',
            'kibibytes',
            'mebibytes',
            'gibibytes',
            'tebibytes',
            'pebibytes',
            'exbibytes',
            'zebibytes',
            'yobibytes',
        ],
        Base::DECIMAL => [
            'bytes',
            'kilobytes',
            'megabytes',
            'gigabytes',
            'terabytes',
            'petabytes',
            'exabytes',
            'zettabytes',
            'yottabytes',
        ],
    ];

    public function __construct($base = Base::BINARY) {
        $this->setBase($base);
    }

    public function setBase($base) {
        isset(static::$sequences[$base]) && $this->sequence = static::$sequences[$base];
    }
}