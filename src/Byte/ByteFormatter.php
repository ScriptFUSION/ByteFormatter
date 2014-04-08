<?php
namespace ScriptFUSION\Byte;

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
        $unitCollection
    ;

    public function __construct($precision = 0, $base = Base::BINARY, $format = '%v %u') {
        $this->setPrecision($precision)->setBase($base)->setFormat($format);
    }

    public function format($bytes) {
        $log = log($bytes, $this->base);
        $scale = max(0, $log|0);
        $value = round(pow($this->base, $log - $scale), $this->precision);
        $units = $this->getUnits($value, $scale);

        return sprintf($this->normalizedFormat, $value, $units);
    }

    protected function getUnits($value, $scale) {
        $collection = $this->getUnitCollection();
        $collection instanceof ValueAware && $collection->setValue($value);

        return $collection[$scale];
    }

    private function normalizeFormat($format) {
        return str_replace(['%v', '%u'], ['%1$s', '%2$s'], $format);
    }

    /**
     * Notifies the unit collection of base changes.
     *
     * @param int $base Numeric base.
     */
    private function notifyBaseChanged($base) {
        $this->automaticUnitSwitching
            && ($collection = $this->getUnitCollection()) instanceof BaseAware
            && $collection->setBase($base);
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

    public function getUnitCollection() {
        return $this->unitCollection ?: $this->unitCollection = new ByteUnitSymbolCollection;
    }
    public function setUnitCollection(ByteUnitCollection $collection) {
        $this->unitCollection = $collection;

        //Notify new collection of current base.
        $this->notifyBaseChanged($this->base);

        return $this;
    }

    /**
     * Sets the unit symbol suffix to the specified value.
     * This is a shortcut method for interfacing with the default unit collection and cannot be used if the default
     * collection has been replaced with a non-derived object.
     *
     * @param string $suffix Unit symbol suffix. Defaults to no suffix.
     * @return $this
     * @throws \BadFunctionCallException when unit collection is not an instance of ByteUnitSymbolCollection.
     */
    public function setUnitSymbolSuffix($suffix = ByteUnitSymbolCollection::SUFFIX_NONE) {
        if (!($collection = $this->getUnitCollection()) instanceof ByteUnitSymbolCollection)
            throw new \BadFunctionCallException(
                'Cannot set suffix: unit collection not instance of ByteUnitSymbolCollection.'
            );

        $collection->setSuffix($suffix);

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