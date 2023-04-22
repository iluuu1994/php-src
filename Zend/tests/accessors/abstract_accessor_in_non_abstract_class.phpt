--TEST--
Abstract accessors in non-abstract class
--FILE--
<?php

class Test {
    public $prop {
        abstract get;
        set {}
    }
}

?>
--EXPECTF--
Fatal error: Class Test contains 1 abstract method and must therefore be declared abstract or implement the remaining methods (Test::$prop::get) in %s on line %d
