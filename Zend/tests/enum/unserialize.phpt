--TEST--
Enum unserialize
--FILE--
<?php

enum Foo {
    case Bar;
    case Quux;
}

var_dump(unserialize('E:7:"Foo:Bar";'));
var_dump(unserialize('E:8:"Foo:Quux";'));

?>
--EXPECT--
enum(Foo::Bar)
enum(Foo::Quux)
