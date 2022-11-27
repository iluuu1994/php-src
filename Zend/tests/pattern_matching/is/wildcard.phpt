--TEST--
Wildcard pattern matching
--FILE--
<?php

class Foo {}

var_dump('' is _);
var_dump(42 is _);
var_dump(3.141 is _);
var_dump(null is _);
var_dump(true is _);
var_dump(new Foo() is _);
var_dump([1, 2, 3] is _);

?>
--EXPECT--
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
