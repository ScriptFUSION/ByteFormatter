<?php
namespace ScriptFUSION\Byte;

use ScriptFUSION\Sequence\FiniteSequence;

/**
 * Formats byte values as human-readable strings.
 */
class ByteFormatter implements BaseAware {
    protected
        $base,
        $format,
        $precision,
        $automaticUnitSwitching = true
    ;

    private
        $normalizedFormat,
        $unitSequence
    ;

    public function __construct($precision = 0, $base = Base::BINARY, $format = '%v %u') {
        $this->setPrecision($precision)->setBase($base)->setFormat($format);
    }

    public function format($bytes) {
        $log = log($bytes, $this->base);
        $scale = max(0, $log|0);
        $value = round(pow($this->base, $log - $scale), $this->precision);
        $units = $this->getUnitSequence()->getSequenceIndex($scale);

        return sprintf($this->normalizedFormat, $value, $units);
    }

    private function normalizeFormat($format) {
        return str_replace(['%v', '%u'], ['%1$s', '%2$s'], $format);
    }

    /**
     * Notifies the unit sequence of base changes.
     *
     * @param int $base Numeric base.
     */
    private function notifyBaseChanged($base) {
        $this->automaticUnitSwitching
            && ($seq = $this->getUnitSequence()) instanceof BaseAware
            && $seq->setBase($base);
    }

    public function getBase() {
        return $this->base;
    }
    public function setBase($base) {
        $this->notifyBaseChanged($this->base = +$base);

        return $this;
    }

    public function getFormat() {
        return $this->format;
    }
    public function setFormat($format) {
        $this->normalizedFormat = $this->normalizeFormat($this->format = "$format");

        return $this;
    }

    public function getPrecision() {
        return $this->precision;
    }
    public function setPrecision($precision) {
        $this->precision = $precision|0;

        return $this;
    }

    public function getUnitSequence() {
        return $this->unitSequence ?: $this->unitSequence = new ByteUnitSymbolSequence;
    }
    public function setUnitSequence(FiniteSequence $sequence) {
        $this->unitSequence = $sequence;

        //Notify new sequence of current base.
        $this->notifyBaseChanged($this->base);

        return $this;
    }

    /**
     * Sets the unit symbol suffix to the specified value.
     * This is a shortcut method for interfacing with the default unit sequence generator and cannot be used if the
     * default generator has been replaced with a non-derived object.
     *
     * @param string $suffix Unit symbol suffix. Defaults to no suffix.
     * @return $this
     * @throws \BadFunctionCallException when unit sequence is not an instance of ByteUnitSymbolSequence.
     */
    public function setUnitSymbolSuffix($suffix = ByteUnitSymbolSequence::SUFFIX_NONE) {
        if (!($seq = $this->getUnitSequence()) instanceof ByteUnitSymbolSequence)
            throw new \BadFunctionCallException(
                'Cannot set suffix: unit sequence not instance of ByteUnitSymbolSequence.'
            );

        $seq->setSuffix($suffix);

        return $this;
    }

    /**
     * Enables or disables automatic unit switching that may occur when the base value is changed.
     *
     * @param bool $disable true to disable automatic unit switching, otherwise false.
     * @return $this
     */
    public function disableAutomaticUnitSwitching($disable = true) {
        $this->automaticUnitSwitching = !$disable;

        return $this;
    }
}