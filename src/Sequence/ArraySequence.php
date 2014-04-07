<?php
namespace ScriptFUSION\Sequence;

/**
 * Provides an array of values as a Sequence.
 */
class ArraySequence extends FiniteSequence {
    protected $sequence = [];

    /** @return \Generator Sequence generator. */
    public function getSequence() {
        foreach ($this->sequence as $k => $v)
            yield $k => $v;
    }

    /** @param int $index */
    public function getSequenceIndex($index) {
        return $this->sequence[$index];
    }

    public function getSequenceArray() {
        return $this->sequence;
    }
}