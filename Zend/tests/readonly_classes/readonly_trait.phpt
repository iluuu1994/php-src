--TEST--
Traits cannot be readonly
--FILE--
<?php

readonly trait Foo
{
}

?>
--EXPECTF--
Fatal error: Trait Foo must not be readonly in %s on line %d
