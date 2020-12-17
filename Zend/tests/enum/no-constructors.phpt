--TEST--
Enum disallows constructor
--FILE--
<?php

enum Foo {
    public function __construct() {}
}

?>
--EXPECTF--
Fatal error: Enums cannot contain constructors in %s on line %d
