--TEST--
Enum disallows static properties
--FILE--
<?php

enum Foo {
    public static $bar;
}

?>
--EXPECTF--
Fatal error: Enums may not include member variables in %s on line %d
