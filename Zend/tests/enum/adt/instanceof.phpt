--TEST--
ADT instanceof
--FILE--
<?php

enum Option {
    case None();
    case Some($value);
}

var_dump(Option::None() instanceof Option::None);
var_dump(Option::None() instanceof Option::Some);
var_dump(Option::Some(42) instanceof Option::None);
var_dump(Option::Some(42) instanceof Option::Some);

?>
--EXPECT--
bool(true)
bool(false)
bool(false)
bool(true)
