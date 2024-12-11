--TEST--
Private interface
--FILE--
<?php

private interface I {
    public function test();
}

class C implements I {}

?>
--EXPECTF--
Fatal error: Class C contains 1 abstract method and must therefore be declared abstract or implement the remaining method (I@%s::test) in %s on line %d
