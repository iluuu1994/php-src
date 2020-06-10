--TEST--
Auto implement ScalarEnum interface
--FILE--
<?php

enum Foo {
    case Bar;
}

enum Baz: int {
    case Qux = 0;
}

var_dump(Foo::Bar instanceof ScalarEnum);
var_dump(Baz::Qux instanceof ScalarEnum);

?>
--EXPECT--
bool(false)
bool(true)
