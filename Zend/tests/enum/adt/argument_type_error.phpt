--TEST--
ADT argument type error
--FILE--
<?php

enum E {
    case C(int $x, int $y);
}

function test($c) {
    try {
        var_dump($c());
    } catch (Error $e) {
        echo get_class($e), ': ', $e->getMessage(), "\n";
    }
}

test(fn() => E::C(1, 2));

test(fn() => E::C('1', 2));
test(fn() => E::C('1.1', 2));
test(fn() => E::C([], []));

test(fn() => E::C(1, '2'));
test(fn() => E::C(1, '2.2'));
test(fn() => E::C(1, []));

?>
--EXPECTF--
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

Deprecated: Implicit conversion from float-string "1.1" to int loses precision in %s on line %d
enum(E::C) (2) {
  ["x"]=>
  int(1)
  ["y"]=>
  int(2)
}
TypeError: E::C(): Argument #1 ($x) must be of type int, array given, called in %s on line %d
enum(E::C) (2) {
  ["x"]=>
  int(1)
  ["y"]=>
  int(2)
}

Deprecated: Implicit conversion from float-string "2.2" to int loses precision in %s on line %d
enum(E::C) (2) {
  ["x"]=>
  int(1)
  ["y"]=>
  int(2)
}
TypeError: E::C(): Argument #2 ($y) must be of type int, array given, called in %s on line %d
