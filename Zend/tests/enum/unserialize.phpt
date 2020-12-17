--TEST--
Enum unserialize
--FILE--
<?php

enum Foo {
    case Bar;
}

var_dump(unserialize('E:8:"Foo::Bar";'));

?>
--EXPECT--
enum(Foo::Bar)
