--TEST--
Scalar enums type can't be union
--FILE--
<?php

enum Foo: int|string {}

?>
--EXPECTF--
Fatal error: Enum scalar type must be int or string, string|int given in %s on line %d
