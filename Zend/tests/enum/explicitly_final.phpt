--TEST--
Explicitly final enum
--FILE--
<?php

final enum Foo {}

?>
--EXPECTF--
Fatal error: Enum Foo must not be final in %s on line %d
