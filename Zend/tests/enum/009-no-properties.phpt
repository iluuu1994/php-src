--TEST--
Enum disallows properties
--FILE--
<?php

enum Foo {
    public $bar;
}

?>
--EXPECTF--
Fatal error: Enums may not include member variables in %s on line %d
