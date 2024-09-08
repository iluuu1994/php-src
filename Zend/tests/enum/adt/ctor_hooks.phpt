--TEST--
ADT assoc value must not contain hooks
--FILE--
<?php

enum E {
    case A($x { get => 42; });
}

?>
--EXPECTF--
Fatal error: Associated value $x of enum case E::A must not be promoted in %s on line %d
