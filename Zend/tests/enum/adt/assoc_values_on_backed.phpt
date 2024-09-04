--TEST--
Backed enum must not have assoc values
--FILE--
<?php

enum E: int {
    case A($value) = 42;
}

?>
--EXPECTF--
Fatal error: Case A of backed enum E must not have associated values in %s on line %d
