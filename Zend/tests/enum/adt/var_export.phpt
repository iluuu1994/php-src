--TEST--
Basic ADT
--FILE--
<?php

enum E {
    case A;
    case B($x);
    case C(mixed $x);
    case D(int $x, string $y);
}

var_export(E::A);
echo "\n";
var_export(E::B(42));
echo "\n";
var_export(E::C('foo'));
echo "\n";
var_export(E::D(43, 'bar'));

?>
--EXPECT--
\E::A
\E::B(42)
\E::C('foo')
\E::D(43, 'bar')
