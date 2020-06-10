--TEST--
Invalid enum scalar type
--FILE--
<?php

enum Foo: Bar {}

?>
--EXPECTF--
Fatal error: Enum scalar type must be int or string, Bar given in %s on line %d
