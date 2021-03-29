--TEST--
Typealias can be substituted with different typealias
--FILE--
<?php

typealias Integer = int;
typealias Integer2 = int;

class Foo {
    function baz(): Integer {
        return 42;
    }
}

class Bar extends Foo {
    function baz(): Integer2 {
        return 43;
    }
}

$bar = new Bar();
var_dump($bar->baz());

?>
--EXPECT--
int(43)
