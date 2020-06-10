--TEST--
Scalar enums reject duplicate int values
--FILE--
<?php

enum Foo: int {
    case Bar = 0;
    case Baz = 0;
}

?>
--EXPECTF--
Fatal error: Duplicate enum case value in %s on line %s
