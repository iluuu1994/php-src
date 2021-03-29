--TEST--
Typealias in parameter type
--FILE--
<?php

typealias Integer = int;
typealias FloatingPoint = float;
typealias Number = Integer|FloatingPoint;

function foo(Number $x) {
    var_dump($x);
}

function bar($x) {
    try {
        foo($x);
    } catch (TypeError $e) {
        echo $e->getMessage() . "\n";
    }
}

bar(42);
bar(3.141);
bar('foo');

?>
--EXPECTF--
int(42)
float(3.141)
foo(): Argument #1 ($x) must be of type int|float, string given, called in %s on line %d
