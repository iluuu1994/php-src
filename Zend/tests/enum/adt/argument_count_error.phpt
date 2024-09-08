--TEST--
ADT argument count error
--FILE--
<?php

enum E {
    case C($x, $y);
}

function test($c) {
    try {
        var_dump($c());
    } catch (Error $e) {
        echo get_class($e), ': ', $e->getMessage(), "\n";
    }
}

test(fn() => E::C());
test(fn() => E::C(1));
test(fn() => E::C(1, 2));
test(fn() => E::C(1, 2, 3));
test(fn() => E::C(x: 1));
test(fn() => E::C(y: 2));
test(fn() => E::C(x: 1, y: 2));
test(fn() => E::C(y: 2, x: 1));
test(fn() => E::C(1, 2, z: 3));

?>
--EXPECT--
ArgumentCountError: E::C() expects exactly 2 arguments, 0 given
ArgumentCountError: E::C() expects exactly 2 arguments, 1 given
enum(E::C) (2) {
  ["x"]=>
  int(1)
  ["y"]=>
  int(2)
}
ArgumentCountError: E::C() expects exactly 2 arguments, 3 given
ArgumentCountError: E::C() expects exactly 2 arguments, 1 given
ArgumentCountError: E::C(): Argument #1 ($x) not passed
enum(E::C) (2) {
  ["x"]=>
  int(1)
  ["y"]=>
  int(2)
}
enum(E::C) (2) {
  ["x"]=>
  int(1)
  ["y"]=>
  int(2)
}
Error: Unknown named parameter $z
