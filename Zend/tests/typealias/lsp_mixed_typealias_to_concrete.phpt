--TEST--
Mixed typealias can be substituted with concrete types
--FILE--
<?php

typealias Integer = int;

class Foo {
    function baz(): ?Integer {
        return 42;
    }
}

class Bar extends Foo {
    function baz(): ?int {
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
