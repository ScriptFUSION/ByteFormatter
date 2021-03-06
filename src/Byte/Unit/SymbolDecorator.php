<?php
namespace ScriptFUSION\Byte\Unit;

use ScriptFUSION\Byte\Base;

/**
 * Decorates byte values with unit symbols.
 */
class SymbolDecorator implements UnitDecorator
{
    const PREFIXES = 'KMGTPEZY';
    const SUFFIX_NONE = '';
    const SUFFIX_METRIC = 'B';
    const SUFFIX_IEC = 'iB';

    private $prefixes = self::PREFIXES;
    private $suffix;
    private $alwaysShowUnit;

    public function __construct($suffix = null)
    {
        $this->setSuffix($suffix);
    }

    public function decorate(int $exponent, int $base, float $value): string
    {
        if (($suffix = $this->suffix) === null) {
            switch ($base) {
                case Base::BINARY:
                    $suffix = static::SUFFIX_IEC;
                    break;

                case Base::DECIMAL:
                    $suffix = static::SUFFIX_METRIC;
                    break;
            }
        }

        if (!$exponent) {
            return $suffix !== static::SUFFIX_NONE || $this->alwaysShowUnit ? 'B' : '';
        }

        return $this->prefixes[min($exponent, strlen($this->prefixes)) - 1] . $suffix;
    }

    public function getPrefixes(): string
    {
        return $this->prefixes;
    }

    public function setPrefixes($prefixes): SymbolDecorator
    {
        $this->prefixes = (string)$prefixes;

        return $this;
    }

    public function getSuffix()
    {
        return $this->suffix;
    }

    /**
     * @param string|null $suffix
     *
     * @return $this
     */
    public function setSuffix(?string $suffix): self
    {
        $this->suffix = $suffix;

        return $this;
    }

    public function alwaysShowUnit($show = true): SymbolDecorator
    {
        $this->alwaysShowUnit = (bool)$show;

        return $this;
    }
}
