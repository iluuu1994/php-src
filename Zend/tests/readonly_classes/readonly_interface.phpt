--TEST--
Interfaces cannot be readonly
--FILE--
<?php

readonly interface Foo
{
}

?>
--EXPECTF--
Fatal error: Interface Foo must not be readonly in %s on line %d
