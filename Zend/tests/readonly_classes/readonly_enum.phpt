--TEST--
Enums cannot be readonly
--FILE--
<?php

readonly enum Foo
{
}

?>
--EXPECTF--
Fatal error: Enum Foo must not be readonly in %s on line %d
