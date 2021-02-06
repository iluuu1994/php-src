--TEST--
Int scalar enums with case without value
--FILE--
<?php

enum Foo: int {
    case Bar;
}

var_dump(Foo::Bar->value);

?>
--EXPECTF--
Fatal error: Case Bar of scalar enum Foo must have a value in %s on line %d
