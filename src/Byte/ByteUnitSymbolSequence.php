<?php
namespace ScriptFUSION\Byte;

use ScriptFUSION\Sequence\FiniteSequence;

/**
 * Provides a sequence of byte unit symbols from least to most significant.
 */
class ByteUnitSymbolSequence extends FiniteSequence implements BaseAware {
    protected static $prefixes = 'KMGTPEZY';

    const
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

    public function getSequence() {
        yield $this->suffix !== self::SUFFIX_NONE || $this->alwaysShowUnit ? 'B' : '';

        for ($i = 0; $i < strlen(static::$prefixes); ++$i)
            yield static::$prefixes[$i] . $this->suffix;
    }

    public function getSequenceIndex($index) {
        $sequence = $this->getSequenceArray();

        return $sequence[min($index, count($sequence) - 1)];
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