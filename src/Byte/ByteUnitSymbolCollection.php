<?php
namespace ScriptFUSION\Byte;

/**
 * Provides a collection of byte unit symbols.
 */
class ByteUnitSymbolCollection extends ByteUnitCollection implements BaseAware {
    const
        PREFIXES = 'KMGTPEZY',

        SUFFIX_NONE   = '',
        SUFFIX_METRIC = 'B',
        SUFFIX_IEC    = 'iB'
    ;

    protected
        $suffix,
        $alwaysShowUnit
    ;

    public function __construct($suffix = self::SUFFIX_NONE, $alwaysShowUnit = false) {
        $this->setSuffix($suffix)->alwaysShowUnit($alwaysShowUnit);
    }

    public function offsetGet($offset) {
        if (!$offset) return $this->suffix !== static::SUFFIX_NONE || $this->alwaysShowUnit ? 'B' : '';

        return substr(static::PREFIXES, min(--$offset, count($this) - 1), 1) . $this->suffix;
    }

    public function setBase($base) {
        switch ($base) {
            case Base::BINARY:
                $this->setSuffix(static::SUFFIX_IEC);
                break;

            case Base::DECIMAL:
                $this->setSuffix(static::SUFFIX_METRIC);
                break;
        }
    }

    public function getSuffix() {
        return $this->suffix;
    }
    public function setSuffix($suffix) {
        $this->suffix = "$suffix";

        return $this;
    }

    public function alwaysShowUnit($show = true) {
        $this->alwaysShowUnit = !!$show;

        return $this;
    }
}