--TEST--
ADT ctor must not contain variadics
--FILE--
<?php

enum E {
    case A(...$args);
}

?>
--EXPECTF--
Fatal error: Associated value $args of enum case E::A must not be variadic in %s on line %d
