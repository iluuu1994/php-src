--TEST--
Class cannot extend typealias
--FILE--
<?php

typealias Integer = int;

class Foo extends Integer {}

?>
--EXPECTF--
Fatal error: Class Foo may not inherit from final class (Integer) in %s on line %d
