<?php
namespace ScriptFUSION\Byte;

use ScriptFUSION\Byte\Unit\SymbolDecorator;
use ScriptFUSION\Byte\Unit\UnitDecorator;

/**
 * Formats byte values as human-readable strings.
 */
class ByteFormatter
{
    private int $base = Base::BINARY;

    private string $format;

    private string $sprintfFormat;

    private int $precision = 0;

    private bool $automaticPrecision = true;

    private ?int $exponent = null;

    private UnitDecorator $unitDecorator;

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
     * @param int|float $bytes Number of bytes.
     * @param int|null $precision Optional. Number of fractional digits.
     *
     * @return string Formatted bytes.
     */
    public function format(int|float $bytes, int $precision = null): string
    {
        // Use default precision when not specified.
        $precision === null && $precision = $this->getPrecision();

        $log = log($bytes, $this->getBase());
        $exponent = $this->hasFixedExponent() ? $this->getFixedExponent() : max(0, (int)$log);
        $value = round($this->getBase() ** ($log - $exponent), $precision);
        $units = $this->getUnitDecorator()->decorate($exponent, $this->getBase(), $value);

        return trim(sprintf($this->sprintfFormat, $this->formatValue($value, $precision), $units));
    }

    /**
     * Formats the specified number with the specified precision.
     *
     * If precision scaling is enabled the precision may be reduced when it
     * contains insignificant digits. If the fractional part is zero it will
     * be completely removed.
     *
     * @param float $value Number.
     * @param int $precision Number of fractional digits.
     *
     * @return string Formatted number.
     */
    private function formatValue(float $value, int $precision): string
    {
        $formatted = sprintf("%0.{$precision}F", $value);

        if ($this->hasAutomaticPrecision()) {
            // [0 => integer part, 1 => fractional part].
            $formattedParts = explode('.', $formatted);

            if (isset($formattedParts[1])) {
                // Strip trailing 0s in fractional part.
                if (!$formattedParts[1] = rtrim($formattedParts[1], '0')) {
                    // Remove fractional part.
                    unset($formattedParts[1]);
                }

                $formatted = implode('.', $formattedParts);
            }
        }

        return $formatted;
    }

    /**
     * Coverts a format specifier into a sprintf() compatible format.
     *
     * @param string $format Format specifier.
     *
     * @return string sprintf() format.
     */
    private function convertFormat(string $format): string
    {
        return str_replace(['%v', '%u'], ['%1$s', '%2$s'], $format);
    }

    /**
     * Gets the exponentiation base.
     *
     * @return int Exponentiation base.
     */
    public function getBase(): int
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
    public function setBase(int $base): self
    {
        $this->base = $base;

        return $this;
    }

    /**
     * Gets the format specifier.
     *
     * @return string Format specifier.
     */
    public function getFormat(): string
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
    public function setFormat(string $format): self
    {
        $this->sprintfFormat = $this->convertFormat($this->format = $format);

        return $this;
    }

    /**
     * Gets the maximum number of fractional digits.
     *
     * @return int Fractional digits.
     */
    public function getPrecision(): int
    {
        return $this->precision;
    }

    /**
     * Sets the maximum number of fractional digits.
     *
     * @param int $precision
     *
     * @return $this
     */
    public function setPrecision(int $precision): self
    {
        $this->precision = $precision;

        return $this;
    }

    /**
     * Enables automatic precision scaling.
     *
     * @return $this
     */
    public function enableAutomaticPrecision(): self
    {
        $this->automaticPrecision = true;

        return $this;
    }

    /**
     * Disables automatic precision scaling.
     *
     * @return $this
     */
    public function disableAutomaticPrecision(): self
    {
        $this->automaticPrecision = false;

        return $this;
    }

    /**
     * Gets a value indicating whether precision will be scaled automatically.
     *
     * @return bool True if precision will be scaled automatically, otherwise
     *     false.
     */
    public function hasAutomaticPrecision(): bool
    {
        return $this->automaticPrecision;
    }

    /**
     * Gets the fixed exponent.
     *
     * @return int Fixed exponent.
     */
    public function getFixedExponent(): int
    {
        return $this->exponent;
    }

    /**
     * Sets the fixed exponent.
     *
     * @param int $exponent Fixed exponent.
     *
     * @return $this
     */
    public function setFixedExponent(int $exponent): self
    {
        $this->exponent = $exponent;

        return $this;
    }

    /**
     * Clears any fixed exponent.
     *
     * @return $this
     */
    public function clearFixedExponent(): self
    {
        $this->exponent = null;

        return $this;
    }

    /**
     * Gets a value indicating whether a fixed exponent has been set.
     *
     * @return bool True if a fixed exponent has been set, otherwise false.
     */
    public function hasFixedExponent(): bool
    {
        return $this->exponent !== null;
    }

    /**
     * Gets the unit decorator.
     *
     * @return UnitDecorator
     */
    public function getUnitDecorator(): UnitDecorator
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
    public function setUnitDecorator(UnitDecorator $decorator): self
    {
        $this->unitDecorator = $decorator;

        return $this;
    }
}
