--TEST--
Abstract enum
--FILE--
<?php

abstract enum Foo {}

?>
--EXPECTF--
Fatal error: Enum Foo must not be abstract in %s on line %d
