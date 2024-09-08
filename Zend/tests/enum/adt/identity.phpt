--TEST--
ADT identity
--FILE--
<?php

enum E {
    case A;
    case B($x);
    case C($x);
}

var_dump(E::A === E::A);
var_dump(E::A === E::B(1));
var_dump(E::B(1) === E::B(1));
var_dump(E::B(1) === E::B(2));
var_dump(E::B(1) === E::C(1));
var_dump(E::B(1) === E::C(2));

?>
--EXPECT--
bool(true)
bool(false)
bool(true)
bool(false)
bool(false)
bool(false)
