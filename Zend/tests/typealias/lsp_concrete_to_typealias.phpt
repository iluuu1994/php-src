--TEST--
Concrete type can be substituted with typealias
--FILE--
<?php

typealias Integer = int;

class Foo {
    function baz(): ?int {
        return 42;
    }
}

class Bar extends Foo {
    function baz(): ?Integer {
        return null;
    }
}

$foo = new Foo();
var_dump($foo->baz());

$bar = new Bar();
var_dump($bar->baz());

?>
--EXPECT--
int(42)
NULL
