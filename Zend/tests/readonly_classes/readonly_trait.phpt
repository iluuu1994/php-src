--TEST--
Traits cannot be readonly
--FILE--
<?php

readonly trait Foo
{
}

?>
--EXPECTF--
Fatal error: Cannot use the readonly modifier on a function in %s on line %d
