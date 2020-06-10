--TEST--
Non-scalar enum errors when case has value
--FILE--
<?php

enum Foo {
    case Bar = 1;
}

?>
--EXPECTF--
Fatal error: Case Bar of non-scalar enum Foo must not have a value in %s on line %d
