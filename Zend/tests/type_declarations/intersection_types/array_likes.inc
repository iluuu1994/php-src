<?php
class ArrayLike implements ArrayAccess, Iterator
{
    public function offsetExists($offset) { }
    public function offsetGet($offset) { }
    public function offsetSet($offset, $value) { }
    public function offsetUnset($offset) { }
    public function current() { }
    public function next() { }
    public function key() { }
    public function valid() { }
    public function rewind() { }
}
class ArrayIter implements Iterator
{
    public function current() { }
    public function next() { }
    public function key() { }
    public function valid() { }
    public function rewind() { }
}
class ArrayAccessable implements ArrayAccess
{
    public function offsetExists($offset) { }
    public function offsetGet($offset) { }
    public function offsetSet($offset, $value) { }
    public function offsetUnset($offset) { }
}
