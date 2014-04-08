<?php
namespace ScriptFUSION\Byte;

/**
 * Provides a collection of byte unit names.
 */
class ByteUnitNameCollection extends ByteUnitCollection implements BaseAware, ValueAware {
    private
        $base,
        $value
    ;

    protected static $sequences = [
        Base::BINARY => [ 'kibi', 'mebi', 'gibi', 'tebi', 'pebi', 'exbi', 'zebi', 'yobi' ],
        Base::DECIMAL => [ 'kilo', 'mega', 'giga', 'tera', 'peta', 'exa', 'zetta', 'yotta' ],
    ];

    public function __construct($base = Base::BINARY) {
        $this->setBase($base);
    }

    public function offsetGet($offset) {
        $suffix = $this->value == 1 ? 'byte' : 'bytes';

        if (!$offset) return $suffix;

        return static::$sequences[$this->base][--$offset] . $suffix;
    }

    public function setBase($base) {
        $this->base = +$base;

        return $this;
    }

    function setValue($value) {
        $this->value = +$value;

        return $this;
    }
}