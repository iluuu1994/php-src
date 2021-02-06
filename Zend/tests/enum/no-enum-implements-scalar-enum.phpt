--TEST--
Enum cannot manually implement ScalarEnum
--FILE--
<?php

enum Foo: int implements ScalarEnum {}

?>
--EXPECTF--
Fatal error: Class Foo cannot implement previously implemented interface ScalarEnum in %s on line %d
