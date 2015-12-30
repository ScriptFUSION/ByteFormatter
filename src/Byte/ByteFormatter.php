<?php
namespace ScriptFUSION\Byte;

use ScriptFUSION\Byte\Unit\UnitDecorator;
use ScriptFUSION\Byte\Unit\SymbolDecorator;

/**
 * Formats byte values as human-readable strings.
 */
class ByteFormatter
{
    const DEFAULT_BASE = Base::BINARY;

    /** @var int */
    private $base = self::DEFAULT_BASE;

    /** @var string */
    private $format;

    /** @var int */
    private $precision = 0;

    /** @var string */
    private $normalizedFormat;

    /** @var UnitDecorator */
    private $unitDecorator;

    public function __construct(UnitDecorator $unitDecorator = null)
    {
        $this
            ->setUnitDecorator($unitDecorator ?: new SymbolDecorator)
            ->setFormat('%v %u')
        ;
    }

    public function format($bytes, $precision = null)
    {
        $precision = $precision === null ? $this->precision : $precision;
        $log = log($bytes, $this->base);
        $exponent = max(0, $log|0);
        $value = round(pow($this->base, $log - $exponent), $precision);
        $units = $this->getUnitDecorator()->decorate($exponent, $this->base, $value);

        return trim(sprintf($this->normalizedFormat, $value, $units));
    }

    private function normalizeFormat($format)
    {
        return str_replace(['%v', '%u'], ['%1$s', '%2$s'], $format);
    }

    public function getBase()
    {
        return $this->base;
    }

    public function setBase($base)
    {
        $this->base = $base|0;

        return $this;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function setFormat($format)
    {
        $this->normalizedFormat = $this->normalizeFormat($this->format = "$format");

        return $this;
    }

    public function getPrecision()
    {
        return $this->precision;
    }

    public function setPrecision($precision)
    {
        $this->precision = $precision|0;

        return $this;
    }

    /**
     * @return UnitDecorator
     */
    public function getUnitDecorator()
    {
        return $this->unitDecorator;
    }

    public function setUnitDecorator(UnitDecorator $collection)
    {
        $this->unitDecorator = $collection;

        return $this;
    }
}
