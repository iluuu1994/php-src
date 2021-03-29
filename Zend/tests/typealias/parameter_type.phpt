--TEST--
Mixed typealias in parameter type
--FILE--
<?php

typealias Integer = int;

function foo(Integer|float|null $x) {
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
foo(): Argument #1 ($x) must be of type int|float|null, string given, called in %s on line %d
