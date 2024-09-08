--TEST--
Basic ADT
--FILE--
<?php

enum E {
    case A;
    case B($x);
    case C(mixed $x);
    case D(string $x, int $y);
}

var_dump(E::A);

$b = E::B(42);
var_dump($b, $b->x);

$c = E::C('foo');
var_dump($c, $c->x);

$d = E::D('bar', 43);
var_dump($d, $d->x, $d->y);

?>
--EXPECT--
enum(E::A)
enum(E::B) (1) {
  ["x"]=>
  int(42)
}
int(42)
enum(E::C) (1) {
  ["x"]=>
  string(3) "foo"
}
string(3) "foo"
enum(E::D) (2) {
  ["x"]=>
  string(3) "bar"
  ["y"]=>
  int(43)
}
string(3) "bar"
int(43)
