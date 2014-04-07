<?php
namespace ScriptFUSION\Sequence;

/**
 * Provides a sequence of values.
 */
interface Sequence {
    /** @return \Generator Sequence generator. */
    function getSequence();
}