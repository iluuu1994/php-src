--TEST--
Nested typealias in return type
--FILE--
<?php

typealias Integer = int;
typealias FloatingPoint = float;
typealias Number = Integer|FloatingPoint;

function foo($x): Number {
    return $x;
}

function bar($x) {
    try {
        return foo($x);
    } catch (TypeError $e) {
        return $e->getMessage();
    }
}

var_dump(bar(42));
var_dump(bar(3.141));
var_dump(bar('foo'));

?>
--EXPECT--
int(42)
float(3.141)
string(62) "foo(): Return value must be of type int|float, string returned"
