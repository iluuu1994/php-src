--TEST--
ADT assoc value must not be pass-by-reference
--FILE--
<?php

enum E {
    case A(public &$x);
}

?>
--EXPECTF--
Fatal error: Associated value $x of enum case E::A must not be promoted in %s on line %d
