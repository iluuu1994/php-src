--TEST--
Mismatched enum scalar type
--FILE--
<?php

enum Foo: int {
    case Bar = 'bar';
}

?>
--EXPECTF--
Fatal error: Enum case type string does not match enum scalar type int in %s on line %d
