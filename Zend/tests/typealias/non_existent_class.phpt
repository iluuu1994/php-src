--TEST--
Typealias pointing to non-existent class
--FILE--
<?php

typealias Foo = Bar;

function takesFoo(Foo $foo) {}

takesFoo(10);

?>
--EXPECTF--
Fatal error: Uncaught TypeError: takesFoo(): Argument #1 ($foo) must be of type Bar, int given, called in %s
Stack trace:
#0 %s(%d): takesFoo(%d)
#1 {main}
  thrown in %s on line %d
