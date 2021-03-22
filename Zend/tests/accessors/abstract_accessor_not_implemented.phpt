--TEST--
Abstract accessors not implemented
--FILE--
<?php

abstract class A {
    public $prop {
        abstract get;
        set { echo __METHOD__, "()\n"; }
    }
}

class B extends A {}

?>
--EXPECTF--
Fatal error: Class B contains 1 abstract method and must therefore be declared abstract or implement the remaining methods (A::$prop::get) in %s on line %d
