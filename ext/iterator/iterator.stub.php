<?php

/** @generate-class-entries */

namespace Iterator {
    function range(int $from, int $to): RangeIterator {}

    final class RangeIterator implements \Iterator {
        public readonly int $from;
        public readonly int $to;
        public /*private(set)*/ int $key;
        public /*private(set)*/ int $current;

        private function __construct() {}
        public function current(): mixed {}
        public function next(): void {}
        public function key(): mixed {}
        public function valid(): bool {}
        public function rewind(): void {}
    }
}
