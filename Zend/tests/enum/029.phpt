--TEST--
Invalid enum primitive type
--FILE--
<?php

enum Foo: Bar {}

?>
--EXPECTF--
Fatal error: Enum primitive type must be int or string in %s on line %d
