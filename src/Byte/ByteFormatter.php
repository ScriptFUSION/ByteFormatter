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

    /** @var string */
    private $sprintfFormat;

    /** @var int */
    private $precision = 0;

    /** @var UnitDecorator */
    private $unitDecorator;

    /**
     * Initializes this instance, optionally with a specific unit decorator.
     * If no unit decorator is specified, SymbolDecorator will be used.
     *
     * @param UnitDecorator|null $unitDecorator Optional. Unit decorator.
     */
    public function __construct(UnitDecorator $unitDecorator = null)
    {
        $this
            ->setUnitDecorator($unitDecorator ?: new SymbolDecorator)
            ->setFormat('%v %u')
        ;
    }

    /**
     * Formats the specified number of bytes as a human-readable string.
     *
     * @param int $bytes Number of bytes.
     * @param int|null $precision Optional. Number of fractional digits.
     *
     * @return string Formatted bytes.
     */
    public function format($bytes, $precision = null)
    {
        // Use default precision when not specified.
        $precision === null && $precision = $this->precision;

        $log = log($bytes, $this->base);
        $exponent = max(0, $log|0);
        $value = round(pow($this->base, $log - $exponent), $precision);
        $units = $this->getUnitDecorator()->decorate($exponent, $this->base, $value);

        return trim(sprintf($this->sprintfFormat, $value, $units));
    }

    /**
     * Coverts a format specifier into a sprintf() compatible format.
     *
     * @param string $format Format specifier.
     *
     * @return string sprintf() format.
     */
    private function convertFormat($format)
    {
        return str_replace(['%v', '%u'], ['%1$s', '%2$s'], $format);
    }

    /**
     * Gets the exponentiation base.
     *
     * @return int Exponentiation base.
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     * Sets the exponentiation base which should usually be a Base constant.
     *
     * @param int $base Exponentiation base.
     *
     * @return $this
     */
    public function setBase($base)
    {
        $this->base = $base|0;

        return $this;
    }

    /**
     * Gets the format specifier.
     *
     * @return string Format specifier.
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Sets the format specifier. Occurrences of %v and %u will be replaced
     * with formatted byte values and units, respectively.
     *
     * @param string $format Format specifier.
     *
     * @return $this
     */
    public function setFormat($format)
    {
        $this->sprintfFormat = $this->convertFormat($this->format = "$format");

        return $this;
    }

    /**
     * Gets the maximum number of fractional digits.
     *
     * @return int Fractional digits.
     */
    public function getPrecision()
    {
        return $this->precision;
    }

    /**
     * Sets the maximum number of fractional digits.
     *
     * @param $precision
     *
     * @return $this
     */
    public function setPrecision($precision)
    {
        $this->precision = $precision|0;

        return $this;
    }

    /**
     * Gets the unit decorator.
     *
     * @return UnitDecorator
     */
    public function getUnitDecorator()
    {
        return $this->unitDecorator;
    }

    /**
     * Sets the unit decorator.
     *
     * @param UnitDecorator $decorator Unit decorator.
     *
     * @return $this
     */
    public function setUnitDecorator(UnitDecorator $decorator)
    {
        $this->unitDecorator = $decorator;

        return $this;
    }
}
