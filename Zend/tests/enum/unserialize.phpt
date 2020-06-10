--TEST--
Enum unserialize
--FILE--
<?php

enum Foo {
    case Bar;
}

var_dump(unserialize('E:7:"Foo:Bar";'));

?>
--EXPECT--
enum(Foo::Bar)
