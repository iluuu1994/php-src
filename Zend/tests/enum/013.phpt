--TEST--
Enum disallows constants
--FILE--
<?php

enum Foo {
    const BAR = 'Bar';
}

?>
--EXPECTF--
Fatal error: Enums cannot have constants in %s on line %d
