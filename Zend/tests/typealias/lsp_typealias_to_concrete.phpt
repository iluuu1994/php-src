--TEST--
Typealias can be substituted with concrete types
--FILE--
<?php

typealias Integer = int;

class Foo {
    function baz(): Integer {
        return 42;
    }
}

class Bar extends Foo {
    function baz(): int {
        return 43;
    }
}

$bar = new Bar();
var_dump($bar->baz());

?>
--EXPECT--
int(43)
