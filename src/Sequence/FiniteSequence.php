<?php
namespace ScriptFUSION\Sequence;

/**
 * Provides a sequence with a finite number of values.
 */
abstract class FiniteSequence implements Sequence {
    /** @param int $index */
    abstract function getSequenceIndex($index);

    public function getSequenceArray() {
        return iterator_to_array($this->getSequence());
    }
}