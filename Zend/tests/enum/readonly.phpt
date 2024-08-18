--TEST--
Readonly enum
--FILE--
<?php

readonly enum Foo {}

?>
--EXPECTF--
Fatal error: Enum Foo must not be readonly in %s on line %d
