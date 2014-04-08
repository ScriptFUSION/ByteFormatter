<?php
namespace ScriptFUSION\Byte;

/**
 * Provides a collection of byte units.
 */
abstract class ByteUnitCollection implements \Countable, \ArrayAccess, \IteratorAggregate {
    public function count() {
        return 9;
    }

    public function offsetExists($offset) {
        return $offset > 0 && $offset < count($this);
    }

    public function offsetSet($offset, $value) {
        throw new \BadFunctionCallException('Collection is read-only.');
    }

    public function offsetUnset($offset) {
        throw new \BadFunctionCallException('Collection is read-only.');
    }

    public function getIterator() {
        for ($i = 0; $i < count($this); ++$i)
            yield $this[$i];
    }
}