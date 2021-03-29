--TEST--
Typealias cannot be instantiated
--FILE--
<?php

typealias Integer = int;

new Integer();

?>
--EXPECTF--
Fatal error: Uncaught Error: Cannot instantiate abstract class Integer in %s:%d
Stack trace:
#0 {main}
  thrown in %s on line %d
