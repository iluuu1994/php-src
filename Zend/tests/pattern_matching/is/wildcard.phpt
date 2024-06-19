--TEST--
Wildcard pattern matching
--FILE--
<?php

class Foo {}

var_dump('' is *);
var_dump(42 is *);
var_dump(3.141 is *);
var_dump(null is *);
var_dump(true is *);
var_dump(new Foo() is *);
var_dump([1, 2, 3] is *);

?>
--EXPECT--
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
